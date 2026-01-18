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


class RegisterAuthGateway extends AuthInstance implements AuthGatewayEventInterface {
    use PasswordHashingTrait;

    /** @var string $_username */
    private string $_username;

    public function __construct(string $_username, string $_email, string $_password){
        parent::__construct($_email, $_password);
        $this->_username = $_username;
    }


    /**
     * Auth job's purpose : check if provided email is not already used.
     *
     * @return bool
     */
    public function _tryAuth() : bool {
        if ($this->_dbconn->_exist(ProjectConfig::_get("AUTH_TABLE_GATEWAY"), [ "email" => $this->_email ]))
            return $this->_setStatus($this->_onFailure(), false);

        return $this->_setStatus($this->_onSuccess([]), true);
    }


    /**
     * @param array $_data
     *
     * @return string
     */
    public function _onSuccess(array $_data) : string {
        $this->_dbconn->_insert(ProjectConfig::_get("AUTH_TABLE_GATEWAY"), [
            "username" => $this->_username,
            "email" => $this->_email,
            "password_hash" => $this->_hashPassword($this->_password),
            "perms" => "[]"
        ]);
        $user_db = $this->_dbconn->_select(ProjectConfig::_get("AUTH_TABLE_GATEWAY"), '*', [ "email" => $this->_email ])[0];

        $user = new UserInstance(
            $user_db["uid"],
            $user_db["username"],
            $user_db["email"],
            $user_db["perms"]
        );

        $serialized = serialize($user);
        $signature = hash_hmac('sha256', $serialized, $_ENV["SESSION_HMAC"]);

        $_SESSION["user"] = $serialized . '::' . $signature;
        return "user successfullly signed up.";
    }

    /**
     * @return string
     */
    public function _onFailure() : string {
        return "provided email is already used.";
    }
}