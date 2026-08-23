<?php

namespace App\Enums;

enum BatchStatus: string
{
    case ACTIVE = 'active';
    case SOLD_OUT = 'sold_out';
    case ARCHIVED = 'archived';
}