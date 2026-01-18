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
 *  Built from scratch. No bloat. POO Embedded.
 *
 *  @author: dawnl3ss (Alex') ©2025 — All rights reserved
 *  Source available • Commercial license required for redistribution
 *  → github.com/dawnl3ss/Aether-PHP
 *
*/
declare(strict_types=1);

namespace Aether\Auth\User\Permission;


class PermissionLayer {

    /** @var string[] $_perms */
    protected array $_perms;


    /**
     * @param mixed $_perm
     *
     * @return bool
     */
    protected function _hasPermission(mixed $_perm) : bool {
        if (is_string($_perm))
            return in_array($_perm, $this->_perms);

        return in_array($_perm->value, $this->_perms);
    }

    /**
     * @param mixed $_perm
     * @param int $uid
     *
     * @return PermissionLayer
     */
    protected function _setPermission(mixed $_perm, int $uid) : PermissionLayer {
        if (!is_string($_perm))
            $_perm = $_perm->value;

        array_push($this->_perms, $_perm);

        Aether()->_db()->_mysql($_ENV["AUTH_DATABASE_GATEWAY"])->_update(
            "users",
            ["perms" => $this->_stringify()],
            ["uid" => $uid]
        );

        return $this;
    }

    /**
     * @param mixed $_perm
     * @param int $uid
     *
     * @return PermissionLayer
     */
    protected function _deletePermission(mixed $_perm, int $uid) : PermissionLayer {
        if (!is_string($_perm))
            $_perm = $_perm->value;

        $this->_perms = array_filter($this->_perms, fn($_p) => $_p !== $_perm);

        Aether()->_db()->_mysql($_ENV["AUTH_DATABASE_GATEWAY"])->_update(
            "users",
            ["perms" => $this->_stringify()],
            ["uid" => $uid]
        );

        return $this;
    }

    /**
     * Stringify permissions array (json_encode) to store it in the database
     *
     * @return string
     */
    private function _stringify() : string { return json_encode($this->_perms); }

    /**
     * Serialize permissions string (json_decode) to decode it from the database
     *
     * @param string $_data
     *
     * @return array
     */
    protected function _serialize(string $_data) : array { return json_decode($_data, true); }
}