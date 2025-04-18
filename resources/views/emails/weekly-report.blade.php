@component('mail::message')
# Hello {{ $user->name }},

Here’s **{{ $companyName }}'s expense report** for the period **{{ $startDate->toFormattedDateString() }} - {{ $endDate->toFormattedDateString() }}**.

## 📊 Summary
- **Total Expenses:** ₦{{ number_format($totalExpense, 2) }}

## 🔍 Breakdown by Category
<table width="100%" style="border-collapse: collapse; text-align: center;">
    <thead>
        <tr>
            <th style="border: 1px solid #ddd; padding: 8px;">Category</th>
            <th style="border: 1px solid #ddd; padding: 8px;">Total Spent</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($sortedExpenses as $category => $amount)
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $category }}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">₦{{ number_format($amount, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<br/>


## 💸 Top Spenders

<table width="100%" style="border-collapse: collapse; text-align: center;">
    <thead>
        <tr>
            <th style="border: 1px solid #ddd; padding: 8px;">User</th>
            <th style="border: 1px solid #ddd; padding: 8px;">Total Spent</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($topSpenders as $spender)
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $spender['user'] }}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">₦{{ number_format($spender['total'], 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@endcomponent
