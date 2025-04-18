# Douglas Leslie

### <u>NOTES/ASSUMPTIONS MADE</u>

1. I used soft deletes for the company and User model as that is industry standard and helps ensure data integrity. There was no need to use it for the Expense model given the extensive logging feature added
2. I used decimal for currency to ensure accuracy and prevent floating point errors in how dbs store floating point numbers
3. I used policies over gates, so that in the event that an admin dashboard is added later via Laravel Nova, FilamentPHP or any admin tool,Existing policies would manage resources effectively
4. Bubbled forbidden exceptions to not found exceptions to promote security through obscurity and guard against bruteforce attacks.
5. Would have used subdomains for managing multiple tenants but that would overcomplicate what should be a "simple" assessment

### <u>FEATURES I IMPLEMENTED</u>

Task 1: Multi-Tenant Database Structure (Migrations & Models)

1. If you are looking for an explicit index on the company_id and other fields, Laravel by default indexes id and foreignId fields

Task 2: API Authentication and RBAC

1. I used laravel policies for managing access to resources

Task 3: API Endpoints

1. All the api endpoints were created and have role based access control

Task 4: Optimization

1. Redis was used to cache certain routes via middleware and the send weekly expense job used eager loading and chunking for better performance

Task 5: Background Job Processing

1. I have attached a screenshot of what the email looks like for the weekly expense report. I also wrote tests for the background job and mailer
   [![Evidence-of-email.png](https://i.postimg.cc/4N3z2JcP/Evidence-of-email.png)](https://postimg.cc/rzv0K2QR)

Task 6: Audit Logs

1. Completed To specification

### <u>INSTRUCTIONS FOR TESTING</u>

#### PROJECT SETUP

1. Clone repository
2. Set encryption key

```
php artisan key:generate
```

3. copy .env.example file to .env and also to .env.testing file. Go to [Configuration](#configuration) to set DB details

```
cp .env.example .env && cp .env.example .env.testing
```

4. Download Dependencies

```
composer install
```

5. Perform migrations and seed database and migrate db for testing database

```
php artisan migrate --seed && php artisan migrate  --env=testing
```

#### TESTING

1. After setting up, you can run the tests by running:

```
php artisan test
```

2. To see code coverage for the tests, install [xdebug][xdebug-url] and then run

```
php artisan test --coverage
```

### CONFIGURATION

Please modify these values in the `.env` file.

-   DB_DATABASE=expense
-   DB_PASSWORD=**\*\*\*\*** (Put your password here, leave blank if your mysql root uses no password)

Please modify these values in the `.env.testing` file.

-   DB_DATABASE=expense_testing
-   DB_PASSWORD=**\*\*\*\*** (Put your password here, leave blank if your mysql root uses no password)

[xdebug-url]: https://xdebug.org/docs/install
