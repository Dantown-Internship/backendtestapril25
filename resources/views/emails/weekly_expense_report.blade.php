<h1>Weekly Expense Report for {{ $company->name }}</h1>
<p>Here's a summary of expenses from the past week:</p>

<table border="1" cellpadding="10">
    <thead>
        <tr>
            <th>Title</th>
            <th>Amount</th>
            <th>Category</th>
            <th>Submitted By</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($expenses as $expense)
        <tr>
            <td>{{ $expense->title }}</td>
            <td>${{ number_format($expense->amount, 2) }}</td>
            <td>{{ $expense->category }}</td>
            <td>{{ $expense->user->name }}</td>
            <td>{{ $expense->created_at->format('m/d/Y') }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="4" style="text-align: right;"><strong>Total:</strong></td>
            <td><strong>${{ number_format($total, 2) }}</strong></td>
        </tr>
    </tbody>
</table>