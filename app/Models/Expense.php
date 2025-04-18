<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    /** @use HasFactory<\Database\Factories\ExpenseFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'user_id',
        'title',
        'amount',
        'category',
    ];

    /**
     * Define the relationship with the Company model.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Define the relationship with the User model.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
/*
**Explanation of the changes:**
* Added the fillable attributes to allow mass assignment.
* Defined a `company()` relationship using `belongsTo()`, indicating that an Expense belongs to one Company.
* Defined a `user()` relationship using `belongsTo()`, indicating that an Expense was created by one User.
* The `company_id` and `user_id` fields are foreign keys that reference the `id` fields in the `companies` and `users` tables, respectively.
*/