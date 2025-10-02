<?php declare(strict_types=1);

namespace spriebsch\money;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Small]
#[CoversClass(TestSupportedCurrencies::class)]
final class MyCurrencyTest extends TestCase
{
    public function testHasEuroAndGbpCases(): void
    {
        $this->assertSame('EUR', TestSupportedCurrencies::EUR->value);
        $this->assertSame('GBP', TestSupportedCurrencies::GBP->value);
    }

    public function testBackedEnumTypeIsString(): void
    {
        $this->assertSame('string', get_debug_type(TestSupportedCurrencies::EUR->value));
    }

    public function testCasesEnumeration(): void
    {
        $cases = TestSupportedCurrencies::cases();

        $this->assertSame([TestSupportedCurrencies::EUR, TestSupportedCurrencies::GBP], $cases);
    }
}
