<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Expense Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 40px;
        }

        .letterhead {
            text-align: center;
            margin-bottom: 30px;
        }

        .letterhead img {
            max-height: 80px;
        }

        .company-details {
            margin-top: 10px;
        }

        h2 {
            text-align: center;
            margin-top: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }

        th {
            background-color: #f5f5f5;
        }
    </style>
</head>

<body>

    <div class="letterhead">
        <h1>{{ $company->name }}</h1>
        <div class="company-details">
            <p>Email: {{ $company->email }}</p>
        </div>
    </div>

    <h2>Expense Report</h2>

    <table>
        <thead>
            <tr>
                <th>User</th>
                <th>Expense Category ID</th>
                <th>Title</th>
                <th>Amount</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <!-- Blade Loop -->
            @foreach ($expenses as $expense)
                <tr>
                    <td>{{ $expense->user->name }}</td>
                    <td>{{ $expense->expenseCategory->name }}</td>
                    <td>{{ $expense->title }}</td>
                    <td>{{ number_format($expense->amount, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($expense->created_at)->format('jS F Y') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
