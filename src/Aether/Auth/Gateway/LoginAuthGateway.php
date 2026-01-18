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
use Aether\Auth\User\UserInstance;
use Aether\Config\ProjectConfig;
use Aether\Security\PasswordHashingTrait;
use Aether\Session\SessionInstance;


class LoginAuthGateway extends AuthInstance implements AuthGatewayEventInterface {
    use PasswordHashingTrait;

    public function __construct(string $_email, string $_password){
        parent::__construct($_email, $_password);
    }

    /**
     * Auth job's purpose : check if provided data are corrects
     *
     * @return bool
     */
    public function _tryAuth() : bool {
        if (!$this->_dbconn->_exist(ProjectConfig::_get("AUTH_TABLE_GATEWAY"), [ "email" => $this->_email ]))
            return $this->_setStatus($this->_onFailure(), false);

        $_data = $this->_dbconn->_select(ProjectConfig::_get("AUTH_TABLE_GATEWAY"), '*', [ "email" => $this->_email ])[0];

        if (!$this->_checkPassword($this->_password, $_data["password_hash"]))
            return $this->_setStatus($this->_onFailure(), false);

        return $this->_setStatus($this->_onSuccess($_data), true);
    }

    /***
     * @param array $_data
     *
     * @return string
     */
    public function _onSuccess(array $_data) : string {
        $user_db = $_data;

        $user = new UserInstance(
            $user_db["uid"],
            $user_db["username"],
            $user_db["email"],
            $user_db["perms"]
        );

        $serialized = serialize($user);
        $signature = hash_hmac('sha256', $serialized, $_ENV["SESSION_HMAC"]);

        $_SESSION["user"] = $serialized . '::' . $signature;

        return "user successfullly logged in.";
    }

    /**
     * @return string
     */
    public function _onFailure() : string {
        return "user login failed.";
    }
}