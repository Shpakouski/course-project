<?php

namespace App\Enum;

enum TicketPriority: string
{
    case High = 'High';
    case Highest = 'Highest';
    case Medium = 'Medium';
    case Low = 'Low';
    case Lowest = 'Lowest';
}
