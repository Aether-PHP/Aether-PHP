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

use Aether\Http\HttpParameterTypeEnum;
use Aether\Middleware\MiddlewareInterface;
use Aether\Security\Token\CsrfToken;


final class CsrfMiddleware implements MiddlewareInterface {

    /**
     * @param callable $_next
     */
    public function _handle(callable $_next){

        # - We expose the token when http req is GET|HEAD|OPTIONS
        if (in_array($_SERVER['REQUEST_METHOD'], ['GET', 'HEAD', 'OPTIONS'])){
            CsrfToken::_exposeHeader();
            $_next();
            return;
        }

        # - Stricter verification when http req is POST|PUT|DELETE|PATCH
        $submitted = Aether()->_http()->_parameters(HttpParameterTypeEnum::PHP_INPUT)->_getAttribute("csrf_token")
            ?? $_SERVER['HTTP_X_CSRF_TOKEN']
            ?? '';

        if (!CsrfToken::_verify($submitted)){
            http_response_code(403);

            if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')){
                return Aether()->_http()->_response()->_json([
                    "status" => "error",
                    "message" => "Invalid or missing CSRF token"
                ], 403)->_send();
            }

            return Aether()->_http()->_response()->_html(
                '<h1>403 - Forbidden</h1><p>Invalid CSRF token.</p>', 403
            )->_send();
        }

        $_next();
    }
}