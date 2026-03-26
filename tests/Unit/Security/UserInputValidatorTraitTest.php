<?php
declare(strict_types=1);

namespace Tests\Unit\Security;

use Aether\Security\UserInputValidatorTrait;
use PHPUnit\Framework\TestCase;

final class UserInputValidatorTraitTest extends TestCase {

    public function testSanitizeInputEscapesHtmlEntities(): void {
        $validator = new class {
            use UserInputValidatorTrait;
        };

        $result = $validator->_sanitizeInput('<script>alert("x")</script>&');

        $this->assertSame('&lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt;&amp;', $result);
    }
}
