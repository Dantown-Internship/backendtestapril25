@component('mail::message')
# Weekly Expense Report

Hi {{ $admin->name }},

Here is a summary of your company's expenses from the past 7 days:

@forelse($expenses as $expense)
- **{{ $expense->title }}** ({{ $expense->category }}) — ${{ number_format($expense->amount, 2) }}  
  by {{ $expense->user->name }} on {{ $expense->created_at->format('M d, Y') }}
@empty
- No expenses found this week.
@endforelse

---

**Total Spent This Week:** ${{ number_format($total, 2) }}

Thanks,  
{{ config('app.name') }}
@endcomponent
