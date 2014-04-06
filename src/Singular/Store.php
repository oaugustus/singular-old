<?php

namespace Singular;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Singular\Application;

/**
 * Class Store.
 *
 * @package Singular
 *
 * @author  Otávio Fernandes <otavio@neton.com.br>
 */
class Store extends Service
{
    /**
     * Tabela origem do store.
     *
     * @var string
     */
    protected $table;

    /**
     * Campo utilizado como chave da tabela.
     *
     * @var string
     */
    protected $id = 'id';

    /**
     * Nome da conexão default.
     *
     * @var string
     */
    protected $connectionName = 'default';

    /**
     * @var Connection;
     */
    protected $qb;

    /**
     * @var
     */
    protected $db;

    /**
     * Inicializa o Store.
     *
     * @param Application $app
     */
    public function __construct(Application $app, $pack)
    {
        parent::__construct($app, $pack);

        $this->db = $this->getConnection();
        $this->qb = $this->db->createQueryBuilder();
    }

    /**
     * Seta em tempo de execução o nome da tabela a ser utilizada pelo store.
     *
     * @param String $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * Localiza um registro pelo seu id.
     *
     * @param Integer $id
     *
     * @return Array
     */
    public function find($id)
    {
        $qb = $this->qb;

        $qb->select('t.*')
            ->from($this->table, 't')
            ->where($this->id." = :id");

        return $this->db->fetchAssoc($qb->getSQL(), array(
            'id' => $id
        ));
    }

    /**
     * Localiza o primeiro registro que casa com um conjunto de filtros.
     *
     * @param Array $filters
     *
     * @return Array
     */
    public function findOneBy($filters)
    {
        $qb = $this->qb;

        $qb->select('t.*')
            ->from($this->table, 't')
            ->where('1 = 1');

        foreach ($filters as $key => $filter) {
            $filters[$key] = "%$filter%";
            $qb->andWhere('t.'.$key.' like :'.$key);
        }


        return $this->db->fetchAssoc($qb->getSQL(), $filters);
    }

    /**
     * Localiza os registros que casam com um conjunto de filtros.
     *
     * @param Array $filters
     * @param Array $opt
     *
     * @return Array
     */
    public function findBy($filters, $opt = array())
    {
        $qb = $this->qb;

        $qb->select('t.*')
            ->from($this->table, 't')
            ->where('1 = 1');

        foreach ($filters as $key => $filter) {
            $filters[$key] = "%$filter%";
            $qb->andWhere('t.'.$key.' like :'.$key);
        }

        return $this->paginate($qb, $opt, $filters);
    }

    /**
     * Salva um registro no banco de dados.
     *
     * @param Array $data
     *
     * @return Mixed
     */
    public function save($data)
    {
        $id = false;

        if (!isset($data[$this->id])){
            $data[$this->id] = 0;
        }

        if ($data[$this->id] == 0) {
            unset($data[$this->id]);

            try {
                $this->db->insert($this->table, $this->fromArray($data));

                $id = $this->db->lastInsertId($this->id);
            } catch (\Exception $e) {
            }

        } else {
            try {
                $this->db->update($this->table, $this->fromArray($data), array(
                    $this->id => $data[$this->id]
                ));

                $id = $data[$this->id];

            } catch (\Exception $e) {
            }
        }

        return $id;
    }

