<?php
declare(strict_types=1);

namespace Tests\Unit\IO\Parser;

use Aether\IO\Parser\JsonParser;
use PHPUnit\Framework\TestCase;

final class JsonParserTest extends TestCase {

    public function testEncodeUsesPrettyPrintAndUnicode(): void {
        $parser = new JsonParser();
        $encoded = $parser->_encode(['label' => 'été']);

        $this->assertStringContainsString('"label": "été"', $encoded);
        $this->assertStringEndsWith(PHP_EOL, $encoded);
    }

    public function testDecodeReturnsArrayForValidJson(): void {
        $parser = new JsonParser();
        $decoded = $parser->_decode('{"a":1,"b":"x"}');

        $this->assertSame(['a' => 1, 'b' => 'x'], $decoded);
    }

    public function testDecodeReturnsNullForInvalidJson(): void {
        $parser = new JsonParser();
        $this->assertNull($parser->_decode('{"a":}'));
    }
}
