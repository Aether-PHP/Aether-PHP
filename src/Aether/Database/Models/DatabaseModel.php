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
 *  → https://github.com/Aether-PHP/Aether-PHP
 *
*/
declare(strict_types=1);

namespace Aether\Database\Models;

use Aether\Database\QueryBuilder;


abstract class DatabaseModel extends \StdClass {

    /** @var QueryBuilder $_queryBuilder */
    private QueryBuilder $_queryBuilder;

    /** @var array $_data */
    private array $_data = [];


    public function __construct(QueryBuilder $_builder){
        $this->_queryBuilder = $_builder;
        $this->_build();
    }

    /**
     * @param callable $_func
     * - must be like : func_name($key, $value) : bool {}
     *
     * @return bool
     */
    public function _map(callable $_func) : bool {
        if (!$this->_hasBeenSent())
            return false;

        foreach ($this->_data as $k => $v){
            if (!$_func($k, $v))
                return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    private function _hasBeenSent() : bool { return !empty($this->_data); }


    /**
     * @return void
     */
    private function _build() : void {
        # - The goal here is to extract $this->_data into stdClass properties
        # -- mimic the table columns
        $data = $this->_queryBuilder->_send();

        if (!is_array($data) || count($data) < 1)
            return;

        foreach ($data[0] as $key => $value){
            $this->$key = $value;
        }
        $this->_data = $data;
    }
}