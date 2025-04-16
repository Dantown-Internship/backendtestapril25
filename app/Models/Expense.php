<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Company
 *
 * @property int id
 * @property int company_id
 * @property int user_id
 * @property string title
 * @property float amount
 * @property string category
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Expense extends Model
{
    /** @use HasFactory<\Database\Factories\ExpenseFactory> */
    use HasFactory;

    protected $fillable = [
        'company_id',
        'user_id',
        'title',
        'amount',
        'category'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'float',
        ];
    }
}
