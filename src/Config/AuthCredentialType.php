<?php

namespace App\Config;

use App\Adapter\Credential;
use App\Entity\Authentication;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;

enum AuthCredentialType: int
{
    case Email = 1;
    case PhoneNumber = 2;
    case WechatOpenid = 3;
    case QQOpenid = 4;
}
