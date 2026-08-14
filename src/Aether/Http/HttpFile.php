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

namespace Aether\Http;

use Aether\IO\IOFile;
use Aether\IO\IOTypeEnum;


final class HttpFile {

    /** @var int|float $_maxSize */
    private int $_maxSize = 1024 * 1024 * 2;

    /** @var array $_allowedExtensions */
    private array $_allowedExtensions;

    /** @var array $_allowedMimeTypes */
    private array $_allowedMimeTypes;

    /** @var array $_fileData */
    private array $_fileData;


    public function __construct(string $_key, array $_allowedExtensions, array $_allowedMimeTypes, int $_maxSize = -1){
        if (isset($_FILES[$_key])){
            if ($_FILES[$_key]["error"] === UPLOAD_ERR_OK)
                $this->_fileData = $_FILES[$_key];
            else
                $this->_fileData = [];
        } else {
            $this->_fileData = [];
        }

        $this->_allowedExtensions = $_allowedExtensions;
        $this->_allowedMimeTypes = $_allowedMimeTypes;
        if ($_maxSize != -1) $this->_maxSize = $_maxSize;
    }

    /**
     * Check the file's real extension (pathinfo)
     *
     * @return bool
     */
    public function _checkExtensions() : bool {
        if ($this->_fileData === [] || $this->_allowedExtensions === [])
            return false;

        return in_array(pathinfo($this->_fileData["name"], PATHINFO_EXTENSION), $this->_allowedExtensions);
    }

    /**
     * Check the file's mime type
     *
     * @return bool
     */
    public function _checkMimeTypes() : bool {
        if ($this->_fileData === [] || $this->_allowedMimeTypes === [])
            return false;

        return in_array($this->_fileData["type"], $this->_allowedMimeTypes, true);
    }

    /**
     * Check if the file is not bigger than the accepted max size
     *
     * @return bool
     */
    public function _checkSize() : bool {
        if ($this->_fileData === [])
            return false;

        return $this->_fileData["size"] <= $this->_maxSize;
    }

    /**
     * Save the temp file to a real location
     *
     * @param string $_path
     * @param bool $_override
     *
     * @return bool
     */
    public function _save(string $_path, bool $_override = false) : bool {
        if ($this->_fileData === [])
            return false;

        $file = IOFile::_open(IOTypeEnum::OTHER, $_path);

        if (!is_null($file->_read()) && !$_override)
            return false;

        if (is_null($file->_write(file_get_contents($this->_fileData["tmp_name"]))))
            return false;

        return true;
    }

    /**
     * @return string
     */
    public function _genRandomName() : string {
        return bin2hex(openssl_random_pseudo_bytes(16));
    }

}