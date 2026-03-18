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

namespace Aether\Config;

use Aether\Exception\EnvConfigException;
use Aether\IO\IOFile;
use Aether\IO\IOTypeEnum;


final class EnvDataUnpacker {

    /** @var array $_envData */
    private array $_envData;


    /**
     * @throws EnvConfigException
     */
    public function __construct(){
        $_raw = IOFile::_open(IOTypeEnum::ENV, _root(".env"))->_readDecoded();

        if (!is_array($_raw) || $_raw === [])
            throw new EnvConfigException("Env config missing or invalid. Ensure .env exists at project root and is readable.");

        $this->_envData = $_raw;
    }


    /**
     * @param string $_key
     *
     * @return mixed
     */
    public function _get(string $_key){
        return $this->_envData[$_key];
    }

    /**
     * @return array
     */
    public function _raw() : array { return $this->_envData; }
}