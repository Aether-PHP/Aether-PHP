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
use Aether\Session\SessionInstance;


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
        $this->_perms = $this->_serialize($_perms);
    }

    /**
     * Check if a user is logged in.
     *
     * @param array $_session
     *
     * @return bool
     */
    public static function _isLoggedIn() : bool {
        if (!isset($_SESSION["user"]))
            return false;

        $parts = explode('::', $_SESSION["user"], 2);

        if (count($parts) !== 2)
            return false;

        [$serialized, $signature] = $parts;

        return hash_equals(
            $signature,
            hash_hmac('sha256', $serialized, $_ENV["SESSION_HMAC"])
        );
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
     * @param mixed $_perm
     *
     * @return bool
     */
    public function _hasPerm(mixed $_perm) : bool {
        return $this->_hasPermission($_perm);
    }

    /**
     * @param mixed $_perm
     *
     * @return UserInstance
     */
    public function _addPerm(mixed $_perm) : UserInstance {
        if ($this->_hasPerm($_perm))
            return $this;

        $this->_setPermission($_perm, $this->_getUid());
        $this->_update();
        return $this;
    }

    /**
     * @param mixed $_perm
     *
     * @return UserInstance
     */
    public function _removePerm(mixed $_perm) : UserInstance {
        if (!$this->_hasPerm($_perm))
            return $this;

        $this->_deletePermission($_perm, $this->_getUid());
        $this->_update();
        return $this;
    }

    /**
     * @return array
     */
    public function _getPerms() : array { return $this->_perms; }


    /**
     * @return bool
     */
    public function _isAdmin() : bool { return $this->_hasPerm(PermissionEnum::PERM_ADMIN); }

    /**
     * Update User's instance in session
     */
    private function _update(){ SessionInstance::_addHttpSess("user", $this); }
}