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
    public function test_has_amount_and_currency(): void
    {
        $cents = 1234;

        $money = Money::from(Amount::cents($cents), TestSupportedCurrencies::EUR);

        $this->assertSame($cents, $money->amount()->fraction()->value());
        $this->assertSame(100, $money->amount()->fraction()->fraction());
        $this->assertSame(TestSupportedCurrencies::EUR, $money->currency());
    }

    public function test_adds_values_of_same_currency(): void
    {
        $expected = Money::from(Amount::cents(350), TestSupportedCurrencies::EUR);

        $a = Money::from(Amount::cents(100), TestSupportedCurrencies::EUR);
        $b = Money::from(Amount::cents(250), TestSupportedCurrencies::EUR);

        $sum = $a->add($b);

        $this->assertTrue($expected->equals($sum));
        $this->assertSame(TestSupportedCurrencies::EUR, $sum->currency());

        $this->assertNotSame($a, $sum);
        $this->assertNotSame($b, $sum);
    }

    public function test_subtracts_values_of_same_currency(): void
    {
        $expected = Money::from(Amount::cents(750), TestSupportedCurrencies::EUR);

        $a = Money::from(Amount::cents(1000), TestSupportedCurrencies::EUR);
        $b = Money::from(Amount::cents(250), TestSupportedCurrencies::EUR);

        $diff = $a->subtract($b);

        $this->assertTrue($expected->equals($diff));
        $this->assertSame(TestSupportedCurrencies::EUR, $diff->currency());

        $this->assertNotSame($a, $diff);
        $this->assertNotSame($b, $diff);
    }

    public function test_compares_values(): void
    {
        $amount = random_int(500, 1000);

        $a = Money::from(Amount::cents($amount), TestSupportedCurrencies::GBP);
        $b = Money::from(Amount::cents($amount), TestSupportedCurrencies::GBP);
        $c = Money::from(Amount::cents(300), TestSupportedCurrencies::GBP);
        $d = Money::from(Amount::cents(300), TestSupportedCurrencies::EUR);

        $this->assertTrue($a->equals($b));
        $this->assertFalse($a->equals($c));
        $this->assertFalse($a->equals($d));
    }

    public function test_will_not_add_different_currencies(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Currency mismatch');

        $oneEuro = Money::from(Amount::cents(100), TestSupportedCurrencies::EUR);
        $onePound = Money::from(Amount::cents(100), TestSupportedCurrencies::GBP);

        $oneEuro->add($onePound);
    }

    public function test_will_not_substract_different_currencies(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Currency mismatch');

        $oneEuro = Money::from(Amount::cents(100), TestSupportedCurrencies::EUR);
        $onePound = Money::from(Amount::cents(100), TestSupportedCurrencies::GBP);

        $oneEuro->subtract($onePound);
    }
}
