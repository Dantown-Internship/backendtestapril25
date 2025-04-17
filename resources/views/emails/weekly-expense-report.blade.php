<h2>Hello {{ $admin->name }},</h2>
<p>Here is your expense report for the past week:</p>

<ul>
@foreach($expenses as $expense)
    <li>{{ $expense->title }} - {{ $expense->amount }} - {{ $expense->created_at->toFormattedDateString() }}</li>
@endforeach
</ul>

<p>Regards,<br>Dantown</p>
