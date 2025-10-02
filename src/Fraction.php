<?php declare(strict_types=1);

namespace spriebsch\money;

class Fraction
{
    private int $value;
    private int $fraction;

    private function __construct(int $value, int $fraction)
    {
        $this->value = $value;
        $this->fraction = $fraction;
    }

    public static function from(int $value, int $fraction = 100): self
    {
        return new self($value, $fraction);
    }

    public function value(): int
    {
        return $this->value;
    }

    public function fraction(): int
    {
        return $this->fraction;
    }

    public function asFloat(): float
    {
        return $this->value / $this->fraction;
    }

    public function add(self $that): self
    {
        // normalize to smallest fraction

        return new self($this->value + $that->value, $this->fraction);
    }

    public function subtract(self $that): self
    {
        // normalize to smallest fraction

        return new self($this->value - $that->value, $this->fraction);
    }

    public function equals(self $that): bool
    {
        return $this->value === $that->value;
    }
}
