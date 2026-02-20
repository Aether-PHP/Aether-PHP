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

namespace Aether\Auth\User;


class UserFactory {

    public const SESSION_KEY = 'user';

    /**
     * Check if a user is logged in.
     *
     * @param array $_session
     *
     * @return bool
     */
    public static function _isLoggedIn() : bool {
        if (!Aether()->_session()->_get()->_valueExist(self::SESSION_KEY))
            return false;

        return !is_null(self::_fromSession());
    }

    /**
     * @return ?UserInstance
     */
    public static function _fromSession() : ?UserInstance {
        if (!Aether()->_session()->_get()->_valueExist(self::SESSION_KEY))
            return null;

        $frmSession = Aether()->_session()->_get()->_getValue(self::SESSION_KEY);
        return new UserInstance($frmSession["id"], $frmSession["username"], $frmSession["email"], $frmSession["perms"]);
    }

    /**
     * @param mixed $_val
     *
     * @return array|null
     */
    public static function _toSession(mixed $_val) : ?array {
        if ($_val instanceof UserInstance)
            return array(
                "id" => $_val->_getUid(),
                "username" => $_val->_getUsername(),
                "email" => $_val->_getEmail(),
                "perms" => json_encode($_val->_getPerms())
            );

        return null;
    }

}