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
 *                      The divine secure, lightweight PHP framework
 *                  < 1 Mo • Zero dependencies • Pure PHP 8.3+
 *
 *  Built from scratch. No bloat. POO Embedded.
 *
 *  @author: dawnl3ss (Alex') ©2026 — All rights reserved
 *  Source available • Commercial license required for redistribution
 *  → https://github.com/Aether-PHP/Aether-PHP
 *
*/
declare(strict_types=1);

namespace Aether\Http\Request;

use Aether\Http\Methods\HttpMethod;
use Aether\Http\Methods\HttpMethodEnum;
use Aether\Http\Response\HttpResponse;
use Aether\Http\Response\Format\HttpResponseFormatEnum;


class HttpRequest implements RequestInterface {

    /** @var string $_destination */
    private string $_destination;

    /** @var HttpMethod $_method */
    private HttpMethod $_method;

    /** @var resource|null $_curl */
    private mixed $_curl = null;


    public function  __construct(string $_destination, HttpMethod $_method){
        $this->_destination = $_destination;
        $this->_method = $_method;
        $this->_init();
    }

    /**
     * @param string $_host
     *
     * @return bool
     */
    private static function _isSafeHost(string $_host) : bool {
        $host = trim($_host);
        if ($host === "")
            return false;

        $host = strtolower($host);
        if ($host === "localhost")
            return false;

        # - Direct IP check
        if (filter_var($host, FILTER_VALIDATE_IP)){
            return filter_var(
                $host,
                FILTER_VALIDATE_IP,
                FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
            ) !== false;
        }

        # - DNS resolve and re-check
        $ip = gethostbyname($host);
        if ($ip === $host || !filter_var($ip, FILTER_VALIDATE_IP))
            return false;

        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) !== false;
    }

    /**
     * @param string $_url
     *
     * @return bool
     */
    private static function _isSafeUrl(string $_url) : bool {
        $parts = parse_url($_url);
        if ($parts === false || empty($parts['scheme']) || empty($parts['host']))
            return false;

        $scheme = strtolower((string) $parts['scheme']);
        if (!in_array($scheme, [ "http", "https" ]))
            return false;

        # - Allow private hosts only if explicitely enabled (use with caution)
        if (($_ENV['HTTP_ALLOW_PRIVATE'] ?? "0") === "1")
            return true;

        return self::_isSafeHost((string) $parts['host']);
    }

    private function _init() : void {
        $this->_curl = curl_init();

        # - SSRF hardening (http/https only, no private/reserved by default)
        if (!self::_isSafeUrl($this->_destination)){
            $this->_curl = null;
            return;
        }

        curl_setopt_array($this->_curl, [
            CURLOPT_URL => $this->_destination,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_FOLLOWLOCATION => (($_ENV['HTTP_FOLLOW_REDIRECTS'] ?? "0") === "1"),
            CURLOPT_TIMEOUT => 10,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
        ]);

        match ($this->_getMethod()->_getName()){
            HttpMethodEnum::GET->value => curl_setopt($this->_curl, CURLOPT_HTTPGET, true),
            HttpMethodEnum::POST->value => curl_setopt($this->_curl, CURLOPT_POST, true),
            HttpMethodEnum::PUT->value,
            HttpMethodEnum::DELETE->value => curl_setopt($this->_curl, CURLOPT_CUSTOMREQUEST, $this->_getMethod()->_getName()),
        };
    }

    /**
     * @return string
     */
    public function _getDestination() : string { return $this->_destination; }

    /**
     * @return HttpMethod
     */
    public function _getMethod() : HttpMethod { return $this->_method; }

    /**
     * @return HttpResponse|null
     */
    public function _send() : ?HttpResponse {
        if (!$this->_curl)
            return null;

        $response = curl_exec($this->_curl);

        if (!$response){
            curl_close($this->_curl);
            return null;
        }

        $code = curl_getinfo($this->_curl, CURLINFO_HTTP_CODE);
        $hSize = curl_getinfo($this->_curl, CURLINFO_HEADER_SIZE);

        $headers = substr($response, 0, $hSize);
        $body = substr($response, $hSize);

        curl_close($this->_curl);

        $method = HttpMethodEnum::from($this->_getMethod()->_getName());

        # - For now we keep raw body (TEXT). Headers are available in $headers if needed later.
        return new HttpResponse(
            HttpResponseFormatEnum::TEXT,
            $body,
            (int) $code,
            $this->_getDestination(),
            $method
        );
    }
}