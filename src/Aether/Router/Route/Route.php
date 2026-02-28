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
 *  Built from scratch. No bloat. POO Embedded.
 *
 *  @author: dawnl3ss (Alex') ©2025 — All rights reserved
 *  Source available • Commercial license required for redistribution
 *  → github.com/dawnl3ss/Aether-PHP
 *
*/
declare(strict_types=1);

namespace Aether\Router\Route;


class Route implements RouteInterface {

    /** @var string $_method */
    private string $_method;

    /** @var string $_route */
    private string $_route;

    /** @var $_callable */
    private $_callable;

    /** @var array $_middlewares */
    private array $_middlewares;


    public function __construct(string $_method, string $_route, $_callable, array $_middlewares){
        $this->_method = $_method;
        $this->_route = $_route;
        $this->_callable = $_callable;
        $this->_middlewares = $_middlewares;
    }

    /**
     * @return string
     */
    public function _getMethod() : string { return $this->_method; }

    /**
     * @return string
     */
    public function _getRoute() : string {  return $this->_route; }

    /**
     * @return
     */
    public function _getCallable() { return $this->_callable; }

    /**
     * @return array
     */
    public function _getMiddlewares() : array { return $this->_middlewares; }

}