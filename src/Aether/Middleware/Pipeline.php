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

namespace Aether\Middleware;


final class Pipeline {

    /**
     * @param MiddlewareInterface::class[] $_middlewares
     * @param callable $_finalHandler
     *
     * @return void
     */
    public static function _run(array $_middlewares, callable $_finalHandler) : void{
        $pipeline = array_reduce(
            array_reverse($_middlewares),
            fn(callable $next, string $middleware) => fn() => (new $middleware())->_handle($next),
            $_finalHandler
        );

        $pipeline();
    }

    /***
     * @param array $_middlewares
     * @param callable $_finalHandler
     *
     * @return void
     */
    public static function _runForRoute(array $_middlewares, callable $_finalHandler) : void {
        $middlewares = array_filter(array_map(function($middleware){
            if ($middleware === "")
                return null;
            return "Aether\\Middleware\\Stack\\" . $middleware;
        }, $_middlewares), function ($middleware){
            if (is_null($middleware))
                return false;

            if (!class_exists($middleware))
                return false;
            return true;
        });

        self::_run($middlewares, $_finalHandler);
    }
}