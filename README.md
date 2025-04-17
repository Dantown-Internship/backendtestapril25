# Dantown 


## Prerequisites

- Laravel 11.x 
- Php >= 8.3
- A MySQL database 
- Nginx (Server)



## Routes 
<p>API Routes are mapped in bootstrap/app.php for easy maintenance. So each routes is prefixed semantically to its used case. For laravel 10x it can be achieved in App/Providers/RouteServiceProvider</p>

<p>This assessment used Modular routing, Service layer with Contracts(Interface) binded for easy maintenance. No Respository layer was created for simplicity </p>
  POST      auth/logout ..................Logout <br>
  GET       auth/me ..................Current Logged In user<br>
  POST      auth/signin ............... SignIn <br>
  POST      auth/signup ..............Signup (Admin authorized)<br>
  GET       company/expense ..................Fetch expenses (paginated), filtered by company name or title<br>
  POST      company/expense/create .........................Create Expenses
  DELETE    company/expense/delete/{expenseId} ...................Delete Expense(Admin authorized) using the expense ID as part of url parameter<br>
  PUT     /company/expense/{expenseId} .........................Update the company Expense using the ExpenseId (Admin and Manager authorized action)<br><br>
 
  GET users .............................Fetch Users (paginated!)(Admin Authorized)<br>
  POST      users/create .............................Create User (using name, email, password, password_confirmation, company_id, role_name) (Admin authorized) <br>
  DELETE    users/delete/{userId} ....................Delete User using userId (Admin authorized)<br>
  GET|HEAD  users/{userId?} ..........................Fetch a user using the userId (Admin authorized)<br>
  PUT   users/{userId} ...............................Update user using the userId.



## Setup
 <p>Setup your project base Url i.e {{baseUrl}} on postman. Import the API collection in collections/ directory in the project root folder into your postman. Set your authorization header to accept application/json, and Authorization as bearer token which the auth token</p>
 <p>Set your mail stmp credentials on your .env</p>

### Clone the Repository
```bash
git switch armstrong-enefe
cp .env.example .env

composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
```

Signin with 
email : admin@dantown.io ;
password: password

<p>In the databse the roles(admin, manager, employee) has been seeded, so is the Users and Company</p>

#### Run the Schedular 

```bash
    php artisan schedule:run
```
Mails are queued, to track failed mails run(seperate terminal process) ;
<p>Add  the following in your .env file.   1 (first day of the week and so on)</p>

```bash
    EXPENSE_REPORT_DAY=1
    EXPENSE_REPORT_TIME=09:00   
```
<p>Then run <p>

```bash
     php artisan queue:work
```




