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
 *  → github.com/dawnl3ss/Aether-PHP
 *
*/
declare(strict_types=1);

namespace Aether\IO;


final class IOFolder {

    /** @var string $_path */
    private string $_path;

    public function __construct(string $_path){
        $this->_path = $_path;
    }

    /**
     * @return string
     */
    public function _getPath() : string { return $this->_path; }

    /**
     * @return bool
     */
    public function _exist() : bool { return is_dir($this->_getPath()); }

    /**
     * @param int $_perms
     *
     * @return bool
     */
    public function _setPerm(int $_perms) : bool {
        return chmod($this->_getPath(), $_perms);
    }

    /**
     * @param bool $_check
     *
     * @return bool
     */
    public function _create(bool $_check = false) : bool {
        if ($_check && $this->_exist())
            return false;

        return mkdir($this->_getPath(),  0777, true);
    }

    /**
     * @param string $_patern
     *
     * @return array|false
     */
    public function _listFiles(string $_patern = '') : array|false {
        return glob($this->_getPath() . $_patern);
    }

    /**
     * @param string $_name
     *
     * @return null|IOFile
     */
    public function _createFile(string $_name) : ?IOFile {
        if ($this->_exist())
            return null;

        return IOFile::_open(IOTypeEnum::OTHER, $this->_getPath() . '/' . $_name)->_write('', true);
    }

    /**
     * @param string $_name
     * @param bool $_forceCreate
     *
     * @return IOFile|null
     */
    public function _getFile(string $_name, bool $_forceCreate = false) : ?IOFile {
        if ($_forceCreate && !$this->_exist())
            return $this->_createFile($_name);

        return IOFile::_open(IOTypeEnum::OTHER, $this->_getPath() . '/' . $_name);
    }

    /**
     * @param string $_file
     * @param int $_perms
     *
     * @return bool
     */
    public function _setFilePerm(string $_file, int $_perms) : bool {
        return chmod($this->_getPath() . '/' . $_file, $_perms);
    }

    /**
     * @param string $_path
     *
     * @return IOFolder
     */
    public static function _path(string $_path) : IOFolder {
        return new self($_path);
    }
}