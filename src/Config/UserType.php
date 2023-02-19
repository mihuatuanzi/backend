<?php

namespace App\Config;

enum UserType: int
{
    case Person = 1;
    case Bot = 2;
    case Client = 3;
}
