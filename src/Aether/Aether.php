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
 *  → https://github.com/dawnl3ss/Aether-PHP
 *
*/
declare(strict_types=1);

namespace Aether;

use Aether\Auth\Gateway\LoginAuthGateway;
use Aether\Auth\User\UserInstance;
use Aether\Config\ProjectConfig;
use Aether\Modules\Database\DatabaseWrapper;
use Aether\Modules\Database\Drivers\DatabaseDriverEnum;
use Aether\Router\ControllerGateway;
use Aether\Session\SessionInstance;


/*
 *
 * Pure PHP 8.3+ framework built from scratch.
 *
 * Wanted a lightweight and fast alternative to other useless-as-hell and huge frameworks.
 * Easy to incorporate in SaaS, Webapps, REST APIs...
 *
 * Made by : Dawnless (Alexandre VOISIN)
 * → https://www.linkedin.com/in/alexvsn/
 * → https://dawnless.me
 * → https://hardware-hub.fr
 *
 */
class Aether {

    /** @var string $_globalAppState */
    public static $_globalAppState = "DEV";


    public static function _init(){

        # - Dev Env-related
        if (self::$_globalAppState == "DEV"){
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }

        # - .Env File load
        ProjectConfig::_load();

        # - Cookies Security Fix
        session_set_cookie_params([
            'httponly' => true,
            'secure' => true,
            'samesite' => 'Strict'
        ]);

        # - Session
        ini_set('session.cookie_lifetime', 60 * 60 * 24 * 10);
        ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 10);
        session_start();


        # - Router Gateway : deliver correct controller for each route
        (new ControllerGateway())->_link();
    }
}