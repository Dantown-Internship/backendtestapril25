<?php

namespace App\Models;

use App\Traits\BelongsToCompany;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    //
    use BelongsToCompany;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected $guarded = [];
}
