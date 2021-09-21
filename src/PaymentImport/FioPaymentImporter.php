<?php

declare(strict_types=1);

/**
 * User: Martin Pecka
 */

namespace kissj\PaymentImport;

class FioPaymentImporter implements AutomaticPaymentImporter
{
    public function getName(): string
    {
        return 'Fio banka';
    }

    /**
     * @return array of kissj\PaymentImport\Payment
     */
    public function getPayments(): array
    {
        // TODO: Implement getPayments() method.
        return [];
    }
}
