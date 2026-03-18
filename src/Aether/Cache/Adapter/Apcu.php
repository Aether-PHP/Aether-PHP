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

namespace Aether\Cache\Adapter;

use Aether\Cache\CacheInterface;

use function apcu_fetch;
use function apcu_store;
use function apcu_delete;
use function apcu_clear_cache;


class Apcu implements CacheInterface {

    /**
     * @param string $_key
     * @param mixed|null $_default
     *
     * @return mixed
     */
    public function _get(string $_key, mixed $_default = null) : mixed {
        $val = apcu_fetch($_key, $ok);
        return $ok ? $val : $_default;
    }

    /**
     * @param string $_key
     * @param mixed $_value
     * @param int $_ttl
     *
     * @return bool
     */
    public function _set(string $_key, mixed $_value, int $_ttl = 0) : bool {
        return apcu_store($_key, $_value, $_ttl);
    }

    /**
     * @param string $_key
     *
     * @return bool
     */
    public function _delete(string $_key) : bool {
        return apcu_delete($_key);
    }

    /**
     * @return bool
     */
    public function _clear() : bool {
        return apcu_clear_cache();
    }

    public function _has(string $_key) : bool {
        apcu_fetch($_key, $status);
        return $status;
    }
}