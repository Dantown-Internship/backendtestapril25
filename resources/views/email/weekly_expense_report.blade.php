<h2>Hello {{ $admin->name }},</h2>

<p>Here is your company's expense report for the past week:</p>

<ul>
@foreach ($expenses as $expense)
    <li>{{ $expense->title }} - {{ $expense->amount }} ({{ $expense->category }})</li>
@endforeach
</ul>

<p>Thank you.</p>
