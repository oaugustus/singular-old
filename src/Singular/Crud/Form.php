<?php

namespace Singular\Crud;

use Singular\Crud\Field;

/**
 * Class Form.
 *
 * @package Singular\Crud
 *
 * @author Otávio Fernandes <otavio@neton.com.br>
 */
class Form extends CrudService
{
    public function compile()
    {
        $fields = $this->getFields();
        $rows = $this->mountFields($fields);

        $this->writeTemplate($rows, $fields);
    }

    /**
     * Escreve o template do formulário.
     *
     * @param array $rows
     * @param array $fields
     */
    private function writeTemplate($rows, $fields)
    {
        $tpl = '';

        foreach ($rows as $row) {
            $tpl.= "<div class='row'>";

            foreach ($row as $field) {
                $tpl.= $fields[$field]->render();
            }

            $tpl.= '</div>';
        }

        echo $tpl;

    }

    /**
     * Organiza os campos em linhas no formulário.
     *
     * @param array $fields
     */
    private function mountFields($fields)
    {
        $rows = array();

        foreach ($fields as $name => $field) {
            $rows[$field->row][] = $name;
        }

        ksort($rows);

        return $rows;
    }

    /**
     * Recupera a definição de campos dos metadados da tabela.
     *
     * @return array
     */
    private function getFields()
    {
        $fields = array();

        $columns = $this->db->getSchemaManager()->listTableColumns($this->table);

        $count = 0;

        foreach ($columns as $column) {

            $count++;

            $field = array (
                'type' => $column->getType()->getName(),
                'name' => $column->getName(),
                'length' => $column->getLength(),
                'precision' => $column->getPrecision(),
                'scale' => $column->getScale(),
                'notnull' => $column->getNotnull(),
                'autoincrement' => $column->getAutoincrement(),
                'default' => $column->getDefault(),
                'order' => $count
            );

            if ($column->getComment() != '') {
                $field = array_merge($field, json_decode($column->getComment(),true));
            }

            $fields[$field['name']] = new Field($field);
        }

        return $fields;

    }
}