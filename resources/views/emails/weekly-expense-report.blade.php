@extends('layouts.email.main')

@section('content')
    <td align="center" bgcolor="#e9ecef">
        <table
            border="0"
            cellpadding="0"
            cellspacing="0"
            width="100%"
            style="max-width: 600px"
        >
            <tr>
                <td
                    align="left"
                    bgcolor="#ffffff"
                    style="
                  padding: 24px;
                  font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif;
                  font-size: 16px;
                  line-height: 24px;
                "
                >
                    <p style="margin: 0">
                    <h2>Hello {{ $user->name }}</h2>
                    <p>Here is your weekly expense report for {{ $user->company->name }} from {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}.
                    </p>
                    <p>
                        <strong>Total Expenses: </strong>
                        NGN{{ number_format($totalAmount, 2) }} <br />

                        <strong>Number of Expenses:</strong> {{ $expenses->count() }}
                    </p>
                    <br />

                    @component('mail::table')
                        | Category | Amount |
                        |:--------|:-------|
                        @foreach($categoryTotals as $category => $amount)
                            | {{ $category }} | ${{ number_format($amount, 2) }} |
                        @endforeach
                    @endcomponent

                    ## Recent Expenses
                    @component('mail::table')
                        | Date | User | Title | Category | Amount |
                        |:-----|:-----|:------|:---------|:-------|
                        @foreach($expenses->take(10) as $expense)
                            | {{ $expense->created_at->format('M d') }} | {{ $expense->user->name }} | {{ $expense->title }} | {{ $expense->category }} | ${{ number_format($expense->amount, 2) }} |
                        @endforeach
                    @endcomponent

                    @if($expenses->count() > 10)
                        _And {{ $expenses->count() - 10 }} more..._
                    @endif

                </td>
            </tr>
        </table>
    </td>
@endsection
