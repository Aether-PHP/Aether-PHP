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

namespace Aether\Router\Controller;

use Aether\Exception\RouterControllerException;
use Aether\Router\Router;
use \ReflectionClass;
use \ReflectionException;
use \ReflectionMethod;


final class ControllerGateway {

    /**
     * Router => Controller Gateway | & API integration
     */
    public static function _link() : void {
        $router = new Router();

        # - Scan for view controllers
        self::_scanForController(_root("app/App/Controller/*.php"),  "App\Controller\\", $router);

        # - Scan for api controllers
        self::_scanForController(_root("app/App/Controller/Api/*.php"),  "App\Controller\Api\\", $router);

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
            $base = $reflection->getDocComment() === false ? "" : self::_extractAnnotation($reflection->getDocComment(), 'base');
            $base = is_null($base) ? "" : $base;

            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method){
                $doc = $method->getDocComment();
                if (!$doc) continue;

                # - We gather the 'Method' and 'Route' and 'Middlewares' phpdoc annotations
                $method_type = self::_extractAnnotation($doc, 'method');
                $route = self::_extractAnnotation($doc, 'route');
                $middlewares = self::_extractAnnotation($doc, 'middlewares');

                if (!$method_type || !$route)
                    throw new RouterControllerException("[ControllerGateway] - ERROR - Wrong PHP Doc for {$class_name} Controller, method {$method->getName()}");

                if (!is_null($middlewares))
                    $_router->_addRoute(strtoupper($method_type), $base . $route, "{$class_name}@{$method->getName()}", explode(",", $middlewares));
                else
                    $_router->_addRoute(strtoupper($method_type), $base . $route, "{$class_name}@{$method->getName()}", []);
            }
        }
    }

    /**
     * Regexp to exract wanted data from phpdoc
     *
     * @param string $_docComment
     * @param string $_annotation
     *
     * @return string|null
     */
    private static function _extractAnnotation(string $_docComment, string $_annotation) : ?string {
        if (preg_match("/\\[@{$_annotation}\\]\\s*=>\\s*(\\S+)/", $_docComment, $matches))
            return $matches[1];

        return null;
    }
}
