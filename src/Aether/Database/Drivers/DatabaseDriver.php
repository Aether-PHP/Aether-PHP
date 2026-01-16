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

namespace Aether\Database\Drivers;

use Aether\Config\ProjectConfig;
use Aether\Utils\IdsMap;


abstract class DatabaseDriver implements DatabaseConnectable {

    /** @var string $_database */
    protected string $_database;

    /** @var DatabaseDriverEnum $_driver */
    protected DatabaseDriverEnum $_driver;

    public function __construct(DatabaseDriverEnum $driver){
        $this->_driver = $driver;
    }

    /**
     * @param string $database
     *
     * @return DatabaseDriver
     */
    public function _database(string $database) : self {
        $this->_database = $database;
        return $this;
    }

    /**
     * @return IdsMap
     */
    protected function _getIds() : IdsMap {
        return new IdsMap(ProjectConfig::_get("DATABASE_USERNAME"), ProjectConfig::_get("DATABASE_PASSWORD"));
    }

    /**
     * @return string
     */
    protected function _getHost() : string {
        return ProjectConfig::_get("DATABASE_ADDRESS");
    }

    /**
     * @return mixed
     */
    abstract public function _connect() : DatabaseDriver;


    /**
     * @param string $query
     * @param array $params
     *
     * @return mixed
     */
    abstract public function _query(string $query, array $params) : mixed;

    /**
     * @param string $_string
     *
     * @return string
     */
    abstract public function _escape(string $_string) : string;


    /**
     * @return array
     */
    abstract public function _dump() : array;

}