<?php

namespace App\Enum;

enum VaccineStatus: int
{
    case NOT_SCHEDULED = 1;
    case SCHEDULED = 2;
    case VACCINATED = 3;
}
