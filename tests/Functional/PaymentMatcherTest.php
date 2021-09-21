<?php

declare(strict_types=1);

namespace Tests\Functional;

use kissj;
use kissj\Payment\PaymentRepository;
use kissj\PaymentImport\PaymentMatcherService;

use function assert;

class PaymentMatcherTest extends BaseTestCase
{
    public function testPaymentMatcher(): void
    {
        $app     = $this->app();
        $matcher = $app->getContainer()->get('paymentMatcherService');
        assert($matcher instanceof PaymentMatcherService);

        $paymentRepository = $app->getContainer()->get('paymentRepository');
        assert($paymentRepository instanceof PaymentRepository);

        $payment                 = new kissj\Payment\Payment();
        $payment->variableSymbol = '34';
        $payment->currency       = 'CZK';
        $payment->event          = 'cej2018';
        $payment->id             = 0;
        $payment->price          = 300;
        $payment->purpose        = 'idontknow';
        $payment->status         = 'waiting';
        $paymentRepository->persist($payment);

        $this->assertTrue(false);
    }
}
