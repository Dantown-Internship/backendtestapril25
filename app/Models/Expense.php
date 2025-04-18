<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Expense
 *
 * @property int id
 * @property int company_id
 * @property int user_id
 * @property string title
 * @property float amount
 * @property string category
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read User owner
 * @property-read Company company
 */
class Expense extends Model
{
    /** @use HasFactory<\Database\Factories\ExpenseFactory> */
    use HasFactory;
    use BelongsToCompany;

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

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('title', 'LIKE', "%{$term}%")
                ->orWhere('category', 'LIKE', "%{$term}%");
        });
    }
}
