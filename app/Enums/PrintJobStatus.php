<?php

namespace App\Enums;

enum PrintJobStatus: string
{
    case Pending = 'pending';
    case Sent = 'sent';
    case Failed = 'failed';
}
