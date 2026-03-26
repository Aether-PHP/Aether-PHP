<?php
declare(strict_types=1);

namespace Tests\Unit\Http\Response\Format;

use Aether\Http\Response\Format\HttpResponseFormatEnum;
use Aether\Http\Response\Format\HttpResponseFormatHtml;
use Aether\Http\Response\Format\HttpResponseFormatJson;
use Aether\Http\Response\Format\HttpResponseFormatPdf;
use Aether\Http\Response\Format\HttpResponseFormatText;
use Aether\Http\Response\Format\HttpResponseFormatXml;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class HttpResponseFormatEnumTest extends TestCase {

    public static function formatProvider(): array {
        return [
            [HttpResponseFormatEnum::JSON, HttpResponseFormatJson::class, 'Content-Type: application/json; charset=UTF-8'],
            [HttpResponseFormatEnum::HTML, HttpResponseFormatHtml::class, 'Content-Type: text/html; charset=UTF-8'],
            [HttpResponseFormatEnum::XML, HttpResponseFormatXml::class, 'Content-Type: application/xml; charset=UTF-8'],
            [HttpResponseFormatEnum::TEXT, HttpResponseFormatText::class, 'Content-Type: text/plain; charset=UTF-8'],
            [HttpResponseFormatEnum::PDF, HttpResponseFormatPdf::class, 'Content-Type: application/pdf'],
        ];
    }

    #[DataProvider('formatProvider')]
    public function testMakeReturnsCorrectFormatter(HttpResponseFormatEnum $format, string $expectedClass, string $expectedHeader): void {
        $instance = $format->_make();

        $this->assertInstanceOf($expectedClass, $instance);
        $this->assertSame($format->value, $format->_get());
        $this->assertSame($format->value, $instance->_getName());
        $this->assertSame($expectedHeader, $instance->_getHeader());
    }
}
