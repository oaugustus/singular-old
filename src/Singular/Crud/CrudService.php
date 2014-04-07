<?php

namespace Singular\Crud;

use Doctrine\DBAL\Connection;
use Singular\Service;

/**
 * Class Form.
 *
 * @package Singular\Crud
 *
 * @author Otávio Fernandes <otavio@neton.com.br>
 */
abstract class CrudService extends Service
{
    /**
     * Referência à tabela no banco de dados.
     *
     * @var string
     */
    protected $table = '';

    /**
     * Referência à conexão de banco de dados.
     *
     * @var string
     */
    protected $conn = 'default';

    /**
     * Nome do serviço associado ao banco de dados.
     *
     * @var Connection $db
     */
    protected $db = null;

    /**
     * Seta em tempo de execução o nome da tabela a ser utilizada pelo store.
     *
     * @param String $table
     * @param String $conn
     */
    public function setTable($table, $conn = 'default')
    {
        $this->table = $table;
        $this->conn = $conn;
        $this->db = $this->getConnection();
    }

    /**
     * Recupera a conexão utilizada pelo store.
     *
     * @return Connection
     */
    private function getConnection()
    {
        if ('default' == $this->conn) {
            return $this->app['db'];
        } else {
            return $this->app['dbs'][$this->conn];
        }
    }

} 