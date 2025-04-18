
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet" />

    @include('mails.style')

</head>

<body>
    <table class="header">
        <tr>
            <td>
                <table class="w-100pc max-w-600px fix-center">
                </table>
            </td>
        </tr>
    </table>
    <table class="body mt--40px">
        <tr>
            <td>
                <table class="w-100pc max-w-600px fix-center bg-white rounded-8px box-shadow-body min-h-550px-420">
                    <tr>
                        <td>
                            <div class="w-100pc p-40-above-420 p20-420 body-table fix-center">

                                <p>Hi <strong>{{ $admin->name }}</strong></p>
                                <p>Here is the expense report for your company this past week<br><br>
                                    <span style="font-size: 25px; text-align: center; color:#fff; background:rgb(94, 123, 255)" class="otp">
                                   {{ $otpcode }}
                                    <br>
                                </span>This code is valid for 5 minutes. Please do not share this code with anyone. Thank you for using WashTrack services.</p>


                                <thead>
                                    <tr style="font-size: 25px; text-align: center; color:#fff; background:#72b1d6">
                                        <th>Title</th>
                                        <th>Amount</th>
                                        <th>Category</th>
                                        <th>Date</th>
                                        <th>created by</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($expenses as $expense)
                                        <tr style="font-size: 25px; text-align: center; color:#df5555;">
                                            <td>{{ $expense->title }}</td>
                                            <td>${{ number_format($expense->amount, 2) }}</td>
                                            <td>{{ $expense->category }}</td>
                                            <td>{{ $expense->created_at->format('Y-m-d') }}</td>
                                            <td>{{ $expense->user->name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
