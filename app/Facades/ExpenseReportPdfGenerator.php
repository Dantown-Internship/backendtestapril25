<?php

namespace App\Facades;

use App\Services\ExpenseReportPdfGenerator as ServicesExpenseReportPdfGenerator;
use Illuminate\Support\Facades\Facade;

class ExpenseReportPdfGenerator extends Facade
{
   protected static function getFacadeAccessor()
   {
       return ServicesExpenseReportPdfGenerator::class;
   }
}