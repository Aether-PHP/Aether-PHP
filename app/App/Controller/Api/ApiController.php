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
 *  Built from scratch. No bloat. POO Embedded.
 *
 *  @author: dawnl3ss (Alex') ©2025 — All rights reserved
 *  Source available • Commercial license required for redistribution
 *  → github.com/dawnl3ss/Aether-PHP
 *
*/
declare(strict_types=1);

namespace App\Controller\Api;


use Aether\Config\ProjectConfig;
use Aether\Api\Format\JsonResponse;

class ApiController {

    public function __construct(){
        header('Content-Type: application/json');
    }

    /**
     * API listing route
     *
     * [@method] => GET
     * [@route] => /api/v1
     */
    public function api(){
        (new JsonResponse())
            ->_add("version", 1.0)
            ->_add("name", ProjectConfig::_get("PROJECT_NAME") . " backend | Powered by Aether-PHP framework.")
            ->_add("description", "Backend API v1 for " . ProjectConfig::_get("PROJECT_NAME"))
            ->_add("routes", array(
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
            ))
        ->_encode();
    }


    /**
     * Test API route
     *
     * [@method] => GET
     * [@route] => /api/v1/test
     */
    public function test(){
        (new JsonResponse())
            ->_add("test", "this is a test")
        ->_encode();
    }
}