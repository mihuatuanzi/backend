<?php

namespace App\Config;

enum SendVerificationScene: int
{
    case SignUp = 1;
    case ResetPassword = 2;
}
