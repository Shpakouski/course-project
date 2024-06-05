<?php

namespace App\Enum;

enum TicketPriority: string
{
    case High = 'High';
    case Average = 'Average';
    case Low = 'Low';
}
