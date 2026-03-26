<?php
declare(strict_types=1);

namespace Tests\Unit\Http\Methods;

use Aether\Http\Methods\HttpDelete;
use Aether\Http\Methods\HttpGet;
use Aether\Http\Methods\HttpMethodEnum;
use Aether\Http\Methods\HttpPost;
use Aether\Http\Methods\HttpPut;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class HttpMethodEnumTest extends TestCase {

    public static function methodProvider(): array {
        return [
            [HttpMethodEnum::GET, HttpGet::class, true, true, false, false],
            [HttpMethodEnum::POST, HttpPost::class, false, false, true, true],
            [HttpMethodEnum::PUT, HttpPut::class, false, false, true, true],
            [HttpMethodEnum::DELETE, HttpDelete::class, false, false, true, false],
        ];
    }

    #[DataProvider('methodProvider')]
    public function testMakeReturnsExpectedMethodObject(
        HttpMethodEnum $method,
        string $expectedClass,
        bool $isSafe,
        bool $isCacheable,
        bool $allowsBody,
        bool $requiresBody
    ): void {
        $instance = $method->_make();

        $this->assertInstanceOf($expectedClass, $instance);
        $this->assertSame($method->value, $method->_get());
        $this->assertSame($method->value, $instance->_getName());
        $this->assertSame($isSafe, $instance->_isSafe());
        $this->assertSame($isCacheable, $instance->_isCacheable());
        $this->assertSame($allowsBody, $instance->_allowsBody());
        $this->assertSame($requiresBody, $instance->_requiresBody());
        $this->assertSame($method->value, (string) $instance);
    }
}
