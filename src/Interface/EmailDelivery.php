<?php

namespace App\Interface;

interface EmailDelivery
{
    public function send(string $toAddr, string $subject, string $tplId, array $vars = []): bool;
}
