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

use Aether\Api\Format\JsonResponse;
use Aether\Auth\Gateway\LoginAuthGateway;
use Aether\Auth\Gateway\LogoutAuthGateway;
use Aether\Auth\Gateway\RegisterAuthGateway;
use Aether\Auth\User\UserInstance;
use Aether\Http\HttpParameterUnpacker;
use Aether\Security\UserInputValidatorTrait;


class AuthApiController {
    use UserInputValidatorTrait;

    public function __construct(){
        header('Content-Type: application/json');
    }

    /**
     * Login API route
     *
     * [@method] => POST
     * [@route] => /api/v1/auth/login
     */
    public function login(){
        $http_params = new HttpParameterUnpacker();
        $json = new JsonResponse();

        $email = $http_params->_getAttribute("email") == false ? "" : $http_params->_getAttribute("email");
        $password = $http_params->_getAttribute("password") == false ? "" : $http_params->_getAttribute("password");

        if ($email == "" || $password == ""){
            return $json
                ->_add("status", "failed")
                ->_add("message", "Credentials should not be empty.")
            ->_encode();
        }

        if (UserInstance::_isLoggedIn()){
            return $json
                ->_add("status", "failed")
                ->_add("message", "user aldready logged-in.")
            ->_encode();
        }

        $gateway = new LoginAuthGateway($email, $password);

        if (!$gateway->_tryAuth()){
            return $json
                ->_add("status", "failed")
                ->_add("message", $gateway->_getStatus())
            ->_encode();
        }

        return $json
            ->_add("status", "success")
            ->_add("message", $gateway->_getStatus())
        ->_encode();
    }



    /**
     * Register API route
     *
     * [@method] => POST
     * [@route] => /api/v1/auth/register
     */
    public function register(){
        $http_params = new HttpParameterUnpacker();
        $json = new JsonResponse();

        $username = $this->_sanitizeInput($http_params->_getAttribute("username") == false ? "" : $http_params->_getAttribute("username"));
        $email = $http_params->_getAttribute("email") == false ? "" : $http_params->_getAttribute("email");
        $password = $http_params->_getAttribute("password") == false ? "" : $http_params->_getAttribute("password");

        if ($username == "" || $email == "" || $password == ""){
            return $json
                ->_add("status", "failed")
                ->_add("message", "wrong data provided.")
            ->_encode();
        }

        if (UserInstance::_isLoggedIn()){
            return $json
                ->_add("status", "failed")
                ->_add("message", "can not register while being already logged in.")
            ->_encode();
        }

        $gateway = new RegisterAuthGateway($username, $email, $password);

        if (!$gateway->_tryAuth()){
            return $json
                ->_add("status", "failed")
                ->_add("message", $gateway->_getStatus())
            ->_encode();
        }

        return $json
            ->_add("status", "success")
            ->_add("message", $gateway->_getStatus())
        ->_encode();
    }



    /**
     * Logout API route
     *
     * [@method] => POST
     * [@route] => /api/v1/auth/logout
     */
    public function logout(){
        $gateway = new LogoutAuthGateway();
        $json = new JsonResponse();

        if (!$gateway->_tryAuth()){
            return $json
                ->_add("status", "failed")
                ->_add("message", $gateway->_getStatus())
            ->_encode();
        }

        return $json
            ->_add("status", "success")
            ->_add("message", $gateway->_getStatus())
        ->_encode();
    }
}