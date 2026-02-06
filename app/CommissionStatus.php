<?php

namespace App;

enum CommissionStatus: string
{
    case PENDING = 'pending';
    case SETTLED = 'settled';
    case CANCELLED = 'cancelled';
}
