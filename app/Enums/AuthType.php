<?php

namespace App\Enums;

enum AuthType: string
{
    case Basic = 'basic';
    case Bearer = 'bearer';
}
