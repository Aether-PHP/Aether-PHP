<?php
declare(strict_types=1);

namespace Tests\Unit\Utils;

use Aether\Utils\IdsMap;
use PHPUnit\Framework\TestCase;

final class IdsMapTest extends TestCase {

    public function testGettersReturnConstructorValues(): void {
        $ids = new IdsMap('admin', 'super-secret');

        $this->assertSame('admin', $ids->_getLogin());
        $this->assertSame('super-secret', $ids->_getPasskey());
    }
}
