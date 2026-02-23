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

namespace Aether\Service\Hub;

use Aether\Http\HttpParameterTypeEnum;
use Aether\Http\HttpParameterUnpacker;
use Aether\Http\Methods\HttpMethod;
use Aether\Http\Methods\HttpMethodEnum;
use Aether\Http\Request\HttpRequest;
use Aether\Http\RequestFactory;
use Aether\Http\ResponseFactory;


final class HttpServiceHub {

    /**
     * @param HttpMethodEnum $_method
     * @param string $_destination
     *
     * @return HttpRequest
     */
    public function _request(HttpMethodEnum $_method, string $_destination) : HttpRequest {
        return RequestFactory::_create($_method, $_destination);
    }

    /**
     * @return ResponseFactory
     */
    public function _response() : ResponseFactory {
        return new ResponseFactory();
    }

    /**
     * @param HttpMethodEnum $_method
     *
     * @return HttpMethod
     */
    public function _method(HttpMethodEnum $_method) : HttpMethod {
        return $_method->_make();
    }

    /**
     * @param HttpParameterTypeEnum $_parameterType
     *
     * @return HttpParameterUnpacker
     */
    public function _parameters(HttpParameterTypeEnum $_parameterType) : HttpParameterUnpacker {
        return $_parameterType->_make();
    }
}