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

namespace Aether\Service\Hub;

use Aether\Database\DatabaseWrapper;
use Aether\Database\Drivers\DatabaseDriverEnum;


final class DatabaseServiceHub {

    /** @var DatabaseWrapper[] $_databases */
    public array $_databases = [];

    /**
     * @param string $_dbname
     *
     * @return DatabaseWrapper
     */
    public function _mysql(string $_dbname) : DatabaseWrapper {
        return $this->_createConn($_dbname, DatabaseDriverEnum::MYSQL);
    }

    /**
     * @param string $_dbname
     *
     * @return DatabaseWrapper
     */
    public function _sqlite(string $_dbname) : DatabaseWrapper {
        return $this->_createConn($_dbname, DatabaseDriverEnum::SQLITE);
    }

    /**
     * Internal functions to cache database connections
     *
     * @param string $_dbname
     * @param DatabaseDriverEnum $_driver
     *
     * @return DatabaseWrapper
     */
    private function _createConn(string $_dbname, DatabaseDriverEnum $_driver) : DatabaseWrapper {
        if (isset($this->_databases[$_driver->value . '_' . $_dbname]))
            return $this->_databases[$_driver->value . '_' . $_dbname];

        $this->_databases[$_driver->value . '_' . $_dbname] = $conn = new DatabaseWrapper($_dbname, $_driver);
        return $conn;
    }
}