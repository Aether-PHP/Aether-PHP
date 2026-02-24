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

namespace Aether\Session;

use Aether\Session\Security\SessionSecurityLayer;


final class SessionHandler extends SessionSecurityLayer {

    /** @var ?Session $_session */
    private static ?Session $_session = null;


    /**
     * @return void
     */
    public static function _load() : void {
        ini_set('session.save_path', $_ENV['SESSION_FOLDER_PATH']);
        ini_set('session.cookie_lifetime', 60 * 60 * 24 * (int)$_ENV['COOKIE_SESSION_TTL']);
        ini_set('session.gc_maxlifetime', 60 * 60 * 24 * (int)$_ENV['COOKIE_SESSION_TTL']);
        session_start();

        if (is_null(self::$_session))
            self::$_session = new Session();

    }

    /**
     * @return Session|null
     */
    public static function _getSession() : ?Session { return self::$_session; }
}