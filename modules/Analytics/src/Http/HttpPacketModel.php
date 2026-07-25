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

namespace Aether\Modules\Analytics\Http;


final class HttpPacketModel {

    /** @var string $_protocol */
    private string $_protocol;

    /** @var string $_domain */
    private string $_domain;

    /** @var string $_route */
    private string $_route;

    /** @var string $_httpMethod */
    private string $_httpMethod;

    /** @var mixed $_httpData */
    private mixed $_httpData;

    /** @var string $_address */
    private string $_address;

    /** @var string $_userAgent */
    private string $_userAgent;

    /** @var int $_timestamp */
    private int $_timestamp;


    public function __construct(string $_protocol, string $_domain, string $_route, string $_httpMethod, mixed $_httpData){
        $this->_protocol = $_protocol;
        $this->_domain = $_domain;
        $this->_route = $_route;
        $this->_httpMethod = $_httpMethod;
        $this->_httpData = $_httpData;
        $this->_address = $_SERVER['REMOTE_ADDR'];
        $this->_userAgent = $_SERVER['HTTP_USER_AGENT'];
        $this->_timestamp = time();
    }

    /**
     * @return string
     */
    public function _craftUrl() : string {
        return $this->_getProtocol() . "://" . $this->_getDomain() . $this->_getRoute();
    }

    /**
     * @return string
     */
    public function _getProtocol() : string { return $this->_protocol; }

    /**
     * @return string
     */
    public function _getDomain() : string { return $this->_domain; }

    /**
     * @return string
     */
    public function _getRoute() : string { return $this->_route; }

    /**
     * @return string
     */
    public function _getHttpMethod() : string { return $this->_httpMethod; }

    /**
     * @return mixed
     */
    public function _getHttpData() : mixed { return $this->_httpData; }

    /**
     * @return string
     */
    public function _getAddress() : string { return $this->_address; }

    /**
     * @return string
     */
    public function _getUserAgent() : string { return $this->_userAgent; }

    /**
     * @return int
     */
    public function _getTimestamp() : int { return $this->_timestamp; }

    /**
     * @return HttpPacketModel
     */
    public static function _build() : self {
        return new self(
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http',
            $_SERVER['HTTP_HOST'] ?? '',
            parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH),
            $_SERVER['REQUEST_METHOD'] ?? 'GET',
            match($_SERVER['REQUEST_METHOD'] ?? 'GET'){
                'GET' => json_encode($_GET),
                'POST', 'PUT', 'DELETE', 'PATCH' => file_get_contents('php://input'),
                default  => null,
            }
        );
    }

}