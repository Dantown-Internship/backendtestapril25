<x-mail::message>

<p>Expense Title:</p> {{ $expenses->title }}
<p>Expense Categpry:</p>{{ $expenses->category }}
<p>Expense Amount:</p>{{ $expenses->amount }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
