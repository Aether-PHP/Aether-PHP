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

namespace Aether\Session\Security;

use Aether\Security\Token\HmacSignature;


abstract class SessionSecurityLayer {

    /**
     * @param string $_key
     *
     * @return mixed
     */
    protected static function _decodeNode(string $_key) : mixed {
        if (!isset($_SESSION[$_key]))
            return null;

        $b64decoded = base64_decode($_SESSION[$_key]);
        [$rawData, $hmacSignStr] = explode(':::', $b64decoded, 2) + [1 => ''];

        if (!HmacSignature::_hmacEquals($hmacSignStr, $rawData))
            return null;

        $data = json_decode($rawData, true);

        if (json_last_error() !== JSON_ERROR_NONE)
            return $rawData;

        return $data;
    }

    /**
     * @param string $_key
     * @param mixed $_dat
     *
     * @return string
     */
    protected static function _encodeNode(string $_key, mixed $_dat) : string {
        if (is_array($_dat))
            $_dat = json_encode($_dat);

        return base64_encode($_dat . ":::" . hash_hmac('sha256', $_dat, $_ENV["SESSION_HMAC"]));
    }
}