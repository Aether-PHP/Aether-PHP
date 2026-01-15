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

namespace Aether\Service;

use Aether\Cache\CacheFactory;
use Aether\Cache\CacheInterface;
use Aether\Database\DatabaseWrapper;
use Aether\Database\Drivers\DatabaseDriverEnum;
use Aether\Service\Hub\ConfigServiceHub;
use Aether\Service\Hub\HttpServiceHub;
use Aether\Service\Hub\IoServiceHub;


class ServiceManager {

    /** @var DatabaseWrapper[] $_databases */
    private array $_databases = [];

    /** @var CacheInterface $_cache */
    private CacheInterface $_cache;

    /** @var HttpServiceHub $_http */
    private HttpServiceHub $_http;

    /** @var IoServiceHub $_io */
    private IoServiceHub $_io;

    /** @var ConfigServiceHub $_config */
    private ConfigServiceHub $_config;


    public function __construct(){
        $this->_cache = CacheFactory::_get();
        $this->_http = new HttpServiceHub();
        $this->_io = new IoServiceHub();
        $this->_config = new ConfigServiceHub();
    }

    /**
     * @param string $_database
     *
     * @return DatabaseWrapper
     */
    public function _db(string $_database) : DatabaseWrapper {
        if (isset($this->_databases[$_database]))
            return $this->_databases[$_database];

        $this->_databases[$_database] = $conn = new DatabaseWrapper($_database, DatabaseDriverEnum::MYSQL);
        return $conn;
    }

    /**
     * @return CacheInterface
     */
    public function _cache() : CacheInterface { return $this->_cache; }

    /**
     * @return HttpServiceHub
     */
    public function _http() : HttpServiceHub { return $this->_http; }

    /**
     * @return IoServiceHub
     */
    public function _io() : IoServiceHub { return $this->_io; }

    /**
     * @return ConfigServiceHub
     */
    public function _config() : ConfigServiceHub { return $this->_config; }
}