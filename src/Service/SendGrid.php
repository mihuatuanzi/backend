<?php

namespace App\Service;

use App\Interface\EmailDelivery;

class SendGrid implements EmailDelivery
{
    public function send(string $toAddr, string $subject, string $tplId, array $vars = []): bool
    {
        return true;
    }
}
