<h1>Weekly Expense Report for {{ $company->name }}</h1>
<p>Total Expenses This Week: {{ $expenses->count() }}</p>

@foreach ($expenses as $expense)
    <p>Expense: {{ $expense->description }} - ${{ $expense->amount }}</p>
@endforeach
