@component('mail::message')
# Weekly Expense Report

Hello {{ $admin->name }},

Here are your company's expenses for this week:

@component('mail::table')
| Title | Category | Amount | Created At |
|-------------|------------|----------|------------|
@foreach ($expenses as $expense)
| {{ $expense->title }} | {{ $expense->category }} | ₦{{ number_format($expense->amount, 2) }} | {{ $expense->created_at->toDateString() }} |
@endforeach
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
