<?php

namespace App;

enum PaymentType: string
{
    case CARD = 'card';
    case TRANSFER = 'transfer';
    case WALLET = 'wallet';
}
