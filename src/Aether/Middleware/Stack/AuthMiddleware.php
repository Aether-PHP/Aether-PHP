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
 *  → https://github.com/dawnl3ss/Aether-PHP
 *
*/
declare(strict_types=1);

namespace Aether\Middleware\Stack;


use Aether\Aether;
use Aether\Http\Response\Format\HttpResponseFormatEnum;
use Aether\Middleware\MiddlewareInterface;

class AuthMiddleware implements MiddlewareInterface {


    /**
     * @param callable $_next
     */
    public function _handle(callable $_next){
        if (!Aether()->_session()->_auth()->_isLoggedIn()){
            http_response_code(403);

            if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')){
                return Aether()->_http()->_response()->_json([
                    "status" => "error",
                    "message" => "You are not logged in.",
                ], 403)->_send();
            }

            return Aether()->_http()->_response()->_html(
                '<h1>403 - Forbidden</h1><p>You are not logged in.</p>', 403
            )->_send();
        }

        $_next();
    }
}