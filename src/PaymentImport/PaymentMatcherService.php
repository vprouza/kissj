<?php

declare(strict_types=1);

namespace kissj\PaymentImport;

use kissj\Payment;
use kissj\Payment\PaymentRepository;
use kissj\Payment\PaymentService;

use function array_combine;
use function array_diff_key;
use function array_intersect_key;
use function array_map;
use function sprintf;

class PaymentMatcherService
{
    private PaymentService $paymentService;

    private PaymentRepository $paymentRepository;

    public function __construct(PaymentService $paymentService, PaymentRepository $paymentRepository)
    {
    }

    public function match(array $importedPayments)
    {
        $toBePaid = $this->paymentRepository->findBy(['status' => 'waiting']);

        $getVs = static fn ($payment) => $payment->variableSymbol;

        $toBePaidByVs = array_combine(array_map($getVs, $toBePaid), $toBePaid);
        $importedByVs = array_combine(array_map($getVs, $importedPayments), $importedPayments);

        $commonPayments = array_intersect_key($toBePaidByVs, $importedByVs);
        $wrongPayments  = array_diff_key($importedByVs, $toBePaidByVs);

        $processedPayments = [];
        $paymentErrors     = [];
        foreach ($commonPayments as $vs => $payment) {
            $importedPayment = $importedByVs[$vs];

            if ($payment->price !== $importedPayment->amount) {
                $paymentErrors[] = new WrongAmountError($importedPayment, $payment);
            } elseif ($importedPayment->currency !== 'Kč') {
                $paymentErrors[] = new WrongCurrencyError($importedPayment, $payment);
            } else {
                $this->paymentService->setPaymentPaid($payment);
                $processedPayments[] = $importedPayment;
            }
        }

        foreach ($wrongPayments as $wrongPayment) {
            $paymentErrors[] = new UnknownVariableSymbolError($wrongPayment);
        }

        return [$processedPayments, $paymentErrors];
    }
}


abstract class PaymentMatchingError
{
    /** @var Payment\Payment */
    public function __construct(public $importedPayment, public $repoPayment = null)
    {
    }

    abstract public function getErrorString(): void;
}


class WrongAmountError extends PaymentMatchingError
{
    public function getErrorString()
    {
        return sprintf('Špatná částka. Má být: %s, je: %s.', $this->repoPayment->price, $this->importedPayment->amount);
    }
}

class WrongCurrencyError extends PaymentMatchingError
{
    public function getErrorString()
    {
        return sprintf('Špatná měna. Má být: Kč, je: %s.', $this->importedPayment->currency);
    }
}

class UnknownVariableSymbolError extends PaymentMatchingError
{
    public function getErrorString()
    {
        return sprintf('Neznámý variabilní symbol: %s.', $this->importedPayment->variableSymbol);
    }
}
