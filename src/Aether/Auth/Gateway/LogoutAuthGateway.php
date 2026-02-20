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

namespace Aether\Auth\Gateway;

use Aether\Auth\AuthInstance;
use Aether\Auth\User\UserFactory;


class LogoutAuthGateway extends AuthInstance implements AuthGatewayEventInterface {

    public function __construct(){
        parent::__construct("", "");
    }

    /**
     * Auth job's purpose : check if user is already logged out.
     *
     * @return bool
     */
    public function _tryAuth() : bool {
        if (!Aether()->_session()->_auth()->_isLoggedIn())
            return $this->_setStatus($this->_onFailure(), false);

        return $this->_setStatus($this->_onSuccess([]), true);
    }

    /***
     * @param array $_data
     *
     * @return string
     */
    public function _onSuccess(array $_data) : string {
        Aether()->_session()->_get()->_removeValue(UserFactory::SESSION_KEY);
        return "user successfullly logged out.";
    }

    /**
     * @return string
     */
    public function _onFailure() : string { return "failed"; }
}