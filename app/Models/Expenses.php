<?php

namespace App\Models;

use App\Auditable;
use Illuminate\Database\Eloquent\Model;

class Expenses extends Model
{
    use Auditable;

    protected $guarded = [];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
