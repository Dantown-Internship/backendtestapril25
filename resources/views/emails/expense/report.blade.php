<x-mail::message>
# Hello {{ $admin->name }},

Here is your **weekly expense report** for the past 7 days.

Total Expenses: **{{ $expenses->count() }}**


<x-mail::button :url="''">
Download the attached PDF
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
