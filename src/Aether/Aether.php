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

namespace Aether;

use Aether\Config\ProjectConfig;
use Aether\Middleware\Pipeline;
use Aether\Middleware\Stack\AuthMiddleware;
use Aether\Middleware\Stack\CsrfMiddleware;
use Aether\Middleware\Stack\MaintenanceMiddleware;
use Aether\Middleware\Stack\RatelimitMiddleware;
use Aether\Middleware\Stack\SecurityHeadersMiddleware;
use Aether\Modules\ModuleFactory;
use Aether\Router\Controller\ControllerGateway;
use Aether\Security\Token\CsrfToken;
use Aether\Service\ServiceManager;
use Aether\Session\Session;
use Aether\Session\SessionHandler;
use App\App;

/*
 * Pure PHP 8.3+ framework built from scratch.
 *
 * Wanted a lightweight and fast alternative to other useless-as-hell and huge frameworks.
 * Easy to incorporate in SaaS, Webapps, REST APIs...
 * -> https://getaether.space
 *
 * Made by : Dawnless (Alexandre VOISIN)
 * → https://www.linkedin.com/in/alexvsn/
 * → https://dawnless.me
 * → https://hardware-hub.fr
 */
class Aether {

    /** @var string $_globalAppState */
    public static string $_globalAppState = "DEV";

    /** @var ServiceManager $_services */
    private static ServiceManager $_services;

    /** @var array $_middlewares */
    public static array $_middlewares = [];


    /**
     * Aether core init function
     *
     * @return void
     */
    private function _init() : void {

        # - Dev Env-related
        if (self::$_globalAppState === "DEV"){
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }

        # - Core helpers
        include_once __DIR__ . "/Utils/CoreHelperFunctions.php";

        # - .Env File load
        ProjectConfig::_load();

        # - Cookies Security Fix
        session_set_cookie_params([
            'httponly' => true,
            'secure' => true,
            'samesite' => 'Strict'
        ]);

        # - Session
        SessionHandler::_load();
    }

    /**
     * Aether core run function
     *
     * @return void
     */
    public function _run() : void {
        $this->_init();
        App::_init();

        # - Middlewares Pipeline
        Pipeline::_run(self::$_middlewares, function (){
            # - Router Gateway : deliver correct controller for each route
            ControllerGateway::_link();
        });
    }

    /**
     * @return ServiceManager
     */
    public static function _getServices() : ServiceManager { return self::$_services ??= new ServiceManager(); }
}