<?php

declare(strict_types=1);

namespace kissj\Mailer;

class MockMailer
{
    public function sendMailFromTemplate($recipientEmail, $subject, $tempalteName, $parameters): void
    {
    }
}
