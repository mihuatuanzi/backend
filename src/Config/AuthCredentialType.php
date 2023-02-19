<?php

namespace App\Config;

enum AuthCredentialType: int
{
    case Email = 1;
    case PhoneNumber = 2;
    case WechatOpenid = 3;
    case QQOpenid = 4;
}
