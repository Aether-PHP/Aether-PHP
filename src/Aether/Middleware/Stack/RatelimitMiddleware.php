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

    /**
     * @param callable $_next
     */
    public function _handle(callable $_next){
        $ip = $_SERVER['REMOTE_ADDR'];
        $cache = Aether()->_cache()->_files();

        $fromCache = $cache->_get("ratelimit://" . $ip);

        $maxlimit = (int)$_ENV["RATELIMIT_MAX_LIMIT"];
        $secondInterval = (int)$_ENV["RATELIMIT_SECOND_INTERVAL"];

        if (!is_null($fromCache)){
            if ($fromCache["req"] >= $maxlimit && time() < $fromCache['t'] + $secondInterval){
                http_response_code(403);
                $cache->_set("ratelimit://" . $ip, ["req" => $fromCache["req"], 't' => time()], 60 * 60 * 24);

                if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) {
                    return Aether()->_http()->_response()->_json([
                        "status" => "error",
                        "message" => "RateLimiter flagged YOU !"
                    ], 403)->_send();
                }

                return Aether()->_http()->_response()->_html(
                    '<h1>403 - Forbidden</h1><p>RateLimiter flagged YOU !</p>', 403
                )->_send();

            } else if (time() >= $fromCache['t'] + $secondInterval)
                $cache->_set("ratelimit://" . $ip, ["req" => 1, 't' => time()], 60 * 60 * 24);
            else
                $cache->_set("ratelimit://" . $ip, ["req" => $fromCache["req"] + 1, 't' => $fromCache['t']], 60 * 60 * 24);
        } else
            $cache->_set("ratelimit://" . $ip, ["req" => 1, 't' => time()], 60 * 60 * 24);

        $_next();
    }
}