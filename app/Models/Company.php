<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Company
 *
 * @property int id
 * @property string name
 * @property string email
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 */
class Company extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
    ];

    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory;
}
