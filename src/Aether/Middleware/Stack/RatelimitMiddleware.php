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

use Aether\Middleware\MiddlewareInterface;


class RatelimitMiddleware implements MiddlewareInterface {

    /** @var int SECOND_INTERVAL */
    private const int SECOND_INTERVAL = 60;

    /** @var int MAX_LIMIT */
    private const int MAX_LIMIT = 100;


    /**
     * @param callable $_next
     */
    public function _handle(callable $_next){
        $ip = $_SERVER['REMOTE_ADDR'];
        $cache = Aether()->_cache()->_apcu();

        $fromCache = $cache->_get("ratelimit_" . $ip);

        if (!is_null($fromCache)){
            if ($fromCache["req"] >= self::MAX_LIMIT && time() < $fromCache['t'] + self::SECOND_INTERVAL){
                http_response_code(403);

                if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) {
                    return Aether()->_http()->_response()->_json([
                        "status" => "error",
                        "message" => "RateLimiter flagged YOU !"
                    ], 403)->_send();
                }

                $cache->_set("ratelimit_" . $ip, ["req" => $fromCache["req"], 't' => time()], 60 * 60 * 24);

                return Aether()->_http()->_response()->_html(
                    '<h1>403 - Forbidden</h1><p>RateLimiter flagged YOU !</p>', 403
                )->_send();

            } else if (time() >= $fromCache['t'] + self::SECOND_INTERVAL)
                $cache->_set("ratelimit_" . $ip, ["req" => 1, 't' => time()], 60 * 60 * 24);
            else
                $cache->_set("ratelimit_" . $ip, ["req" => $fromCache["req"] + 1, 't' => $fromCache['t']], 60 * 60 * 24);
        } else
            $cache->_set("ratelimit_" . $ip, ["req" => 1, 't' => time()], 60 * 60 * 24);

        $_next();
    }
}