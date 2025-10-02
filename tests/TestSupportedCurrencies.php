<?php declare(strict_types=1);

namespace spriebsch\money;

enum TestSupportedCurrencies: string implements Currency
{
    case EUR = 'EUR';
    case GBP = 'GBP';
}
