<?php

namespace App\Enums;

enum InventoryMovementType: string
{
    case Purchase = 'purchase';
    case Sale = 'sale';
    case Return_ = 'return';
    case Adjustment = 'adjustment';
    case Transfer = 'transfer';
}
