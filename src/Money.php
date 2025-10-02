<?php declare(strict_types=1);

namespace spriebsch\money;

use RuntimeException;

final readonly class Money
{
    public static function from(Amount $amount, Currency $currency): self
    {
        return new self($amount, $currency);
    }

    private function __construct(
        private Amount   $amount,
        private Currency $currency
    ) {}

    public function amount(): Amount
    {
        return $this->amount;
    }

    public function currency(): Currency
    {
        return $this->currency;
    }

    public function add(self $that): self
    {
        $this->ensureSameCurrency($that);

        return new self($this->amount->add($that->amount), $this->currency);
    }

    public function subtract(self $that): self
    {
        $this->ensureSameCurrency($that);

        return new self($this->amount->subtract($that->amount), $this->currency);
    }

    public function equals(self $that): bool
    {
        return $this->currencyMatches($this, $that) && $this->amount->equals($that->amount());
    }

    private function ensureSameCurrency(self $that): void
    {
        if (!$this->currencyMatches($this, $that)) {
            throw new RuntimeException('Currency mismatch');
        };
    }

    private function currencyMatches(self $_this, self $that): bool
    {
        return $_this->currency === $that->currency;
    }
}
