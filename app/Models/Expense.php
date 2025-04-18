<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_id',
        'user_id',
        'title',
        'amount',
        'category',
    ];

    public function users(){
        return $this->belongsTo(User::class, 'user_id');
    }
    public function company(){
        return $this->belongsTo(Company::class, 'company_id');
    }


    public static function createExpenseRecord($params, $companyId, $userId){

        $expense = new Expense();
        $expense->title= $params['title'];
        $expense->category = $params['category'];
        $expense->amount = $params['amount'];
        $expense->user_id = $params['user_id'];
        $expense->company_id = $companyId;
        $expense->save();

        self::logAudit('created', [], $expense);
        return $expense;
    }
}
