<?php

declare(strict_types=1);

namespace kissj\Payment;

use DateInterval;
use DateTime;
use kissj\Orm\EntityDatetime;
use kissj\Participant\Participant;

use function assert;

/**
 * @property int         $id
 * @property string      $variableSymbol
 * @property string      $price
 * @property string      $currency
 * @property string      $status
 * @property string      $purpose
 * @property string      $accountNumber
 * @property string      $note
 * @property Participant $participant m:hasOne
 */
class Payment extends EntityDatetime
{
    public const STATUS_WAITING  = 'waiting';
    public const STATUS_PAID     = 'paid';
    public const STATUS_CANCELED = 'canceled';

    public function getElapsedPaymentDays(): int
    {
        $createdAt = $this->createdAt;
        assert($createdAt instanceof DateTime);

        return $createdAt->diff(new DateTime('now'))->days;
    }

    public function getMaxElapsedPaymentDays(): int
    {
        return 14; // TODO move into db
    }

    public function getPaymentUntil(): DateTime
    {
        $createdAt = $this->createdAt;
        assert($createdAt instanceof DateTime);
        $dateInterval = new DateInterval('P' . $this->getMaxElapsedPaymentDays() . 'D');

        return $createdAt->add($dateInterval);
    }
}

/**
 * TODO do not forget add note and rename conventions into new DB
 */
