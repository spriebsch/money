<?php declare(strict_types=1);

namespace spriebsch\money;

use RuntimeException;

final readonly class Money
{
    public static function from(int $amountInCents, Currency $currency): self
    {
        return new self($amountInCents, $currency);
    }

    private function __construct(
        private int      $amountInCents,
        private Currency $currency
    ) {}

    public function amountInCents(): int
    {
        return $this->amountInCents;
    }

    public function currency(): Currency
    {
        return $this->currency;
    }

    public function add(self $that): self
    {
        $this->ensureSameCurrency($that);

        return new self($this->amountInCents + $that->amountInCents, $this->currency);
    }

    public function subtract(self $that): self
    {
        $this->ensureSameCurrency($that);

        return new self($this->amountInCents - $that->amountInCents, $this->currency);
    }

    public function equals(self $that): bool
    {
        return $this->currency() === $that->currency() &&
            $this->amountInCents() === $that->amountInCents();
    }

    private function ensureSameCurrency(self $that): void
    {
        if ($this->currency !== $that->currency) {
            throw new RuntimeException('Currency mismatch');
        };
    }
}
