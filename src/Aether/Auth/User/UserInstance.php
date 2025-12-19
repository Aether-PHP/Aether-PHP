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

namespace Aether\Auth\User;

use Aether\Auth\User\Permission\PermissionEnum;
use Aether\Auth\User\Permission\PermissionLayer;
use Aether\Security\UserInputValidatorTrait;


class UserInstance extends PermissionLayer implements UserInterface {
    use UserInputValidatorTrait;

    /** @var int $_uid */
    private int $_uid;

    /** @var string $_username */
    private string $_username;

    /** @var string $_email */
    private string $_email;


    public function __construct(int $uid, string $username, string $email, string $_perms){
        $this->_uid = $uid;
        $this->_username = $username;
        $this->_email = $email;
        $this->_perms = json_decode($_perms, true);
    }

    /**
     * Check if a user is logged in.
     *
     * @param array $_session
     *
     * @return bool
     */
    public static function _isLoggedIn() : bool {
        return isset($_SESSION["user"]) && unserialize($_SESSION["user"]) instanceof UserInstance;
    }

    /**
     * @return int
     */
    public function _getUid() : int { return $this->_uid; }


    /**
     * @return string
     */
    public function _getUsername() : string { return $this->_sanitizeInput($this->_username); }

    /**
     * @return string
     */
    public function _getEmail() : string { return $this->_sanitizeInput($this->_email); }

    /**
     * @return bool
     */
    public function _isAdmin() : bool { return $this->_hasPerm(PermissionEnum::PERM_ADMIN); }

    /**
     * @param mixed $_perm
     *
     * @return bool
     */
    public function _hasPerm(mixed $_perm) : bool {
        return $this->_hasPermission($_perm);
    }

    /**
     * @param mixed $_perm
     * @param int $uid
     *
     * @return UserInstance
     */
    public function _setPerm(mixed $_perm) : UserInstance {
        if ($this->_hasPerm($_perm))
            return $this;

        $this->_setPermission($_perm, $this->_getUid());
        return $this;
    }

    /**
     * @return array
     */
    public function _getPerms() : array { return $this->_perms; }

}