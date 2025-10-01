<?php declare(strict_types=1);

namespace spriebsch\eventSourcing\bankAccount\tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;
use spriebsch\eventSourcing\bankAccount\Currency;

#[Small]
#[CoversClass(Currency::class)]
final class CurrencyTest extends TestCase
{
    public function testHasEuroAndGbpCases(): void
    {
        self::assertSame('EUR', Currency::EUR->value);
        self::assertSame('GBP', Currency::GBP->value);
    }

    public function testBackedEnumTypeIsString(): void
    {
        self::assertSame('string', get_debug_type(Currency::EUR->value));
    }

    public function testCasesEnumeration(): void
    {
        $cases = Currency::cases();

        self::assertSame([Currency::EUR, Currency::GBP], $cases);
    }
}
