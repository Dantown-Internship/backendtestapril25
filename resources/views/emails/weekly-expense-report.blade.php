@component('mail::message')
    # Weekly Expense Summary

    Hello {{ $user->name }},
    Here's a summary of your expenses for the week ({{ now()->subWeek()->format('d M') }} - {{ now()->format('d M') }}):

    ---

    Number of Expenses: {{ $count }}

    Total Spent: ₦{{ number_format($total, 2) }}

    Highest Expense:
    - Title: {{ $largest->title ?? 'N/A' }}
    - Amount: ₦{{ number_format($largest->amount ?? 0, 2) }}
    - Date: {{ optional($largest->created_at)->format('d M Y') }}

    Smallest Expense:
    - Title: {{ $smallest->title ?? 'N/A' }}
    - Amount: ₦{{ number_format($smallest->amount ?? 0, 2) }}
    - Date: {{ optional($smallest->created_at)->format('d M Y') }}

    Average Expense: ₦{{ number_format($average, 2) }}

    ---

    Thanks,
    {{ config('app.name') }}
@endcomponent
