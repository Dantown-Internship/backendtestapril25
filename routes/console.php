<?php

use App\Jobs\SendWeeklyExpenseReport;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new SendWeeklyExpenseReport)->weeklyOn(1, '08:00')->timezone('Africa/Lagos');
