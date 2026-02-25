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
 *                   < 1 Mo • Zero dependencies • Pure PHP 8.3+
 *
 *  Built from scratch. No bloat. OOP Embedded.
 *
 *  @author: dawnl3ss (Alex') ©2026 — All rights reserved
 *  Source available • Commercial license required for redistribution
 *  → github.com/dawnl3ss/Aether-PHP
 *
*/
declare(strict_types=1);

namespace App;

use Aether\Aether;
use Aether\Middleware\Stack\CsrfMiddleware;
use Aether\Middleware\Stack\MaintenanceMiddleware;
use Aether\Middleware\Stack\RatelimitMiddleware;
use Aether\Middleware\Stack\SecurityHeadersMiddleware;
use Aether\Modules\ModuleFactory;


/**
 * @class App : will handle App-related stuff
 */
class App {

    /** @var string[] $_middlewares */
    private static $_middlewares = [
        MaintenanceMiddleware::class,
        RatelimitMiddleware::class,
        CsrfMiddleware::class,
        SecurityHeadersMiddleware::class
    ];

    /** @var array $_modules */
    private static array $_modules = [];


    public static function _init() : void {
        # - Modules load
        ModuleFactory::_load(self::$_modules);

        # - Middlewares load
        Aether::$_middlewares = self::$_middlewares;
    }
}