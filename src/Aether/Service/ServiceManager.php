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

namespace Aether\Service;

use Aether\Service\Hub\CacheServiceHub;
use Aether\Service\Hub\ConfigServiceHub;
use Aether\Service\Hub\DatabaseServiceHub;
use Aether\Service\Hub\HttpServiceHub;
use Aether\Service\Hub\IoServiceHub;
use Aether\Service\Hub\SessionServiceHub;


class ServiceManager {

    /** @var DatabaseServiceHub $_db */
    private DatabaseServiceHub $_db;

    /** @var CacheServiceHub $_cache */
    private CacheServiceHub $_cache;

    /** @var HttpServiceHub $_http */
    private HttpServiceHub $_http;

    /** @var IoServiceHub $_io */
    private IoServiceHub $_io;

    /** @var ConfigServiceHub $_config */
    private ConfigServiceHub $_config;

    /** @var SessionServiceHub $_session */
    private SessionServiceHub $_session;


    public function __construct(){
        $this->_db = new DatabaseServiceHub();
        $this->_cache = new CacheServiceHub();
        $this->_http = new HttpServiceHub();
        $this->_io = new IoServiceHub();
        $this->_config = new ConfigServiceHub();
        $this->_session = new SessionServiceHub();
    }

    /**
     * @return DatabaseServiceHub
     */
    public function _db() : DatabaseServiceHub {
        return $this->_db;
    }

    /**
     * @return CacheServiceHub
     */
    public function _cache() : CacheServiceHub { return $this->_cache; }

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

    /**
     * @return SessionServiceHub
     */
    public function _session() : SessionServiceHub { return $this->_session; }
}