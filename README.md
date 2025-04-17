# Local Setup and Testing

### Requirements
* PHP, MySQL, Redis and Git

### Setup

install dependencies
```bash
$ composer install
```

Then, create a `.env` file based on `.env.example`
```bash
$ cp .env.example .env
```

set up database
   - create a new database
   - fill the details in .env file

    DB_DATABASE=your_db_name
    DB_USERNAME=your_db_user
    DB_PASSWORD=your_db_password

set up mail account
   - use mailtrap.io for local mailing
   - set up your smtp account
   - get the smtp info and fill the .env file

    MAIL_MAILER=smtp
    MAIL_HOST=smtp.mailtrap.io
    MAIL_PORT=2525
    MAIL_USERNAME=your_mailtrap_username
    MAIL_PASSWORD=your_mailtrap_password
    MAIL_ENCRYPTION=null
    MAIL_FROM_ADDRESS=example@example.com
    MAIL_FROM_NAME="${APP_NAME}"

run migration
```bash
$ php artisan migrate
```

If you want to test with some dummy data expecially for scheduling, jobs, and mail sendiing
run seeder
```bash
$ php artisan db:seed
```

start the server
```bash
$ php artisan serve
```

start redis server
```bash
$ redis-server
```

start queue worker
```bash
$ php artisan queue:work --queue=default,report-emails
```

uncomment the command to run the job in the next minute for test purpose instead of weekly in file app\Console\Kernel.php
Also stop the scheduler immediately it runs first time to avoid duplicating (because it was set to run every minitue)
run scheduler
```bash
$ php artisan schedule:run
```

run the test
```bash
$ php artisan test
```