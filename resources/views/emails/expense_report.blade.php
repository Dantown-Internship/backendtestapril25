<!-- resources/views/emails/expense_report.blade.php -->
<h1>Weekly Expense Report</h1>
<p>Here is the expense summary for the week <strong>{{ $from->format('Y-m-d') }}</strong> to <strong>{{ $to->format('Y-m-d') }}</strong>:</p>
<table border="1" cellpadding="6" style="border-collapse:collapse;">
    <thead>
        <tr>
            <th>ID</th>
            <th>Description</th>
            <th>Amount</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($expenses as $expense)
            <tr>
                <td>{{ $expense->id }}</td>
                <td>{{ $expense->description }}</td>
                <td>{{ $expense->amount }}</td>
                <td>{{ $expense->date }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<p>Total Expenses: <strong>{{ $expenses->sum('amount') }}</strong></p>
