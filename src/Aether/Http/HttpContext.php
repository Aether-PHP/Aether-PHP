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

namespace Aether\Http;

use Aether\Http\Methods\HttpMethod;
use Aether\Http\Methods\HttpMethodEnum;


final class HttpContext {

    /** @var string $_ipaddr */
    private string $_ipaddr;

    /** @var string $_useragent */
    private string $_useragent;

    /** @var HttpMethod $_method */
    private HttpMethod $_method;

    /** @var string $_route */
    private string $_route;


    public function __construct(){
        $this->_ipaddr = $_SERVER['REMOTE_ADDR'];
        $this->_useragent = $_SERVER['HTTP_USER_AGENT'];
        $this->_method = HttpMethodEnum::{$_SERVER['REQUEST_METHOD']}->_make();
        $this->_route = $_SERVER['REQUEST_URI'];
    }

    /**
     * @return string
     */
    public function _getIpaddr() : string { return $this->_ipaddr; }

    /**
     * @return string
     */
    public function _getUseragent() : string { return $this->_useragent; }

    /**
     * @return HttpMethod
     */
    public function _getMethod() : HttpMethod { return $this->_method; }

    /**
     * @return string
     */
    public function _getRoute() : string { return $this->_route; }
}