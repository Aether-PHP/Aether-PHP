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
        $this->_perms = $this->_serialize($_perms);
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
    private function _update(){ Aether()->_session()->_get()->_setValue("user", UserFactory::_toSession($this)); }
}