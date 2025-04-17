protected function schedule(Schedule $schedule)
{
    $schedule->job(new SendWeeklyExpenseReport)->weekly()->mondays()->at('8:00');
}
