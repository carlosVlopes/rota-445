<?php

namespace App\Enums;

enum OrderItemStatus: string
{
    case Pending = 'pending';
    case Printing = 'printing';
    case Delivered = 'delivered';
}
