<?php
declare(strict_types=1);

namespace Tests\Unit\IO\Parser;

use Aether\IO\Parser\EnvParser;
use PHPUnit\Framework\TestCase;

final class EnvParserTest extends TestCase {

    public function testEncodeArrayToEnvString(): void {
        $parser = new EnvParser();

        $encoded = $parser->_encode([
            'APP_ENV' => 'test',
            'FLAGS' => ['a' => 1]
        ]);

        $this->assertStringContainsString('APP_ENV=test', $encoded);
        $this->assertStringContainsString('FLAGS={"a":1}', $encoded);
        $this->assertStringEndsWith(PHP_EOL, $encoded);
    }

    public function testEncodeReturnsEmptyStringForNonArrayInput(): void {
        $parser = new EnvParser();
        $this->assertSame('', $parser->_encode('invalid'));
    }

    public function testDecodeParsesAndTrimsValues(): void {
        $parser = new EnvParser();

        $decoded = $parser->_decode(" APP_ENV = \"test\" \nEMPTY=\n#COMMENT\nTOKEN='abc' \n");

        $this->assertSame('test', $decoded['APP_ENV']);
        $this->assertSame('', $decoded['EMPTY']);
        $this->assertSame('abc', $decoded['TOKEN']);
        $this->assertArrayNotHasKey('#COMMENT', $decoded);
    }
}
