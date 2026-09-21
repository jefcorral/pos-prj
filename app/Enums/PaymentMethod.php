<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case Card = 'card';
    case Gcash = 'gcash';
    case Maya = 'maya';
    case BankTransfer = 'bank_transfer';
    case Other = 'other';
}
