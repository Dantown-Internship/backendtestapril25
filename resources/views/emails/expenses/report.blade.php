@component('mail::layout')
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            {{ config('app.name') }}
        @endcomponent
    @endslot

    @component('mail::panel', ['color' => '#f8f9fa'])
        <h1 style="color: #2d3748; margin-bottom: 0">Weekly Expense Report</h1>
        <p style="color: #718096; margin-top: 4px">
            {{ now()->subWeek()->format('M j') }} - {{ now()->format('M j, Y') }}
        </p>
    @endcomponent

    <p style="font-size: 16px">Hello {{ $admin->name }},</p>

    <p>Here's the expense summary for <strong>{{ $admin->company->name }}</strong>:</p>

    @component('mail::table')
        | Category       | Description   | Amount       |
        |:---------------|:--------------|-------------:|
        @foreach($expenses as $expense)
        | {{ $expense->category }} | {{ $expense->title }} | ₦{{ number_format($expense->amount, 2) }} |
        @endforeach
        | **Total**      |               | **₦{{ number_format($expenses->sum('amount'), 2) }}** |
    @endcomponent

    @component('mail::button', ['url' => route('expenses.index'), 'color' => 'primary'])
        View Detailed Report
    @endcomponent

    <p style="margin-top: 24px">
        Need help analyzing these expenses?<br>
        <a href="mailto:finance@example.com">Contact our finance team</a>
    </p>

    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
            [Privacy Policy]({{ url('/privacy') }) | [Unsubscribe]({{ url('/unsubscribe') }})
        @endcomponent
    @endslot
@endcomponent
