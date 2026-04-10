<?php

namespace App\Enums;

enum TableStatus: string
{
    case Free = 'free';
    case Occupied = 'occupied';
    case WaitingPayment = 'waiting_payment';
}
