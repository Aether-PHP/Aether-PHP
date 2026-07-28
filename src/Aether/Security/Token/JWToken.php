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

namespace Aether\Security\Token;


final class JWToken {

    /** @var string ALGORITHM */
    private const ALGORITHM = 'sha256';

    /** @var string TOKEN_TYPE */
    private const TOKEN_TYPE = 'JWT';

    /** @var int DEFAULT_TTL — 1 hour */
    private const DEFAULT_TTL = 3600;


    /**
     * @param array $_payload
     * @param int|null $_ttl
     *
     * @return string
     */
    public static function _encode(array $_payload, ?int $_ttl = null) : string {
        $_ttl ??= (int) ($_ENV['JWT_TTL'] ?? self::DEFAULT_TTL);

        $header = self::_base64UrlEncode(json_encode([
            'alg' => 'HS256',
            'typ' => self::TOKEN_TYPE,
        ]));

        # - Registered claims injection
        $_payload['iat'] = time();
        $_payload['exp'] = time() + $_ttl;
        $_payload['nbf'] = time();

        $payload = self::_base64UrlEncode(json_encode($_payload));

        $signature = self::_sign($header . '.' . $payload);

        return $header . '.' . $payload . '.' . $signature;
    }

    /**
     * @param string $_token
     *
     * @return array|false
     */
    public static function _decode(string $_token) : array|false {
        $segments = explode('.', $_token);

        if (count($segments) !== 3)
            return false;

        [$headerB64, $payloadB64, $signatureB64] = $segments;

        # - Algorithm pinning : reject tokens with manipulated headers
        $header = json_decode(self::_base64UrlDecode($headerB64), true);

        if (!$header || ($header['alg'] ?? '') !== 'HS256')
            return false;

        # - Timing-safe signature verification
        $expectedSignature = self::_sign($headerB64 . '.' . $payloadB64);

        if (!hash_equals($expectedSignature, $signatureB64))
            return false;

        $payload = json_decode(self::_base64UrlDecode($payloadB64), true);

        if (!$payload || json_last_error() !== JSON_ERROR_NONE)
            return false;

        # - Temporal claims validation
        $now = time();

        if (isset($payload['exp']) && $now >= $payload['exp'])
            return false;

        if (isset($payload['nbf']) && $now < $payload['nbf'])
            return false;

        return $payload;
    }

    /**
     * @param string $_token
     *
     * @return bool
     */
    public static function _verify(string $_token) : bool {
        return self::_decode($_token) !== false;
    }

    /**
     * @param string $_token
     * @param int|null $_ttl
     *
     * @return string|false
     */
    public static function _refresh(string $_token, ?int $_ttl = null) : string|false {
        $payload = self::_decode($_token);

        if (!$payload)
            return false;

        # - Strip registered claims before re-encoding
        unset($payload['iat'], $payload['exp'], $payload['nbf']);

        return self::_encode($payload, $_ttl);
    }

    /**
     * @param string $_token
     *
     * @return array|false
     */
    public static function _getPayload(string $_token) : array|false {
        $segments = explode('.', $_token);

        if (count($segments) !== 3)
            return false;

        $payload = json_decode(self::_base64UrlDecode($segments[1]), true);

        if (!$payload || json_last_error() !== JSON_ERROR_NONE)
            return false;

        return $payload;
    }

    /**
     * @param string $_token
     *
     * @return int|false
     */
    public static function _expiresIn(string $_token) : int|false {
        $payload = self::_getPayload($_token);

        if (!$payload || !isset($payload['exp']))
            return false;

        $remaining = $payload['exp'] - time();

        return $remaining > 0 ? $remaining : 0;
    }

    /**
     * @param string $_data
     *
     * @return string
     */
    private static function _sign(string $_data) : string {
        $secret = $_ENV['JWT_SECRET'] ?? '';

        return self::_base64UrlEncode(
            hash_hmac(self::ALGORITHM, $_data, $secret, true)
        );
    }

    /**
     * @param string $_data
     *
     * @return string
     */
    private static function _base64UrlEncode(string $_data) : string {
        return rtrim(strtr(base64_encode($_data), '+/', '-_'), '=');
    }

    /**
     * @param string $_data
     *
     * @return string
     */
    private static function _base64UrlDecode(string $_data) : string {
        $remainder = strlen($_data) % 4;

        if ($remainder)
            $_data .= str_repeat('=', 4 - $remainder);

        return base64_decode(strtr($_data, '-_', '+/'));
    }

}
