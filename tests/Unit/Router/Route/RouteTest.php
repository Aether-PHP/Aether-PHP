<?php
declare(strict_types=1);

namespace Tests\Unit\Router\Route;

use Aether\Router\Route\Route;
use PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase {

    public function testRouteStoresAllGivenValues(): void {
        $callable = static fn() => 'ok';
        $middlewares = ['auth', 'csrf'];

        $route = new Route('GET', '/users', $callable, $middlewares);

        $this->assertSame('GET', $route->_getMethod());
        $this->assertSame('/users', $route->_getRoute());
        $this->assertSame($callable, $route->_getCallable());
        $this->assertSame($middlewares, $route->_getMiddlewares());
    }
}
