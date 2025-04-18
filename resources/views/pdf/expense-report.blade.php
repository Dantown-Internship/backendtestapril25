<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title> {{ $title }} </title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 20px;
            page-break-before: always;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        thead {
            background-color: #f2f2f2;
        }

        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
            word-wrap: break-word; /* Allows long text to break */
        }

        th {
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        /* Ensure table header repeats on new page */
        thead {
            display: table-header-group;
        }

        .text-center{
            text-align: center;
        }
        .text-right{
            text-align: right;
        }

        /* Avoid table spilling over by breaking rows */
        tr {
            page-break-inside: avoid;
        }

        /* Add a bit of space between tables if multiple tables are generated */
        table + table {
            margin-top: 20px;
        }

        .total {
           text-align: right; 
           font-weight:bold; 
        }

    </style>
</head>
<body>

    <h2>{{ $title }}</h2>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Category</th>
                <th class="text-center">Amount (₦)</th>
                <th>Owner</th>
                <th>Owner's Role</th>
            </tr>
        </thead>
        <tbody>
           @for ($i = 0; $i < count($weeklyExpenses); $i++)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $weeklyExpenses[$i]['title'] }}</td>
                    <td>{{ $weeklyExpenses[$i]['category'] }}</td>
                    <td class="text-right"> {{ number_format($weeklyExpenses[$i]['amount'], 2) }}</td>
                    <td>{{ $weeklyExpenses[$i]['user']->name }}</td>
                    <td>{{ $weeklyExpenses[$i]['user']->role }}</td>
                </tr>   
               
           @endfor
           <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="total">₦ {{ number_format($totalAmount, 2)}}</td>
                    <td></td>
                    <td></td>
                </tr> 
        </tbody>
    </table>

</body> 
</html>
