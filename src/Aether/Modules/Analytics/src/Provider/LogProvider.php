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

namespace Aether\Modules\Analytics\Provider;

use Aether\Aether;
use Aether\Database\DatabaseWrapper;
use Aether\Database\Drivers\DatabaseDriver;
use Aether\Database\Drivers\DatabaseDriverEnum;
use Aether\Modules\Analytics\Http\HttpPacketModel;


final class LogProvider {

    /** @var string $_logPath */
    private string $_logPath;

    /** @var DatabaseWrapper $_database */
    private DatabaseWrapper $_database;

    public function __construct(string $_logPath){
        $this->_logPath = $_logPath;
        $this->_database = Aether()->_db()->_sqlite($_logPath)->_table("analytics");
    }

    /**
     * @return string
     */
    public function _getLogPath(): string { return $this->_logPath; }

    /**
     * @return DatabaseWrapper
     */
    public function _getDatabase() : DatabaseWrapper { return $this->_database; }

    /**
     * Write logged data to the provided sqlite database.
     *
     * @param HttpPacketModel $_httpPacket
     *
     * @return void
     */
    public function _log(HttpPacketModel $_httpPacket) : void {
        $this->_getDatabase()
            ->_insert("ip_addr", $_httpPacket->_getAddress())
            ->_insert("user_agent", $_httpPacket->_getUserAgent())
            ->_insert("protocol", $_httpPacket->_getProtocol())
            ->_insert("domain", $_httpPacket->_getDomain())
            ->_insert("route", $_httpPacket->_getRoute())
            ->_insert("http_method", $_httpPacket->_getHttpMethod())
            ->_insert("http_data", $_httpPacket->_getHttpData())
            ->_insert("phptimestamp", $_httpPacket->_getTimestamp())
            ->_send();
    }

    /**
     * Retrieve data from the provided sqlite database.
     *
     * @param string $_address
     *
     * @return null|array
     */
    public function _retrieve(string $_key, string $_val) : ?array {
        $data = $this->_getDatabase()
            ->_select('*')
            ->_where($_key, $_val)
            ->_send();

        return $data;
    }

}