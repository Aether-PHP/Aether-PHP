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

final class ModelTemplate {

    /** @var string $_dbName */
    private string $_dbName;

    /** @var string $_table */
    private string $_table;

    /** @var string $_rows */
    private string $_rows;

    /** @var int $_count */
    private int $_count;

    /** @var array $_conditions */
    private array $_conditions;


    public function __construct(string $_dbName, string $_table){
        $this->_dbName = $_dbName;
        $this->_table = $_table;
        $this->_rows = '*';
        $this->_count = 1;
    }

    /**
     * @return QueryBuilder
     */
    public function _template() : QueryBuilder {
        $builder = Aether()->_db()->_mysql($this->_dbName)->_table($this->_table)->_select($this->_rows);

        foreach($this->_conditions as $cond){
            $builder->_where($cond[0], $cond[1]);
        }

        return $builder;
    }

}