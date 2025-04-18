@component('mail::message')
# Weekly Expense Report: {{ $reportDate }}

Dear {{ $admin->name }},

Here is the weekly expense report for **{{ $company->name }}**.

## Summary
- Total Expenses: {{ $expensesCount }}
- Total Amount: ${{ number_format($totalAmount, 2) }}

## Expenses by Category
@component('mail::table')
| Category | Count | Total Amount |
| -------- | ----- | ------------ |
@foreach($categorySummary as $summary)
| {{ $summary['category'] }} | {{ $summary['count'] }} | ${{ number_format($summary['total'], 2) }} |
@endforeach
@endcomponent

## Recent Expenses
@component('mail::table')
| Title | User | Amount | Category | Date |
| ----- | ---- | ------ | -------- | ---- |
@foreach($expenses->take(10) as $expense)
| {{ $expense->title }} | {{ $expense->user->name }} | ${{ number_format($expense->amount, 2) }} | {{ $expense->category }} | {{ $expense->created_at->format('M d, Y') }} |
@endforeach
@endcomponent

@if($expenses->count() > 10)
*Note: Only showing the 10 most recent expenses. View all expenses in the dashboard.*
@endif

@component('mail::button', ['url' => config('app.url') . '/dashboard/expenses'])
View All Expenses
@endcomponent

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent