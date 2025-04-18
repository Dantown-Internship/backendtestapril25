<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('weekly-expense-report:generate')->everyFifteenSeconds();