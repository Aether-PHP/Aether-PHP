<?php
declare(strict_types=1);

namespace Tests\Unit\Enum;

use Aether\Auth\User\Permission\PermissionEnum;
use Aether\IO\IOTypeEnum;
use PHPUnit\Framework\TestCase;

final class BasicEnumsTest extends TestCase {

    public function testPermissionEnumValueIsStable(): void {
        $this->assertSame('PERM.AETHER.ADMIN', PermissionEnum::PERM_ADMIN->value);
    }

    public function testIoTypeEnumContainsExpectedFormats(): void {
        $values = array_map(static fn(IOTypeEnum $type): string => $type->value, IOTypeEnum::cases());

        $this->assertContains('text', $values);
        $this->assertContains('json', $values);
        $this->assertContains('env', $values);
        $this->assertContains('other', $values);
    }
}
