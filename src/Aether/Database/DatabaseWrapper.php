<?php

/*
 *
 *      █████╗ ███████╗████████╗██╗  ██╗███████╗██████╗         ██████╗ ██╗  ██╗██████╗
 *     ██╔══██╗██╔════╝╚══██╔══╝██║  ██║██╔════╝██╔══██╗        ██╔══██╗██║  ██║██╔══██╗
 *     ███████║█████╗     ██║   ███████║█████╗  ██████╔╝ █████╗ ██████╔╝███████║██████╔╝
 *     ██╔══██║██╔══╝     ██║   ██╔══██║██╔══╝  ██╔══██╗ ╚════╝ ██╔═══╝ ██╔══██║██╔═══╝
 *     ██║  ██║███████╗   ██║   ██║  ██║███████╗██║  ██║        ██║     ██║  ██║██║
 *     ╚═╝  ╚═╝╚══════╝   ╚═╝   ╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝        ╚═╝     ╚═╝  ╚═╝╚═╝
 *
 *                      The divine lightweight PHP framework
 *                  < 1 Mo • Zero dependencies • Pure PHP 8.3+
 *
 *  Built from scratch. No bloat. POO Embedded.
 *
 *  @author: dawnl3ss (Alex') ©2025 — All rights reserved
 *  Source available • Commercial license required for redistribution
 *  → github.com/dawnl3ss/Aether-PHP
 *
*/
declare(strict_types=1);

namespace Aether\Database;

use Aether\Database\Drivers\DatabaseDriverEnum;


class DatabaseWrapper extends QueryBuilder {


    public function __construct(string $_database, DatabaseDriverEnum $_driver){
        parent::__construct($_database, $_driver->_get());
    }


    /**
     * @param string $query
     *
     * @return mixed
     */
    public function _raw(string $query){
        return $this->_driver->_query($query, []);
    }





    /**
     * Operate a SQL 'UPDATE' query
     *
     * @param string $table
     * @param array $assoc
     * @param array $conditions
     *
     * @return mixed
     */
    public function _update(string $table, array $assoc, array $conditions = []) : mixed {
        $table = $this->_driver->_escape($table);
        $setClauses = [];

        foreach ($assoc as $key => $value){
            $setClauses[] = "{$key} = :set_{$key}";
        }

        $query = "UPDATE {$table} SET " . implode(", ", $setClauses);
        $params = [];

        foreach ($assoc as $key => $value){
            $params["set_{$key}"] = $value;
        }

        if (!empty($conditions)){
            $whereClauses = [];

            foreach ($conditions as $key => $value){
                $whereClauses[] = "{$key} = :where_{$key}";
                $params["where_{$key}"] = $value;
            }

            $query .= " WHERE " . implode(" AND ", $whereClauses);
        }

        return $this->_driver->_query($query, $params);
    }


    /**
     * Check if a value is in a table
     *
     * @param $table
     * @param string $content
     * @param array $assoc
     *
     * @return bool
     */
    public function _exist($table, array $assoc = []){
        return $this->_select($table, '*', $assoc) != [];
    }


    /**
     * Operate a SQL 'SELECT' query with JOIN
     *
     * @param string $table
     * @param string $content
     * @param array $joins Format: [['table' => 'table_name', 'type' => 'INNER|LEFT|RIGHT', 'on' => 'table1.id = table2.id']]
     * @param array $assoc
     *
     * @return mixed
     */
    public function _join(string $table, string $content, array $joins = [], array $assoc = []) : mixed {
        $query = "SELECT {$content} FROM " . $this->_driver->_escape($table);

        if (!empty($joins)){
            foreach ($joins as $join){
                $joinType = strtoupper($join['type'] ?? 'INNER');
                $joinTable = $join['table'];
                $joinOn = $join['on'];

                $query .= " {$joinType} JOIN {$joinTable} ON {$joinOn}";
            }
        }

        if (!empty($assoc)){
            $conditions = [];

            foreach ($assoc as $key => $value){
                $conditions[] = "{$key} = :{$key}";
            }

            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        return $this->_driver->_query($query, $assoc);
    }

}