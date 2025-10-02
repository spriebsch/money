# Money

To use this library, you must create an enum with the currencies you want to support, e.g.

<?php declare(strict_types=1);

namespace spriebsch\money;

enum SupportedCurrencies: string implements Currency
{
    case EUR = 'EUR';
    case GBP = 'GBP';
}

This Enum must implement the Currency interface.
