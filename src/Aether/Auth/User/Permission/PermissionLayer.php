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


use Aether\Config\ProjectConfig;
use Aether\Modules\Database\DatabaseWrapper;
use Aether\Modules\Database\Drivers\DatabaseDriverEnum;

class PermissionLayer {

    /** @var array $_perms */
    protected array $_perms;


    /**
     * @param string $_perm
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
     *
     * @return PermissionLayer
     */
    protected function _setPermission(mixed $_perm, int $uid) : PermissionLayer {
        array_push($this->_perms, $_perm);
        (new DatabaseWrapper(ProjectConfig::_get("AUTH_DATABASE_GATEWAY"), DatabaseDriverEnum::MYSQL))->_update(
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

}