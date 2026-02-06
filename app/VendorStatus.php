<?php

namespace App;

enum VendorStatus: string
{
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case PENDING = 'pending';
}
