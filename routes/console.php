<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('weekly-expense-report:generate')->weekly()
    ->mondays()
    ->at('08:00');