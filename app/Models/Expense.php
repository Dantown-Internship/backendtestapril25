<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\User;
use App\Models\Company;


class Expense extends Model
{
    //
    use HasFactory;

    protected $fillable = ['company_id', 'user_id', 'title', 'amount', 'category'];

    public function user():BelongsTo { return $this->belongsTo(User::class); }
    public function company():BelongsTo { return $this->belongsTo(Company::class); }
}
