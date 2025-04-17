<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Styled Table for PDF</title>
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

        /* Avoid table spilling over by breaking rows */
        tr {
            page-break-inside: avoid;
        }

        /* Add a bit of space between tables if multiple tables are generated */
        table + table {
            margin-top: 20px;
        }

    </style>
</head>
<body>

    <h2>Expenses Report</h2>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Company</th>
                <th>User</th>
                <th>Title</th>
                <th>Amount</th>
                <th>Category</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Acme Corp</td>
                <td>Jane Doe</td>
                <td>Office Supplies</td>
                <td>$120.50</td>
                <td>Admin</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Globex</td>
                <td>John Smith</td>
                <td>Travel</td>
                <td>$1,200.00</td>
                <td>Operations</td>
            </tr>
            <!-- More rows to cause spillover -->
            <tr><td>3</td><td>TechCorp</td><td>Sam Wilson</td><td>Marketing</td><td>$330.00</td><td>Sales</td></tr>
            <tr><td>4</td><td>Innovate Inc</td><td>Laura Brown</td><td>Consulting</td><td>$450.00</td><td>Development</td></tr>
            <tr><td>5</td><td>SyncTech</td><td>Mark Taylor</td><td>Software</td><td>$900.00</td><td>R&D</td></tr>
            <tr><td>6</td><td>GiantWorks</td><td>Alice Green</td><td>Business Travel</td><td>$210.50</td><td>HR</td></tr>
            <tr><td>7</td><td>NextGen</td><td>Paul Wilson</td><td>Equipment</td><td>$870.00</td><td>Tech</td></tr>
            <tr><td>8</td><td>Worldwide Enterprises</td><td>Daniel Lee</td><td>Supplies</td><td>$350.00</td><td>Logistics</td></tr>
            <tr><td>9</td><td>CloudCorp</td><td>Rachel Adams</td><td>Training</td><td>$550.00</td><td>Admin</td></tr>
            <tr><td>10</td><td>Digital Innovations</td><td>David King</td><td>Software Licenses</td><td>$600.00</td><td>IT</td></tr>
            <tr><td>3</td><td>TechCorp</td><td>Sam Wilson</td><td>Marketing</td><td>$330.00</td><td>Sales</td></tr>
            <tr><td>4</td><td>Innovate Inc</td><td>Laura Brown</td><td>Consulting</td><td>$450.00</td><td>Development</td></tr>
            <tr><td>5</td><td>SyncTech</td><td>Mark Taylor</td><td>Software</td><td>$900.00</td><td>R&D</td></tr>
            <tr><td>6</td><td>GiantWorks</td><td>Alice Green</td><td>Business Travel</td><td>$210.50</td><td>HR</td></tr>
            <tr><td>7</td><td>NextGen</td><td>Paul Wilson</td><td>Equipment</td><td>$870.00</td><td>Tech</td></tr>
            <tr><td>8</td><td>Worldwide Enterprises</td><td>Daniel Lee</td><td>Supplies</td><td>$350.00</td><td>Logistics</td></tr>
            <tr><td>9</td><td>CloudCorp</td><td>Rachel Adams</td><td>Training</td><td>$550.00</td><td>Admin</td></tr>
            <tr><td>10</td><td>Digital Innovations</td><td>David King</td><td>Software Licenses</td><td>$600.00</td><td>IT</td></tr>
            <tr><td>3</td><td>TechCorp</td><td>Sam Wilson</td><td>Marketing</td><td>$330.00</td><td>Sales</td></tr>
            <tr><td>4</td><td>Innovate Inc</td><td>Laura Brown</td><td>Consulting</td><td>$450.00</td><td>Development</td></tr>
            <tr><td>5</td><td>SyncTech</td><td>Mark Taylor</td><td>Software</td><td>$900.00</td><td>R&D</td></tr>
            <tr><td>6</td><td>GiantWorks</td><td>Alice Green</td><td>Business Travel</td><td>$210.50</td><td>HR</td></tr>
            <tr><td>7</td><td>NextGen</td><td>Paul Wilson</td><td>Equipment</td><td>$870.00</td><td>Tech</td></tr>
            <tr><td>8</td><td>Worldwide Enterprises</td><td>Daniel Lee</td><td>Supplies</td><td>$350.00</td><td>Logistics</td></tr>
            <tr><td>9</td><td>CloudCorp</td><td>Rachel Adams</td><td>Training</td><td>$550.00</td><td>Admin</td></tr>
            <tr><td>10</td><td>Digital Innovations</td><td>David King</td><td>Software Licenses</td><td>$600.00</td><td>IT</td></tr>
            <tr><td>3</td><td>TechCorp</td><td>Sam Wilson</td><td>Marketing</td><td>$330.00</td><td>Sales</td></tr>
            <tr><td>4</td><td>Innovate Inc</td><td>Laura Brown</td><td>Consulting</td><td>$450.00</td><td>Development</td></tr>
            <tr><td>5</td><td>SyncTech</td><td>Mark Taylor</td><td>Software</td><td>$900.00</td><td>R&D</td></tr>
            <tr><td>6</td><td>GiantWorks</td><td>Alice Green</td><td>Business Travel</td><td>$210.50</td><td>HR</td></tr>
            <tr><td>7</td><td>NextGen</td><td>Paul Wilson</td><td>Equipment</td><td>$870.00</td><td>Tech</td></tr>
            <tr><td>8</td><td>Worldwide Enterprises</td><td>Daniel Lee</td><td>Supplies</td><td>$350.00</td><td>Logistics</td></tr>
            <tr><td>9</td><td>CloudCorp</td><td>Rachel Adams</td><td>Training</td><td>$550.00</td><td>Admin</td></tr>
            <tr><td>10</td><td>Digital Innovations</td><td>David King</td><td>Software Licenses</td><td>$600.00</td><td>IT</td></tr>

        </tbody>
    </table>

</body> 
</html>
