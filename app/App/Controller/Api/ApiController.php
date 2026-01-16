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
 *  @author: dawnl3ss (Alex') ©2025 — All rights reserved
 *  Source available • Commercial license required for redistribution
 *  → github.com/dawnl3ss/Aether-PHP
 *
*/
declare(strict_types=1);

namespace App\Controller\Api;

use Aether\Config\ProjectConfig;
use Aether\Http\Response\Format\HttpResponseFormatEnum;
use Aether\Router\Controller\Controller;


class ApiController extends Controller {

    /**
     * API listing route
     *
     * [@method] => GET
     * [@route] => /api/v1
     */
    public function api(){
        Aether()->_http()->_response()->_json([
            "name" => $_ENV["PROJECT_NAME"] . " backend | Powered by Aether-PHP framework.",
            "version" => 1.0,
            "description" => "Backend API v1 for " . $_ENV["PROJECT_NAME"],
            "routes" => array(
                [
                    "method" => "GET",
                    "path" => "/api/v1",
                    "description" => "API v1 lobby"
                ],
                [
                    "method" => "GET",
                    "path" => "/api/v1/test",
                    "description" => "Test Route for API"
                ],
                [
                    "method" => "POST",
                    "path" => "/api/v1/auth/login",
                    "description" => "Auth Login route"
                ],
                [
                    "method" => "POST",
                    "path" => "/api/v1/auth/register",
                    "description" => "Auth Register route"
                ],
                [
                    "method" => "POST",
                    "path" => "/api/v1/auth/logout",
                    "description" => "Auth Logout route"
                ]
            )
        ], 200)->_send();
    }


    /**
     * Test API route
     *
     * [@method] => GET
     * [@route] => /api/v1/test
     */
    public function test(){
        Aether()->_http()->_response()->_json([
            "test" => "This is a test v23"
        ], 200)->_send();
    }
}