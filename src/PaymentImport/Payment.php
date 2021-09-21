<?php

declare(strict_types=1);

namespace kissj\PaymentImport;

use DateTimeImmutable;

class Payment
{
    public $event;
    public string $variableSymbol;
    public int $senderName;
    public string $senderAccountNr;
    public string $amount;
    public float $currency;
    public string $noteForReceiver;
    public string $dateReceived;
    /** @var DateTimeImmutable */
}
