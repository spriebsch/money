<?php declare(strict_types=1);

namespace spriebsch\eventSourcing\bankAccount;

enum Currency :string
{
    case EUR = 'EUR';
    case GBP = 'GBP';
}
