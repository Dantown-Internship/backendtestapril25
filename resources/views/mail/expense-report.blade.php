<!DOCTYPE html>
<html>

    <head>
        <meta charset="UTF-8">
        <title>Weekly Expenses</title>
    </head>

    <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
        <h1>Dear {{ $admin->name }},</h1>

        <p>Here is a list of expenses for the past week:</p>

        <ul>
            @forelse ($expenses as $expense)
                <li>
                    <strong>User:</strong> {{ $expense->user->name }}<br>
                    <strong>Company:</strong> {{ $expense->company->name }}<br>
                    <strong>Title:</strong> {{ $expense->title }}<br>
                    <strong>Amount:</strong> {{ number_format($expense->amount, 2) }}<br>
                    <strong>Category:</strong> {{ $expense->category }}<br>
                    <strong>Date:</strong> {{ $expense->created_at->format('F j, Y') }}
                </li>
                <br>
            @empty
                <p>No expenses recorded this week.</p>
            @endforelse
        </ul>


        <p>
            <a href="{{ route('expenses.index') }}"
                style="display: inline-block; padding: 10px 20px; background-color: #3490dc; color: #ffffff; text-decoration: none; border-radius: 5px;">
                View All Expenses
            </a>
        </p>

        <p>Thanks,<br>{{ config('app.name') }}</p>
    </body>

</html>
