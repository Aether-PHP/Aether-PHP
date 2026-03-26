<?php
declare(strict_types=1);

namespace Tests\Unit\Security\Token;

use Aether\Security\Token\HmacSignature;
use PHPUnit\Framework\TestCase;

final class HmacSignatureTest extends TestCase {

    private string|null $previousSecret = null;

    protected function setUp(): void {
        $this->previousSecret = $_ENV['SESSION_HMAC'] ?? null;
        $_ENV['SESSION_HMAC'] = 'unit-test-secret';
    }

    protected function tearDown(): void {
        if ($this->previousSecret === null) {
            unset($_ENV['SESSION_HMAC']);
            return;
        }

        $_ENV['SESSION_HMAC'] = $this->previousSecret;
    }

    public function testHmacEqualsReturnsTrueForValidSignature(): void {
        $payload = '{"sub":"u_1"}';
        $signature = hash_hmac('sha256', $payload, 'unit-test-secret');

        $this->assertTrue(HmacSignature::_hmacEquals($signature, $payload));
    }

    public function testHmacEqualsReturnsFalseForInvalidSignature(): void {
        $this->assertFalse(HmacSignature::_hmacEquals('invalid-signature', '{"sub":"u_1"}'));
    }
}
