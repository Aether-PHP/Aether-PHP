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
 *  Built from scratch. No bloat. POO Embedded.
 *
 *  @author: dawnl3ss (Alex') ©2025 — All rights reserved
 *  Source available • Commercial license required for redistribution
 *  → github.com/dawnl3ss/Aether-PHP
 *
*/
declare(strict_types=1);

namespace Aether\Database\Drivers\List;

use Aether\Database\Drivers\DatabaseDriver;
use Aether\Database\Drivers\DatabaseDriverEnum;
use PDO;


final class DatabaseMySQLDriver extends DatabaseDriver {


    /** @var PDO $_conn */
    private PDO $_conn;

    public function __construct(){
        parent::__construct(DatabaseDriverEnum::MYSQL);
    }

    /**
     * @return DatabaseDriver
     */
    public function _connect() : self {
        $this->_conn = new PDO("mysql:dbname={$this->_database};host={$this->_getHost()}", $this->_getIds()->_getLogin(), $this->_getIds()->_getPasskey(), [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        return $this;
    }


    /**
     * @param string $database
     *
     * @return DatabaseMySQLDriver
     */
    public function _database(string $database) : self {
        parent::_database($database);
        $this->_connect();
        return $this;
    }

    /**
     * @param $value
     *
     * @return int
     */
    private function _detectPdoType($value) : int {
        return match (true){
            is_int($value) => PDO::PARAM_INT,
            is_bool($value) => PDO::PARAM_BOOL,
            is_null($value) => PDO::PARAM_NULL,
            default => PDO::PARAM_STR,
        };
    }

    /**
     * @param string $query
     * @param array $params
     *
     * @return mixed
     */
    public function _query(string $query, array $params = []) : mixed {
        $stmt = $this->_conn->prepare($query);

        foreach ($params as $key => $value){
            $paramKey = str_starts_with($key, ':') ? $key : ':' . $key;
            $stmt->bindValue($paramKey, $value, $this->_detectPdoType($value));
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param string $_string
     *
     * @return string
     */
    public function _escape(string $_string) : string { return '`' . str_replace('`', '``', $_string) . '`'; }


    /**
     * @return array
     */
    public function _dump() : array { return []; }
}