<?php

namespace App\Enums;

enum SaleStatus: string
{
    case Draft = 'draft';
    case Held = 'held';
    case PendingPayment = 'pending_payment';
    case Completed = 'completed';
    case Voided = 'voided';
    case Refunded = 'refunded';
    case PartiallyRefunded = 'partially_refunded';
}
