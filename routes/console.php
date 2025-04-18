<?php

use Illuminate\Support\Facades\Schedule;


Schedule::command('dantown:send-weekly-expenses')->weekly()->mondays()->at('00:00');


