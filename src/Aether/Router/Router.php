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
 *  @author: dawnl3ss (Alex') ©2026 — All rights reserved
 *  Source available • Commercial license required for redistribution
 *  → https://github.com/Aether-PHP/Aether-PHP
 *
*/
declare(strict_types=1);

namespace Aether\Router;

use Aether\Exception\RouterControllerException;
use Aether\Middleware\Pipeline;
use Aether\Router\Route\Route;
use Aether\Security\UserInputValidatorTrait;


final class Router implements RouterInterface {
    use UserInputValidatorTrait;


    /** @var array[] $_routes */
    public $_routes = array(
        "GET" => [],
        "POST" => [],
        "DELETE" => [],
        "PUT" => [],
    );


    /**
     * @param string $method
     * @param string $route
     * @param $callable
     *
     * @return Router
     */
    public function _addRoute(string $method, string $route, $callable, array $middlewares) : Router {
        array_push($this->_routes[$method], new Route($method, $route, $callable, $middlewares));
        return $this;
    }


    /**
     * @return bool
     * @throws RouterControllerException
     */
    public function _run() : bool {
        $req_uri = $_SERVER['REQUEST_URI'];
        $req_method = strtoupper($_SERVER['REQUEST_METHOD']);

        if (!isset($this->_routes[$req_method]))
            return false;

        foreach ($this->_routes[$req_method] as $route){
            if ($route instanceof Route){

                # - Case 1 : URI == route - ex: uri:(/test) route:(/test)
                if (trim($req_uri, '/') == trim($route->_getRoute(), '/')){
                    header('HTTP/2 200 OK', true, 200);
                    $this->_execute($route);
                    return true;
                }

                # - Case 2 : URI contains params
                $path = preg_replace('#{([\w])+}#', '([^/]+)', trim($route->_getRoute(), '/'));
                $path_to_match = "#^$path$#";

                if (preg_match_all($path_to_match, trim($req_uri, '/'), $params)){
                    header('HTTP/2 200 OK', true, 200);
                    $this->_execute($route, $params);
                    return true;
                }
            }
        }

        header("HTTP/1.1 404 Not Found", true, 404);
        return false;
    }


    /**
     * @param Route $route
     * @param array|null $_params
     *
     * @return mixed
     * @throws RouterControllerException
     */
    private function _execute(Route $route, ?array $_params = []){
        $matches = [];
        $callable = $route->_getCallable();

        # - Middlewares wrapper
        Pipeline::_runForRoute($route->_getMiddlewares(), function () use ($_params, $matches, $callable){

            foreach($_params as $key => $value){
                if ($key != 0)
                    array_push($matches, $this->_sanitizeInput($value[0]));
            }

            if (is_callable($callable))
                return call_user_func_array($callable, $matches);

            if (is_string($callable))
                $callable = explode('@', $callable);

            if (!class_exists($callable[0]))
                throw new RouterControllerException("Class {$callable[0]} not found");

            $class = new $callable[0];

            if (!method_exists($class, $callable[1]))
                throw new RouterControllerException("Method {$callable[1]} not found in class {$callable[0]}");


            return call_user_func_array([$class, $callable[1]], $matches);
        });
    }
}