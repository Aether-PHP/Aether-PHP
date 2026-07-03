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

namespace Aether\Modules\Analytics\Maths;

use Aether\Modules\Analytics\Analytics;
use Aether\Modules\Analytics\Provider\LogProvider;


final class Statistics {

    /**
     * @return int
     */
    public static function _getAllVisitorCount() : int {
        $provider = new LogProvider(Analytics::$_path);
        $dat = $provider->_retrieve('', '');
        return is_null($dat) ? 0 : count($dat);
    }

    /**
     * @return int
     */
    public static function _getUniqueVisitorCount() : int {
        $provider = new LogProvider(Analytics::$_path);
        $dat = $provider->_distinct("ip_addr");
        return is_null($dat) ? 0 : count($dat);
    }

    /**
     * @param string $_route
     *
     * @return int
     */
    public static function _getPageVisitorCount(string $_route) : int {
        $provider = new LogProvider(Analytics::$_path);
        $dat = $provider->_retrieve("route", $_route);
        return is_null($dat) ? 0 : count($dat);
    }

    /**
     * @param string $_route
     *
     * @return int
     */
    public static function _getUniquePageVisitorCount(string $_route) : int {
        $provider = new LogProvider(Analytics::$_path);
        $dat = $provider->_distinctRetrieve("ip_addr", "route", "'$_route'");
        return is_null($dat) ? 0 : count($dat);
    }
}