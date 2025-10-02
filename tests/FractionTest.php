<?php declare(strict_types=1);

namespace spriebsch\money;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Small]
#[CoversClass(Fraction::class)]
final class FractionTest extends TestCase
{
    public function test_some(): void
    {
        $cents = 1234;
        $fraction = 100;

        $fraction = Fraction::from($cents, $fraction);

        $this->assertSame(12.34, $fraction->asFloat());
    }
}
