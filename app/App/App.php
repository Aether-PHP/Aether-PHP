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
 *                  The divine secure, lightweight PHP framework
 *                   < 1 Mo • Zero dependencies • Pure PHP 8.3+
 *
 *  Built from scratch. No bloat. OOP Embedded.
 *
 *  @author: dawnl3ss (Alex') ©2026 — All rights reserved
 *  Source available • Commercial license required for redistribution
 *  → https://aetherphp.net
 *  → https://github.com/Aether-PHP/Aether-PHP
 *
*/
declare(strict_types=1);

namespace App;

use Aether\Aether;
use Aether\Middleware\Stack\CorsMiddleware;
use Aether\Middleware\Stack\CsrfMiddleware;
use Aether\Middleware\Stack\MaintenanceMiddleware;
use Aether\Middleware\Stack\RatelimitMiddleware;
use Aether\Middleware\Stack\SecurityHeadersMiddleware;
use Aether\Modules\ModuleFactory;


/**
 * @class App : will handle App-related stuff
 */
class App {


    public static function _init() : void {

        # - Middlewares load
        Aether::$_middlewares = [
            MaintenanceMiddleware::class,
            RatelimitMiddleware::class,
            CsrfMiddleware::class,
            SecurityHeadersMiddleware::class,
            CorsMiddleware::class
        ];

        # - Modules load
        ModuleFactory::_load([ ]);
    }
}