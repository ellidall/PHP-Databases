<?php
declare(strict_types = 1);

namespace App\Common;

enum GenderEnum: int
{
    case NOT_STATED = 0;
    case MALE = 1;
    case FEMALE = 2;
}
