<?php
declare(strict_types=1);

namespace Tests\Unit\Modules\AetherCLI\Cli;

use Aether\Modules\AetherCLI\Cli\CliColorEnum;
use PHPUnit\Framework\TestCase;

final class CliColorEnumTest extends TestCase {

    public function testPaintWrapsTextWithAnsiCodeAndReset(): void {
        $painted = CliColorEnum::FG_GREEN->_paint('OK');
        $this->assertSame("\e[32mOK\e[0m", $painted);
    }

    public function testWithCombinesCodesInOrder(): void {
        $combined = CliColorEnum::BOLD->_with(CliColorEnum::FG_RED, CliColorEnum::BG_WHITE);
        $this->assertSame("\e[1;31;47m", $combined);
    }

    public function testCodeReturnsSingleAnsiCode(): void {
        $this->assertSame("\e[93m", CliColorEnum::FG_BRIGHT_YELLOW->_code());
    }
}
