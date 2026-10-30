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

namespace Aether\Http\Router\Controller;

use Aether\Exception\RouterControllerException;
use Aether\Http\Router\Router;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;


final class ControllerGateway {

    private const CACHE_KEY = "aether::routes::compiled//";
    private const CACHE_TTL = 3600;

    /**
     * Router => Controller Gateway | & API integration
     */
    public static function _link() : void {
        $router = new Router();
        $useCache = Aether()->_config()->_get("ENABLE_ROUTE_CACHING");

        if ($useCache && Aether()->_cache()->_apcu()->_has(self::CACHE_KEY)){
            # - Fetch from cache
            $routes = json_decode(Aether()->_cache()->_apcu()->_get(self::CACHE_KEY), true);

            foreach ($routes as $r){
                $router->_addRoute($r['method'], $r['route'], $r['callable'], $r['middlewares']);
            }
        } else {

            # - Scan for view controllers
            self::_scanForController(_root("app/App/Controller/*.php"), "App\Controller\\", $router);

            # - Scan for api controllers
            self::_scanForController(_root("app/App/Controller/Api/*.php"), "App\Controller\Api\\", $router);

            # - Cache the compiled route table
            if ($useCache){
                $compiled = [];

                foreach ($router->_routes as $method => $routes){
                    foreach ($routes as $route){
                        $compiled[] = [
                            'method' => $method,
                            'route' => $route->_getRoute(),
                            'callable' => $route->_getCallable(),
                            'middlewares' => $route->_getMiddlewares()
                        ];
                    }
                }

                Aether()->_cache()->_apcu()->_set(self::CACHE_KEY, json_encode($compiled), _ttl: self::CACHE_TTL);
            }
        }

        $router->_run();
    }

    /**
     * Scan the directory to attribute each route to the provided controller
     *
     * @param string $_dir
     * @param string $_namespace
     * @param Router $_router
     *
     * @return void
     * @throws RouterControllerException
     */
    private static function _scanForController(string $_dir, string $_namespace, Router $_router) : void {
        $controllerFiles = glob($_dir);

        foreach ($controllerFiles as $file){
            $class_name = $_namespace . pathinfo($file, PATHINFO_FILENAME);

            # - We use ReflectionClass to analyze each file
            try {
                $reflection = new ReflectionClass($class_name);
            } catch (ReflectionException $e){
                throw new RouterControllerException("[ControllerGateway] - ERROR - Cannot reflect class $class_name: " . $e->getMessage());
            }

            # - We allow developers to add a base path for their controller
            $base = $reflection->getAttributes() !== [] && $reflection->getAttributes()[0]->getName() === "Aether\Http\Router\Route\Base" ? $reflection->getAttributes()[0]->getArguments()[0] : "";
            $base = is_null($base) ? "" : $base;

            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method){
                $doc = $method->getAttributes();
                if (!isset($doc[0])) continue;

                if ($doc[0]->getName() !== "Aether\Http\Router\Route\Route")
                    continue;

                $args = $doc[0]->getArguments();

                # - We gather the 'Method' and 'Route' and 'Middlewares' from php attributes
                $method_type = $args[0];
                $route = $args[1];
                $middlewares = isset($args[2]) ? $args[2] : [];

                if (!$method_type || !$route)
                    throw new RouterControllerException("[ControllerGateway] - ERROR - Wrong PHP Attributes for {$class_name} Controller, method {$method->getName()}");

                $_router->_addRoute(strtoupper($method_type), $base . $route, "{$class_name}@{$method->getName()}", $middlewares);
            }
        }
    }

}
