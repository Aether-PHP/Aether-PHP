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

namespace Aether\Auth\Controller;

use Aether\Auth\Gateway\LoginAuthGateway;
use Aether\Auth\Gateway\LogoutAuthGateway;
use Aether\Auth\Gateway\RegisterAuthGateway;
use Aether\Http\HttpParameterTypeEnum;
use Aether\Router\Controller\Controller;
use Aether\Security\UserInputValidatorTrait;


/**
 * [@base] => /api/v1/auth
 */
class AuthApiController extends Controller {
    use UserInputValidatorTrait;

    /**
     * Login API route
     *
     * [@method] => POST
     * [@route] => /login
     */
    public function login(){
        $http_params = Aether()->_http()->_parameters(HttpParameterTypeEnum::PHP_INPUT);

        $email = $http_params->_getAttribute("email") == false ? "" : $http_params->_getAttribute("email");
        $password = $http_params->_getAttribute("password") == false ? "" : $http_params->_getAttribute("password");

        if ($email == "" || $password == ""){
            return Aether()->_http()->_response()->_json([
                "status" => "error",
                "message" => "Credentials should not be empty."
            ], 404)->_send();
        }

        if (Aether()->_session()->_auth()->_isLoggedIn()){
            return Aether()->_http()->_response()->_json([
                "status" => "error",
                "message" => "user already logged-in."
            ], 404)->_send();
        }

        $gateway = new LoginAuthGateway($email, $password);

        if (!$gateway->_tryAuth()){
            return Aether()->_http()->_response()->_json([
                "status" => "error",
                "message" => $gateway->_getStatus()
            ], 404)->_send();
        }

        return Aether()->_http()->_response()->_json([
            "status" => "success",
            "message" => $gateway->_getStatus()
        ], 200)->_send();
    }



    /**
     * Register API route
     *
     * [@method] => POST
     * [@route] => /register
     */
    public function register(){
        $http_params = Aether()->_http()->_parameters(HttpParameterTypeEnum::PHP_INPUT);

        $username = $this->_sanitizeInput($http_params->_getAttribute("username") == false ? "" : $http_params->_getAttribute("username"));
        $email = $http_params->_getAttribute("email") == false ? "" : $http_params->_getAttribute("email");
        $password = $http_params->_getAttribute("password") == false ? "" : $http_params->_getAttribute("password");

        if ($username == "" || $email == "" || $password == ""){
            return Aether()->_http()->_response()->_json([
                "status" => "error",
                "message" => "Credentials should not be empty."
            ], 404)->_send();
        }

        if (Aether()->_session()->_auth()->_isLoggedIn()){
            return Aether()->_http()->_response()->_json([
                "status" => "error",
                "message" => "Can not register while being already logged in."
            ], 404)->_send();
        }

        $gateway = new RegisterAuthGateway($username, $email, $password);

        if (!$gateway->_tryAuth()){
            return Aether()->_http()->_response()->_json([
                "status" => "error",
                "message" => $gateway->_getStatus()
            ], 404)->_send();
        }

        return Aether()->_http()->_response()->_json([
            "status" => "success",
            "message" => $gateway->_getStatus()
        ], 200)->_send();
    }



    /**
     * Logout API route
     *
     * [@method] => POST
     * [@route] => /logout
     */
    public function logout(){
        $gateway = new LogoutAuthGateway();

        if (!$gateway->_tryAuth()){
            return Aether()->_http()->_response()->_json([
                "status" => "error",
                "message" => $gateway->_getStatus()
            ], 404)->_send();
        }

        return Aether()->_http()->_response()->_json([
            "status" => "success",
            "message" => $gateway->_getStatus()
        ], 200)->_send();
    }
}