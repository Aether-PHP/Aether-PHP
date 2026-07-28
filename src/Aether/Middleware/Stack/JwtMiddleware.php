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
use Aether\Security\Token\JWToken;


final class JwtMiddleware implements MiddlewareInterface {

    /**
     * @param callable $_next
     */
    public function _handle(callable $_next){

        $token = self::_extractToken();

        if (!$token || !JWToken::_verify($token)){
            http_response_code(401);

            if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')){
                return Aether()->_http()->_response()->_json([
                    "status" => "error",
                    "message" => "Invalid or missing JWT token.",
                ], 401)->_send();
            }

            return Aether()->_http()->_response()->_html(
                '<h1>401 - Unauthorized</h1><p>Invalid or missing token.</p>', 401
            )->_send();
        }

        # - Inject decoded payload into request attributes for downstream usage
        $_REQUEST['__jwt_payload'] = JWToken::_decode($token);

        $_next();
    }

    /**
     * @return string|null
     */
    private static function _extractToken() : ?string {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (str_starts_with($header, 'Bearer '))
            return substr($header, 7);

        return $_GET['token'] ?? null;
    }

}
