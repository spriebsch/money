<?php declare(strict_types=1);

namespace spriebsch\money;

final readonly class Amount
{
    public static function cents(int $cents): self
    {
        return new self(Fraction::from($cents, 100));
    }

    public static function from(Fraction $fraction): self
    {
        return new self($fraction);
    }

    private function __construct(
        private Fraction $fraction
    ) {}

    public function fraction(): Fraction
    {
        return $this->fraction;
    }

    public function add(self $that): self
    {
        return new self($this->fraction->add($that->fraction));
    }

    public function subtract(self $that): self
    {
        return new self($this->fraction->subtract($that->fraction));
    }

    public function equals(self $that): bool
    {
        return $this->fraction->equals($that->fraction());
    }
}
