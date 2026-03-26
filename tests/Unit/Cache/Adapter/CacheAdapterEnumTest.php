<?php
declare(strict_types=1);

namespace Tests\Unit\Cache\Adapter;

use Aether\Cache\Adapter\Apcu;
use Aether\Cache\Adapter\CacheAdapterEnum;
use Aether\Cache\Adapter\Files;
use PHPUnit\Framework\TestCase;

final class CacheAdapterEnumTest extends TestCase {

    public function testFilesAdapterFactoryReturnsFilesImplementation(): void {
        $adapter = CacheAdapterEnum::FILES->_make();
        $this->assertInstanceOf(Files::class, $adapter);
    }

    public function testApcuAdapterFactoryReturnsApcuImplementation(): void {
        $adapter = CacheAdapterEnum::APCU->_make();
        $this->assertInstanceOf(Apcu::class, $adapter);
    }

    public function testMemcachedFallbackReturnsApcuImplementation(): void {
        $adapter = CacheAdapterEnum::MEMCACHED->_make();
        $this->assertInstanceOf(Apcu::class, $adapter);
    }
}
