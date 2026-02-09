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
 *  Built from scratch. No bloat. OOP Embedded.
 *
 *  @author: dawnl3ss (Alex') ©2026 — All rights reserved
 *  Source available • Commercial license required for redistribution
 *  → https://github.com/dawnl3ss/Aether-PHP
 *
*/
declare(strict_types=1);

namespace Aether\Database;

use Aether\Database\Drivers\DatabaseDriver;


abstract class QueryBuilder {

    /** @var string|null $_type */
    private ?string $_type = null;

    /** @var string $_database */
    private string $_database;

    /** @var string $_table */
    private string $_table;


    /** @var string|array $_rows */
    private string|array $_rows;

    /** @var array $_wheres */
    private array $_wheres = [];

    /** @var array $_inserts */
    private array $_inserts = [];


    /** @var DatabaseDriver $_driver */
    protected DatabaseDriver $_driver;

    public function __construct(string $_database, DatabaseDriver $_driver){
        $this->_database = $_database;
        $this->_driver = $_driver->_database($_database);
    }

    /**
     * @param string $_table
     *
     * @return self
     */
    public function _table(string $_table) : QueryBuilder {
        $this->_table = $_table;
        return $this;
    }

    /**
     * @param string|array ...$_rows
     *
     * @return self
     */
    public function _select(string|array ...$_rows) : QueryBuilder {
        if ($this->_type !== null)
            return $this;

        $this->_rows = is_array($_rows[0]) ? $_rows[0] : $_rows;
        $this->_type = "select";

        return $this;
    }

    /**
     * @param string $_key
     * @param mixed $_value
     *
     * @return self
     */
    public function _where(string $_key, mixed $_value) : QueryBuilder {
        if ($this->_type !== "select" && $this->_type !== "where")
            return $this;

        $this->_wheres[$_key] = $_value;
        return $this;
    }

    /**
     * @param string $_key
     * @param mixed $_value
     *
     * @return self
     */
    public function _insert(string $_key, mixed $_value) : QueryBuilder {
        if ($this->_type !== "insert" && !is_null($this->_type))
            return $this;

        $this->_type = "insert";
        $this->_inserts[$_key] = $_value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function _send() : mixed {
        # - SQL Select
        if ($this->_type === "select"){
            $query = "SELECT " . implode(", ", $this->_rows) . " FROM " . $this->_table;

            if ($this->_wheres !== []){
                $wheres = [];

                foreach ($this->_wheres as $key => $val){
                    $wheres[] = "{$key} = :{$key}";
                }

                $query .= " WHERE " . implode(" AND ", $wheres);
            }

            return $this->_driver->_query($query, $this->_wheres);
        }

        # - Insert SQL query
        else if ($this->_type === "insert"){
            $query = "INSERT INTO " . $this->_table . ' (';

            if ($this->_inserts === [])
                return $this;

            $query .= implode(',', array_keys($this->_inserts)) . ") VALUES (:" . implode(", :", array_keys($this->_inserts)) . ")";
            return $this->_driver->_query($query, $this->_inserts);
        }

        return null;
    }
}