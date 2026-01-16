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
 *  → github.com/dawnl3ss/Aether-PHP
 *
*/
declare(strict_types=1);

namespace Aether\Http;

use Aether\Http\Methods\HttpMethodEnum;
use Aether\Http\Response\Format\HttpResponseFormatEnum;
use Aether\Http\Response\HttpResponse;


class ResponseFactory {

    /**
     * @param string|array $_body
     * @param int $_code
     * @param string $_url
     * @param HttpMethodEnum $_method
     *
     * @return HttpResponse
     */
    public function _html(string|array $_body, int $_code, string $_url = "", HttpMethodEnum $_method = HttpMethodEnum::GET) : HttpResponse {
        return self::_create(HttpResponseFormatEnum::HTML, $_body, $_code, $_url, $_method);
    }

    /**
     * @param string|array $_body
     * @param int $_code
     * @param string $_url
     * @param HttpMethodEnum $_method
     *
     * @return HttpResponse
     */
    public function _json(string|array $_body, int $_code, string $_url = "", HttpMethodEnum $_method = HttpMethodEnum::GET) : HttpResponse {
        return self::_create(HttpResponseFormatEnum::JSON, $_body, $_code, $_url, $_method);
    }

    /**
     * @param string $_body
     * @param int $_code
     * @param string $_url
     * @param HttpMethodEnum $_method
     *
     * @return HttpResponse
     */
    public function _xml(string $_body, int $_code, string $_url = "", HttpMethodEnum $_method = HttpMethodEnum::GET) : HttpResponse {
        return self::_create(HttpResponseFormatEnum::XML, $_body, $_code, $_url, $_method);
    }

    /**
     * @param string $_body
     * @param int $_code
     * @param string $_url
     * @param HttpMethodEnum $_method
     *
     * @return HttpResponse
     */
    public function _text(string $_body, int $_code, string $_url = "", HttpMethodEnum $_method = HttpMethodEnum::GET) : HttpResponse {
        return self::_create(HttpResponseFormatEnum::TEXT, $_body, $_code, $_url, $_method);
    }

    /**
     * @param string $_body
     * @param int $_code
     * @param string $_url
     * @param HttpMethodEnum $_method
     *
     * @return HttpResponse
     */
    public function _pdf(string $_body, int $_code, string $_url = "", HttpMethodEnum $_method = HttpMethodEnum::GET) : HttpResponse {
        return self::_create(HttpResponseFormatEnum::PDF, $_body, $_code, $_url, $_method);
    }


    /**
     *  Create HTTP response instance.
     *
     * @param HttpResponseFormatEnum $_format
     * @param string|array $_body
     * @param int $_statusCode
     * @param string $_url
     * @param HttpMethodEnum $_method
     *
     * @return HttpResponse
     */
    public static function _create(HttpResponseFormatEnum $_format, string|array $_body, int $_statusCode, string $_url = "", HttpMethodEnum $_method = HttpMethodEnum::GET) : HttpResponse {
        return new HttpResponse($_format, $_body, $_statusCode, $_url, $_method);
    }
}