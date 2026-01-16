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

namespace Aether\Auth;

use Aether\Config\ProjectConfig;
use Aether\Database\DatabaseWrapper;
use Aether\Database\Drivers\DatabaseDriverEnum;


abstract class AuthInstance implements AuthInterface {

    /** @var string $_email */
    protected string $_email;

    /** @var string $_password */
    protected string $_password;

    /** @var string $_status */
    protected string $_status;

    /** @var DatabaseWrapper $_dbconn */
    protected DatabaseWrapper $_dbconn;

    public function __construct(string $email, string $password){
        $this->_email = $email;
        $this->_password = $password;
        $this->_status = "";
        $this->_dbconn = new DatabaseWrapper(ProjectConfig::_get("AUTH_DATABASE_GATEWAY"), DatabaseDriverEnum::MYSQL);
    }

    /**
     * @return string
     */
    protected function _getEmail() : string { return $this->_email; }

    /**
     * @return string
     */
    protected function _getPassword() : string { return $this->_password; }

    /**
     * @return string
     */
    public function _getStatus() : string { return $this->_status; }

    /**
     * @param string $message
     * @param bool $status
     *
     * @return bool
     */
    protected function _setStatus(string $message, bool $status) : bool {
        $this->_status = $message;
        return $status;
    }

    /**
     * @return bool
     */
    abstract public function _tryAuth() : bool;

}