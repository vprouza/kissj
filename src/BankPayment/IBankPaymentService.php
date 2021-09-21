<?php

declare(strict_types=1);

namespace kissj\BankPayment;

use DateTimeImmutable;

interface IBankPaymentService
{
    public function setBreakpoint(DateTimeImmutable $dateTime): bool;

    public function getAndSafeFreshPaymentsFromBank(): int;
}
