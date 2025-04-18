# Weekly Expense Report for {{ $companyName }}

Here is a summary of expenses incurred last week:

@if ($expenses->isNotEmpty())
| Title | Category | Amount | Created At |
|-------|----------|--------|------------|
@foreach ($expenses as $expense)
| {{ $expense->title }} | {{ $expense->category }} | {{ $expense->amount }} | {{ $expense->created_at }} |
@endforeach
@else
No expenses were recorded last week.
@endif

Thank you,
The Expense Management System