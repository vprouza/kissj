<?php

declare(strict_types=1);

namespace kissj\PaymentImport;

interface PaymentImporter
{
    public function getName(): string;

    /**
     * @return Payment[]
     */
    public function getPayments(): array;
}
