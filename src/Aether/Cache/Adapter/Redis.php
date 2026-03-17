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
 *                      The divine secure lightweight PHP framework
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

namespace Aether\Cache\Adapter;

use Aether\Cache\CacheInterface;
use Aether\Exception\CacheRedisException;
use \Redis as PhpRedis;


class Redis implements CacheInterface {

    /** @var PhpRedis $_redis */
    private PhpRedis $_redis;


    public function __construct(){
        $this->_redis = new PhpRedis();

        $addr = Aether()->_config()->_get("REDIS_ADDRESS");
        $port = Aether()->_config()->_get("REDIS_PORT");

        if (is_null($addr) || is_null($port))
            throw new CacheRedisException("[Caching] - Redis - Configuration parameters are missing in the .env file.");

        $this->_redis->connect($addr, $port);
    }

    /**
     * @param string $_key
     * @param mixed|null $_default
     *
     * @return mixed
     */
    public function _get(string $_key, mixed $_default = null) : mixed {
        $val = $this->_redis->get($_key);
        return $val !== false ? $val : $_default;
    }

    /**
     * @param string $_key
     * @param mixed $_value
     * @param int $_ttl seconds (0 = no expiration)
     *
     * @return bool
     */
    public function _set(string $_key, mixed $_value, int $_ttl = 0) : bool {
        if ($_ttl > 0)
            return $this->_redis->setEx($_key, $_ttl, $_value);

        return $this->_redis->set($_key, $_value);
    }

    /**
     * @param string $_key
     *
     * @return bool
     */
    public function _delete(string $_key) : bool {
        return (bool)$this->_redis->del($_key);
    }

    /**
     * @return bool
     */
    public function _clear() : bool {
        return $this->_redis->flushDB();
    }

    /**
     * @param string $_key
     *
     * @return bool
     */
    public function _has(string $_key) : bool {
        return $this->_redis->exists($_key) > 0;
    }
}