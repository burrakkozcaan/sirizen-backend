<?php

namespace App;

enum DisputeStatus: string
{
    case OPEN = 'open';
    case INVESTIGATING = 'investigating';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';
}
