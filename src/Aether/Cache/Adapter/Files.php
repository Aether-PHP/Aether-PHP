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
use Aether\IO\IOTypeEnum;


class Files implements CacheInterface {

    /** @var string DEFAULT_DIR */
    private const DEFAULT_DIR = 'storage/cache';


    /**
     * Resolve cache directory path (from .env or default)
     *
     * @return string
     */
    private function _getDir() : string {
        $dir = $_ENV['CACHE_FILES_PATH'] ?? _root(self::DEFAULT_DIR);
        return rtrim($dir, "/\\");
    }

    /**
     * Resolve cache file path from key
     *
     * @param string $_key
     *
     * @return string
     */
    private function _getPath(string $_key) : string {
        $hash = hash('sha256', $_key);
        return $this->_getDir() . '/' . $hash . '.cache.json';
    }

    /**
     * Ensure cache directory exists
     *
     * @return void
     */
    private function _ensureDir() : void {
        $folder = Aether()->_io()->_folder($this->_getDir());

        if (!$folder->_exist())
            $folder->_create(true);
    }


    /**
     * @param string $_key
     * @param mixed|null $_default
     *
     * @return mixed
     */
    public function _get(string $_key, mixed $_default = null) : mixed {
        $path = $this->_getPath($_key);
        $file = Aether()->_io()->_file($path, IOTypeEnum::JSON);

        $payload = $file->_readDecoded();

        if (!is_array($payload) || !isset($payload['v'], $payload['e']))
            return $_default;

        # - TTL check
        if ($payload['e'] !== 0 && time() > $payload['e']){
            $file->_delete();
            return $_default;
        }

        return $payload['v'];
    }

    /**
     * @param string $_key
     * @param mixed $_value
     * @param int $_ttl
     *
     * @return bool
     */
    public function _set(string $_key, mixed $_value, int $_ttl = 0) : bool {
        $this->_ensureDir();

        $expiresAt = $_ttl > 0 ? time() + $_ttl : 0;
        $payload = [
            'v' => $_value,
            'e' => $expiresAt
        ];

        $path = $this->_getPath($_key);
        $file = Aether()->_io()->_file($path, IOTypeEnum::JSON);

        return !is_null($file->_write($payload));
    }

    /**
     * @param string $_key
     *
     * @return bool
     */
    public function _delete(string $_key) : bool {
        $path = $this->_getPath($_key);
        $file = Aether()->_io()->_file($path, IOTypeEnum::JSON);

        return $file->_delete();
    }

    /**
     * @return bool
     */
    public function _clear() : bool {
        $folder = Aether()->_io()->_folder($this->_getDir());

        if (!$folder->_exist())
            return true;

        $files = $folder->_listFiles('/*.cache.json');

        if ($files === false)
            return false;

        $ok = true;

        foreach ($files as $f){
            $file = Aether()->_io()->_file($f, IOTypeEnum::JSON);

            if (!$file->_delete())
                $ok = false;
        }

        return $ok;
    }

    /**
     * @param string $_key
     *
     * @return bool
     */
    public function _has(string $_key) : bool {
        $path = $this->_getPath($_key);
        $file = Aether()->_io()->_file($path, IOTypeEnum::JSON);

        $payload = $file->_readDecoded();

        if (!is_array($payload) || !isset($payload['e']))
            return false;

        if ($payload['e'] !== 0 && time() > $payload['e']){
            $file->_delete();
            return false;
        }

        return true;
    }


    /**
     * @param string $_key
     * @param callable $_fallback
     *
     * @return mixed
     */
    public function _getFallback(string $_key, callable $_fallback) : mixed {
        if ($this->_has($_key))
            return $this->_get($_key);

        return $_fallback();
    }
}

