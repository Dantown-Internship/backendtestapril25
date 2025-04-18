@component('mail::message')
# Weekly Expense Report

Hello {{ $admin->name }}, here's your weekly expense summary:

- Total Expenses: ₦{{ number_format($summary['total'], 2) }}
- Number of Entries: {{ $summary['count'] }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
