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


class MaintenanceMiddleware implements MiddlewareInterface {

    /**
     * @param callable $_next
     * @return void
     */
    public function _handle(callable $_next){
        $inMaintenance = $_ENV['MAINTENANCE'] === "true";

        if ($inMaintenance){
            http_response_code(503);

            if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')){
                return Aether()->_http()->_response()->_json([
                    "status" => "error",
                    "message" => "Website in maintenance."
                ], 503)->_send();
            }

            return Aether()->_http()->_response()->_html(
                '<h1>503 - Forbidden</h1><p>Website in maintenance.</p>', 503
            )->_send();
        }

        $_next();
    }
}