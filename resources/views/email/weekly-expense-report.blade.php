<h2>Weekly Expense Report</h2>
<p>Hello {{ $admin->name }}, here is the weekly expense report.</p>

@foreach ($expenses as $companyId => $groupedExpenses)
    <h4>Company: {{ $groupedExpenses->first()->company->name ?? 'N/A' }}</h4>
    <ul>
        @foreach ($groupedExpenses as $expense)
            <li>
                {{ $expense->title }} - ${{ $expense->amount }} <br>
                By: {{ $expense->user->name ?? 'N/A' }} ({{ $expense->category }})
            </li>
        @endforeach
    </ul>
@endforeach
