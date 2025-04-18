// resources/views/emails/expenses/weekly-report.blade.php
@component('mail::message')
# Weekly Expense Report for {{ $company->name }}

Dear {{ $admin->name }},

Here is the weekly expense report for the period {{ now()->subWeek()->format('Y-m-d') }} to {{ now()->format('Y-m-d') }}.

**Total Expenses: ${{ number_format($totalAmount, 2) }}**

@component('mail::table')
| Employee | Title | Category | Amount |
|:---------|:------|:---------|-------:|
@foreach($expenses as $expense)
| {{ $expense->user->name }} | {{ $expense->title }} | {{ $expense->category }} | ${{ number_format($expense->amount, 2) }} |
@endforeach
@endcomponent

You can view detailed reports by logging into your account.

Thanks,<br>
{{ config('app.name') }}
@endcomponent