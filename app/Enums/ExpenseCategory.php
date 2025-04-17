<?php

namespace App\Enums;

enum ExpenseCategory: string
{
    case Food = 'food';
    case Transportation = 'transportation';
    case Utilities = 'utilities';
    case Entertainment = 'entertainment';
    case Health = 'health';
    case Other = 'other';
}
