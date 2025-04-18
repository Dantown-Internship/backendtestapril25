<?php

// app/Models/Central/Tenant.php
namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $connection = 'central';
    protected $fillable = ['name', 'email', 'database_name', 'subdomain'];
}