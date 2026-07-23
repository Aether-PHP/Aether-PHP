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

namespace Aether\Middleware\Stack;

use Aether\Middleware\MiddlewareInterface;


class HttpFilteringMiddleware implements MiddlewareInterface {

    /** @var bool EXCLUDE_GET */
    private const bool EXCLUDE_GET = true;


    /**
     * @param callable $_next
     */
    public function _handle(callable $_next){
        $cache = Aether()->_cache()->_apcu();

        $ip = Aether()->_http()->_context()->_getIpaddr();
        $method = Aether()->_http()->_context()->_getMethod();
        $route = Aether()->_http()->_context()->_getRoute();

        if (self::EXCLUDE_GET && $method->_getName() == "GET")
            return $_next();

        # - The goal here is to filter strange http requests
        $key = "filtering://" . $method->_getName() . ":" . $ip . ':' . $route;

        if ($cache->_has($key)){

            if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')){
                return Aether()->_http()->_response()->_json([
                    "status" => "error",
                    "message" => "Flagged for two requests at the same time.",
                ], 403)->_send();
            }

            return Aether()->_http()->_response()->_html(
                '<h1>403 - Forbidden</h1><p>Flagged for two requests at the same time.</p>', 403
            )->_send();

        } else {
            $cache->_set($key, true, 1);
        }

        $_next();
    }
}