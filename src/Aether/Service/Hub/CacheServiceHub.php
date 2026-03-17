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

use Aether\Cache\Adapter\Apcu;
use Aether\Cache\Adapter\CacheAdapterEnum;
use Aether\Cache\Adapter\Files;
use Aether\Cache\Adapter\Redis;
use Aether\Cache\CacheFactory;


final class CacheServiceHub {

    /** @var Redis|null $_redis */
    private ?Redis $_redis = null;

    /**
     * @return Apcu
     */
    public function _apcu() : Apcu {
        return CacheFactory::_get(CacheAdapterEnum::APCU);
    }

    /**
     * @return Files
     */
    public function _files() : Files {
        return CacheFactory::_get(CacheAdapterEnum::FILES);
    }

    /**
     * @return Redis
     */
    public function _redis() : Redis {
        if (is_null($this->_redis))
            $this->_redis = CacheFactory::_get(CacheAdapterEnum::REDIS);

        return $this->_redis;
    }
}