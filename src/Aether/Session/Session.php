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
 *  → https://github.com/Aether-PHP/Aether-PHP
 *
*/
declare(strict_types=1);

namespace Aether\Session;

use Aether\Session\Security\SessionSecurityLayer;


class Session extends SessionSecurityLayer {


    /**
     * @param string $_key
     *
     * @return mixed
     */
    public function _getValue(string $_key) : mixed {
        if (!$this->_valueExist($_key))
            return null;

        return self::_decodeNode($_key);
    }

    /**
     * @param string $_key
     * @param mixed $_dat
     *
     * @return void
     */
    public function _setValue(string $_key, mixed $_dat) : void {
        $_SESSION[$_key] = self::_encodeNode($_key, $_dat);
    }

    /**
     * @param string $_key
     *
     * @return bool
     */
    public function _valueExist(string $_key) : bool {
        return isset($_SESSION[$_key]);
    }

    /**
     * @param string $_key
     *
     * @return void
     */
    public function _removeValue(string $_key) : void { unset($_SESSION[$_key]); }

    /**
     * @return false|string
     */
    public function _getSessId() : false|string { return session_id(); }

    /**
     * @return bool
     */
    public function _regenerateId() : bool { return session_regenerate_id(true); }

}