    /**
     * Exclui um registro da tabela.
     *
     * @param Integer $id
     *
     * @return Boolean
     */
    public function remove($id)
    {
        try {
            $this->db->delete($this->table, array(
                $this->id => $id
            ));

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Exclui um registro da tabela por uma condição composta.
     *
     * @param array $filter
     *
     * @return bool
     */
    public function removeBy(array $filter)
    {
        try {
            $this->db->delete($this->table, $filter);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Pagina o resultado de uma consulta.
     *
     * @param QueryBuilder $qb
     * @param Array        $opt
     * @param Array        $filters
     *
     * @return Array
     */
    protected function paginate(QueryBuilder $qb, $opt, $filters = array())
    {
        $db = $this->db;

        $total = count($db->fetchAll($qb->getSQL(), $filters));

        $qb->setFirstResult(isset($opt['start']) ? $opt['start'] : 0)->
            setMaxResults(isset($opt['limit']) ? $opt['limit'] : 200);

        $result = $db->fetchAll($qb->getSQL(), $filters);

        return array(
            'total' => $total,
            'results' => $result
        );
    }

    /**
     * Mapeia os dados recebidos de um array nos campos existentes na tabela.
     *
     * @param Array $source
     *
     * @return Array
     */
    protected function fromArray($source)
    {
        $data = array();
        $columnNames = $this->getColumnNames();

        foreach ($source as $key => $value) {

            if (in_array($key, $columnNames)) {

                if (is_string($value)) {
                    $value = ($value);
                }

                $data[$key] = $value;
            }
        }

        return $data;
    }

    /**
     * Aplica filtro baseado no GridFeature Filter.
     *
     * @param  Array  $filters
     * @return string
     */
    protected function getGridFilter($filters)
    {
        // GridFilters sends filters as an Array if not json encoded
        if (is_array($filters)) {
            $encoded = false;
        } else {
            $encoded = true;
            $filters = json_decode($filters);
        }

        $where = ' 0 = 0 ';
        $qs = '';

        // loop through filters sent by client
        if (is_array($filters)) {
            for ($i=0;$i<count($filters);$i++) {
                $filter = $filters[$i];

                // assign filter data (location depends if encoded or not)
                if ($encoded) {
                    $field = $filter->field;
                    $value = $filter->value;
                    $compare = isset($filter->comparison) ? $filter->comparison : null;
                    $filterType = $filter->type;
                } else {
                    $field = $filter['field'];
                    $value = $filter['value'];
                    $compare = isset($filter['comparison']) ? $filter['comparison'] : null;
                    $filterType = $filter['type'];
                }

                switch ($filterType) {
                    case 'string' : $qs .= " AND ".$field." LIKE '%".$value."%'"; Break;
                    case 'list' :
                        if (strstr($value,',')) {
                            $fi = explode(',',$value);
                            for ($q=0;$q<count($fi);$q++) {
                                $fi[$q] = "'".$fi[$q]."'";
                            }
                            $value = implode(',',$fi);
                            $qs .= " AND ".$field." IN (".$value.")";
                        } else {
                            $qs .= " AND ".$field." = '".$value."'";
                        }
                        Break;
                    case 'boolean' : $qs .= " AND ".$field." = ".($value); Break;
                    case 'numeric' :
                        switch ($compare) {
                            case 'eq' : $qs .= " AND ".$field." = ".$value; Break;
                            case 'lt' : $qs .= " AND ".$field." < ".$value; Break;
                            case 'gt' : $qs .= " AND ".$field." > ".$value; Break;
                        }
                        Break;
                    case 'date' :
                        switch ($compare) {
                            case 'eq' : $qs .= " AND ".$field." = '".date('Y-m-d',strtotime($value))."'"; Break;
                            case 'lt' : $qs .= " AND ".$field." < '".date('Y-m-d',strtotime($value))."'"; Break;
                            case 'gt' : $qs .= " AND ".$field." > '".date('Y-m-d',strtotime($value))."'"; Break;
                        }
                        Break;
                }
            }
            $where .= $qs;
        }

        return $where;
    }

    /**
     * Retorna os nomes das colunas da tabela.
     *
     * @return Array
     *
     * @return Array
     */
    private function getColumnNames()
    {
        $names = array();
        $columns = $this->db->getSchemaManager()->listTableColumns($this->table);

        foreach ($columns as $column) {
            $names[] = $column->getName();
        }

        return $names;
    }

    /**
     * Recupera a conexão utilizada pelo store.
     *
     * @return Connection
     */
    private function getConnection()
    {
        if ('default' == $this->connectionName) {
            return $this->app['db'];
        } else {
            return $this->app['dbs'][$this->connectionName];
        }
    }
}