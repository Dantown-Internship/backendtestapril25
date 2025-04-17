<?php

namespace App\Enums;

enum ExpenseCategory: string
{
    case Food = 'food';
    case Utilities = 'utilities';
    case Transportation = 'transportation';
    case DebtPayments = 'debt_payments';
    case Housing = 'housing';
    case Healthcare = 'healthcare';
    case Shopping = 'shopping';
    case Others = 'others';

    public function title(): string
    {
        return match ($this) {
            self::Food => 'Food',
            self::Utilities => 'Utilities',
            self::Transportation => 'Transportation',
            self::DebtPayments => 'Debt Payments',
            self::Housing => 'Housing',
            self::Healthcare => 'Healthcare',
            self::Shopping => 'Shopping',
            self::Others => 'Others',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Food => 'Food & Dining',
            self::Utilities => 'Utilities & Bills',
            self::Transportation => 'Transportation & Fuel',
            self::DebtPayments => 'Debt Payments & Loans',
            self::Housing => 'Housing & Rent',
            self::Healthcare => 'Healthcare & Medical',
            self::Shopping => 'Shopping & Personal',
            self::Others => 'Others & Miscellaneous',
        };
    }
}