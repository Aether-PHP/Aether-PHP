<?php
declare(strict_types=1);

namespace Tests\Unit\Http;

use Aether\Http\HttpParameterTypeEnum;
use Aether\Http\HttpParameterUnpacker;
use PHPUnit\Framework\TestCase;

final class HttpParameterUnpackerTest extends TestCase {

    private array $previousPost = [];

    protected function setUp(): void {
        $this->previousPost = $_POST;
    }

    protected function tearDown(): void {
        $_POST = $this->previousPost;
    }

    public function testFormInputUnpackerReadsPostValues(): void {
        $_POST = ['email' => 'hello@aether.test'];
        $unpacker = new HttpParameterUnpacker(HttpParameterTypeEnum::FORM_INPUT);

        $this->assertSame('hello@aether.test', $unpacker->_getAttribute('email'));
        $this->assertFalse($unpacker->_getAttribute('missing'));
    }

    public function testEnumFactoryReturnsUnpackerForFormInput(): void {
        $_POST = ['id' => '42'];
        $unpacker = HttpParameterTypeEnum::FORM_INPUT->_make();

        $this->assertInstanceOf(HttpParameterUnpacker::class, $unpacker);
        $this->assertSame('42', $unpacker->_getAttribute('id'));
    }
}
