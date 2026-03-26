<?php
declare(strict_types=1);

namespace Tests\Unit\Database\Drivers;

use Aether\Database\Drivers\DatabaseDriver;
use Aether\Database\Drivers\DatabaseDriverEnum;
use Aether\Database\Drivers\List\DatabaseMySQLDriver;
use Aether\Database\Drivers\List\DatabaseSQLiteDriver;
use PHPUnit\Framework\TestCase;

final class DatabaseDriverEnumTest extends TestCase {

    public function testMysqlEnumBuildsMysqlDriver(): void {
        $driver = DatabaseDriverEnum::MYSQL->_get();

        $this->assertInstanceOf(DatabaseDriver::class, $driver);
        $this->assertInstanceOf(DatabaseMySQLDriver::class, $driver);
    }

    public function testSqliteEnumBuildsSqliteDriver(): void {
        $driver = DatabaseDriverEnum::SQLITE->_get();

        $this->assertInstanceOf(DatabaseDriver::class, $driver);
        $this->assertInstanceOf(DatabaseSQLiteDriver::class, $driver);
    }
}
