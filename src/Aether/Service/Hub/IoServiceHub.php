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

namespace Aether\Service\Hub;

use Aether\IO\IOFile;
use Aether\IO\IOFolder;
use Aether\IO\IOStream;
use Aether\IO\IOTypeEnum;


final class IoServiceHub {

    /**
     * @param IOTypeEnum $_type
     * @param string $_path
     *
     * @return IOFile
     */
    public function _file(string $_path, IOTypeEnum $_type) : IOFile {
        return IOFile::_open($_type, $_path);
    }

    /**
     * @param IOTypeEnum $_type
     * @param string $_path
     *
     * @return IOStream
     */
    public function _stream(string $_path, IOTypeEnum $_type) : IOStream {
        return IOStream::_open($_type, $_path);
    }

    /**
     * @param string $_path
     *
     * @return IOFolder
     */
    public function _folder(string $_path) : IOFolder {
        return IOFolder::_path($_path);
    }
}