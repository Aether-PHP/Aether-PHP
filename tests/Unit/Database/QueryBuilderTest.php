<?php
declare(strict_types=1);

namespace Tests\Unit\Database;

use Aether\Database\Drivers\DatabaseDriver;
use Aether\Database\Drivers\DatabaseDriverEnum;
use Aether\Database\QueryBuilder;
use PHPUnit\Framework\TestCase;

final class QueryBuilderTest extends TestCase {

    public function testSelectBuildsQueryWithWhereOrderByAndLimit(): void {
        $driver = new FakeDatabaseDriver();
        $driver->nextResult = [['id' => 1]];
        $queryBuilder = $this->makeQueryBuilder($driver);

        $result = $queryBuilder
            ->_table('users')
            ->_select('id', 'email')
            ->_where('email', 'hello@aether.test')
            ->_orderBy('id', 'DESC')
            ->_limit(10)
            ->_send();

        $this->assertSame([['id' => 1]], $result);
        $this->assertCount(1, $driver->queries);
        $this->assertSame(
            'SELECT id, email FROM users WHERE email = :email ORDER BY id DESC LIMIT 10',
            $driver->queries[0]['query']
        );
        $this->assertSame(['email' => 'hello@aether.test'], $driver->queries[0]['params']);
    }

    public function testInsertWithoutValuesReturnsNull(): void {
        $driver = new FakeDatabaseDriver();
        $queryBuilder = $this->makeQueryBuilder($driver);

        $result = $queryBuilder
            ->_table('users')
            ->_send();

        $this->assertNull($result);
        $this->assertCount(0, $driver->queries);
    }

    public function testUnsafeTableNameIsRejected(): void {
        $driver = new FakeDatabaseDriver();
        $queryBuilder = $this->makeQueryBuilder($driver);

        $result = $queryBuilder
            ->_table('users;DROP TABLE users')
            ->_select('id')
            ->_send();

        $this->assertNull($result);
        $this->assertCount(0, $driver->queries);
    }

    public function testWherePlaceholderSanitizesDotInColumnName(): void {
        $driver = new FakeDatabaseDriver();
        $driver->nextResult = [['id' => 1]];
        $queryBuilder = $this->makeQueryBuilder($driver);

        $queryBuilder
            ->_table('users')
            ->_select('id')
            ->_where('users.email', 'x@y.test')
            ->_send();

        $this->assertSame(
            'SELECT id FROM users WHERE users.email = :users_email',
            $driver->queries[0]['query']
        );
        $this->assertSame(['users_email' => 'x@y.test'], $driver->queries[0]['params']);
    }

    public function testExistReturnsBooleanFromDriverResult(): void {
        $driver = new FakeDatabaseDriver();
        $queryBuilder = $this->makeQueryBuilder($driver);

        $driver->nextResult = [['1' => 1]];
        $exists = $queryBuilder
            ->_table('users')
            ->_exist()
            ->_where('id', 5)
            ->_send();

        $driver->nextResult = [];
        $missing = $queryBuilder
            ->_table('users')
            ->_exist()
            ->_where('id', 999)
            ->_send();

        $this->assertTrue($exists);
        $this->assertFalse($missing);
    }

    public function testCountReturnsIntegerValue(): void {
        $driver = new FakeDatabaseDriver();
        $driver->nextResult = [['_count' => '7']];
        $queryBuilder = $this->makeQueryBuilder($driver);

        $count = $queryBuilder
            ->_table('users')
            ->_count('id')
            ->_send();

        $this->assertSame(7, $count);
    }

    private function makeQueryBuilder(FakeDatabaseDriver $driver): QueryBuilder {
        return new class('unit_db', $driver) extends QueryBuilder {
        };
    }
}

final class FakeDatabaseDriver extends DatabaseDriver {

    /** @var array<int, array{query: string, params: array}> */
    public array $queries = [];

    public mixed $nextResult = [];

    public function __construct() {
        parent::__construct(DatabaseDriverEnum::MYSQL);
    }

    public function _database(string $database): self {
        $this->_database = $database;
        return $this;
    }

    public function _connect(): DatabaseDriver {
        return $this;
    }

    public function _query(string $query, array $params): mixed {
        $this->queries[] = [
            'query' => $query,
            'params' => $params
        ];
        return $this->nextResult;
    }

    public function _escape(string $_string): string {
        return $_string;
    }

    public function _dump(): array {
        return [];
    }
}
