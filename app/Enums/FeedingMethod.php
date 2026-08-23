<?php

namespace App\Enums;

enum FeedingMethod: string
{
    case TWICE_DAILY = 'twice_daily';
    case FOUR_TIMES_DAILY = 'four_times_daily';
    case UNLIMITED = 'unlimited';
}