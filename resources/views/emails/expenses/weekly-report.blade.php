@component('mail::message')
# Weekly Expense Report
### Period: {{ $statistics['period']['start'] }} to {{ $statistics['period']['end'] }}

Dear {{ $user->name }},

Here's your company's expense summary for the past week:

## Summary
- Total Expenses: {{ number_format($statistics['total_amount'], 2) }}
- Number of Expenses: {{ $statistics['count'] }}
- Average Amount: {{ number_format($statistics['average_amount'], 2) }}

## Expenses by Category
@component('mail::table')
| Category | Count | Total Amount |
|:---------|:------|:-------------|
@foreach($statistics['by_category'] as $category => $data)
| {{ $category }} | {{ $data['count'] }} | {{ number_format($data['total'], 2) }} |
@endforeach
@endcomponent

## Recent Expenses
@component('mail::table')
| Date | Title | Category | Amount |
|:-----|:------|:---------|:--------|
@foreach($expenses->take(10) as $expense)
| {{ $expense->created_at->format('Y-m-d') }} | {{ $expense->title }} | {{ $expense->category }} | {{ number_format($expense->amount, 2) }} |
@endforeach
@endcomponent

@if($expenses->count() > 10)
And {{ $expenses->count() - 10 }} more expenses...
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent 