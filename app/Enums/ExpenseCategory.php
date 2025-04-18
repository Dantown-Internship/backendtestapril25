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

    public function label()
    {
        return match ($this) {
            self::Food => 'Food',
            self::Transportation => 'Transportation',
            self::Utilities => 'Utilities',
            self::Entertainment => 'Entertainment',
            self::Health => 'Health',
            self::Other => 'Other',
        };
    }
}
