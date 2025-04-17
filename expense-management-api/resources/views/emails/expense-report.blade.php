<!DOCTYPE html>
<html>
<head>
    <title>Weekly Expense Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            background-color: #f5f5f5;
            padding: 20px;
            border-bottom: 1px solid #ddd;
        }
        .content {
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .footer {
            font-size: 12px;
            color: #777;
            padding: 20px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Weekly Expense Report</h2>
        <p>
            <strong>Company:</strong> {{ $company->name }}<br>
            <strong>Period:</strong> {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}
        </p>
    </div>
    
    <div class="content">
        <h3>Hello {{ $user->name }},</h3>
        
        <p>Here is the weekly expense report for {{ $company->name }}.</p>
        
        <h4>Summary</h4>
        <p><strong>Total Expenses:</strong> ${{ number_format($totalAmount, 2) }}</p>
        <p><strong>Number of Expenses:</strong> {{ $expenses->count() }}</p>
        
        @if($expenses->count() > 0)
            <h4>Expense Details</h4>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Submitted By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenses as $expense)
                        <tr>
                            <td>{{ $expense->created_at->format('M d, Y') }}</td>
                            <td>{{ $expense->title }}</td>
                            <td>{{ $expense->category }}</td>
                            <td>${{ number_format($expense->amount, 2) }}</td>
                            <td>{{ $expense->user->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <h4>Category Breakdown</h4>
            <table>
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $categories = $expenses->groupBy('category');
                        $categoryTotals = [];
                        
                        foreach($categories as $category => $items) {
                            $categoryTotals[$category] = $items->sum('amount');
                        }
                    @endphp
                    
                    @foreach($categoryTotals as $category => $amount)
                        <tr>
                            <td>{{ $category }}</td>
                            <td>${{ number_format($amount, 2) }}</td>
                            <td>{{ round(($amount / $totalAmount) * 100, 1) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No expenses were recorded during this period.</p>
        @endif
        
        <p>
            You can view more detailed reports by logging into the expense management system.
            If you have any questions, please contact support.
        </p>
    </div>
    
    <div class="footer">
        <p>This is an automated report from the Expense Management System.</p>
    </div>
</body>
</html>