<?php

namespace App;

enum RefundStatus: string
{
    case REQUESTED = 'requested';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case PROCESSED = 'processed';
}
