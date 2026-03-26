<?php
declare(strict_types=1);

namespace Tests\Unit\Security;

use Aether\Security\PasswordHashingTrait;
use PHPUnit\Framework\TestCase;

final class PasswordHashingTraitTest extends TestCase {

    public function testHashAndCheckPassword(): void {
        $service = new class {
            use PasswordHashingTrait;
        };

        $hash = $service->_hashPassword('P@ssword123');

        $this->assertNotSame('P@ssword123', $hash);
        $this->assertTrue($service->_checkPassword('P@ssword123', $hash));
        $this->assertFalse($service->_checkPassword('wrong-password', $hash));
    }
}
