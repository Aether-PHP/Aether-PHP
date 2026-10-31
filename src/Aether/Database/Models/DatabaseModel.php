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
 *                  The divine secure, lightweight PHP framework
 *                   < 1 Mo • Zero dependencies • Pure PHP 8.3+
 *
 *  Built from scratch. No bloat. OOP Embedded.
 *
 *  @author: dawnl3ss (Alex') ©2026 — All rights reserved
 *  Source available • Commercial license required for redistribution
 *  → https://aetherphp.net
 *  → https://github.com/Aether-PHP/Aether-PHP
 *
*/
declare(strict_types=1);

namespace Aether\Database\Models;

use Aether\Database\QueryBuilder;


abstract class DatabaseModel {

    /** @var ModelTemplate $_template */
    private ModelTemplate $_template;

    /** @var array $_data */
    private array $_data = [];


    public function __construct(ModelTemplate $_template){
        $this->_template = $_template;
        $this->_build();
    }

    /**
     * @param callable $_func
     * - must be like : func_name($row instanceof ModelRow) : bool {}
     *
     * @return bool
     */
    public function _map(callable $_func) : bool {
        if (!$this->_hasBeenSent())
            return false;

        foreach ($this->_data as $row){
            $exc = $_func($row);

            if (!is_null($exc) && !$exc)
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
        $data = $this->_template->_template()->_send();

        if (!is_array($data) || count($data) < 1)
            return;

        $rows = [];

        foreach ($data as $row){
            $std = new ModelRow();

            foreach ($row as $key => $value){
                $std->{$key} = $value;
            }
            array_push($rows, $std);
        }
        $this->_data = $rows;
    }
}