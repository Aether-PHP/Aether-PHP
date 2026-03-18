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

namespace Aether\Modules\Analytics;

use Aether\Modules\AetherModule;
use Aether\Modules\Analytics\Http\HttpPacketModel;
use Aether\Modules\Analytics\Provider\LogProvider;


class Analytics extends AetherModule {

    /** @var string $_path */
    private static string $_path = __DIR__ . "/../ressources/db.sqlite";

    public function __construct(){
        parent::__construct("Analytics Module", 1.0, "Permits you to have insightful http analytics.");
    }

    public function _onLoad() : void {
        $httpPacket = HttpPacketModel::_build();
        $provider = new LogProvider(self::$_path);
        $this->_initDatabase(self::$_path);
        $provider->_log($httpPacket);
    }

    /**
     * @param string $_path
     *
     * @return void
     */
    public function _initDatabase(string $_path) : void {
        Aether()->_db()->_sqlite($_path)->_raw("
            CREATE TABLE IF NOT EXISTS analytics (
                ip_addr VARCHAR(12),
                user_agent TEXT NOT NULL,
                protocol VARCHAR(5) NOT NULL,
                domain VARCHAR(200) NOT NULL,
                route TEXT NOT NULL,
                http_method VARCHAR(6) NOT NULL,
                http_data TEXT,
                phptimestamp INT not null
            )"
        );
    }

    /**
     * @return AetherModule
     */
    public static function _make() : AetherModule {
        return new self();
    }
}