<h1>Weekly Expense Report for {{ $company->name }}</h1>

<p>Here are the expenses for this week:</p>

<ul>
    @foreach ($expenses as $expense)
        <li>{{ $expense->title }}: ${{ $expense->amount }} ({{ $expense->category }})</li>
    @endforeach
</ul>