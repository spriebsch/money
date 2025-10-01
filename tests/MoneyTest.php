<?php declare(strict_types=1);

namespace spriebsch\money;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[Small]
#[CoversClass(Money::class)]
final class MoneyTest extends TestCase
{
    public function testItIsCreatedFromAmountAndCurrency(): void
    {
        $money = Money::from(1234, Currency::EUR);

        self::assertSame(1234, $money->amountInCents());
        self::assertSame(Currency::EUR, $money->currency());
    }

    public function testAddReturnsNewInstanceWithSummedAmount(): void
    {
        $a = Money::from(100, Currency::EUR);
        $b = Money::from(250, Currency::EUR);

        $sum = $a->add($b);

        self::assertSame(350, $sum->amountInCents());
        self::assertSame(Currency::EUR, $sum->currency());
        self::assertNotSame($a, $sum);
        self::assertNotSame($b, $sum);
    }

    public function testSubtractReturnsNewInstanceWithSubtractedAmount(): void
    {
        $a = Money::from(1000, Currency::EUR);
        $b = Money::from(250, Currency::EUR);

        $diff = $a->subtract($b);

        self::assertSame(750, $diff->amountInCents());
        self::assertSame(Currency::EUR, $diff->currency());
    }

    public function testEqualsIsTrueForSameAmountAndCurrency(): void
    {
        $a = Money::from(500, Currency::GBP);
        $b = Money::from(500, Currency::GBP);

        self::assertTrue($a->equals($b));
    }

    public function testEqualsIsFalseForDifferentAmount(): void
    {
        $a = Money::from(500, Currency::GBP);
        $b = Money::from(600, Currency::GBP);

        self::assertFalse($a->equals($b));
    }

    public function testEqualsIsFalseForDifferentCurrency(): void
    {
        $a = Money::from(500, Currency::GBP);
        $b = Money::from(500, Currency::EUR);

        self::assertFalse($a->equals($b));
    }

    public function testAddThrowsOnCurrencyMismatch(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Currency mismatch');

        Money::from(100, Currency::EUR)->add(Money::from(100, Currency::GBP));
    }

    public function testSubtractThrowsOnCurrencyMismatch(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Currency mismatch');

        Money::from(100, Currency::GBP)->subtract(Money::from(50, Currency::EUR));
    }
}
