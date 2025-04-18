@component('mail::message')
    # Weekly Expense Report

    Hello Admin,

    Here are your company’s expenses from **{{ now()->subWeek()->format('M j, Y') }}**
    to **{{ now()->format('M j, Y') }}**:

    @component('mail::table')
        | Title                | Amount        | Date         |
        | -------------------- | ------------- | ------------ |
        @foreach ($expenses as $expense)
            | {{ $expense->title }} | {{ number_format($expense->amount, 2) }} | {{ $expense->created_at->format('M j, Y') }} |
        @endforeach
    @endcomponent

    Thanks,
    {{ config('app.name') }}
@endcomponent
