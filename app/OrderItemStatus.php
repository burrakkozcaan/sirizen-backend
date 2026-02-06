<?php

namespace App;

enum OrderItemStatus: string
{
    case PENDING = 'pending';
    case PREPARING = 'preparing';
    case READY_TO_SHIP = 'ready_to_ship';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
    case RETURNED = 'returned';
}
