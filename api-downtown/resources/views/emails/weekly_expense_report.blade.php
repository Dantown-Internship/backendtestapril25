<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Expense Report - {{ $app_name }}</title>
    <style>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="max-w-lg mx-auto bg-white p-6 mt-10 rounded-lg shadow-md">
        <div class="text-center">
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Weekly Expense Report</h1>
            <p class="text-gray-600 mb-6">Here are the expenses for {{ $company->name }} this week:</p>
        </div>

        @if ($expenses->isEmpty())
            <p class="text-gray-600 text-center">No expenses recorded this week.</p>
        @else
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="p-2">Title</th>
                        <th class="p-2">Amount</th>
                        <th class="p-2">Category</th>
                        <th class="p-2">User</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($expenses as $expense)
                        <tr class="border-b">
                            <td class="p-2">{{ $expense->title }}</td>
                            <td class="p-2">${{ number_format($expense->amount, 2) }}</td>
                            <td class="p-2">{{ $expense->category }}</td>
                            <td class="p-2">{{ $expense->user->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <p class="text-gray-600 text-center mt-6">View more details at <a href="{{ $frontend_url }}/login" class="text-blue-600 hover:underline">Multi-Tenant SaaS</a>.</p>

        <div class="text-center mt-8">
            <p class="text-gray-600 text-sm">Best regards,<br>The {{ $app_name }} Team</p>
        </div>
    </div>
</body>
</html>