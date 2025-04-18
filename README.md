# 🧾 Mid-Level Technical Test – Multi-Tenant SaaS-Based Expense Management API Solution

This is the solution to th technical test for laravel backend developers.

I have attached the API collection in the root directory as `Backend Test.postman_collection.json`

# 📚 API Documentation

The full API documentation is available via Postman:

👉 <a href="https://documenter.getpostman.com/view/34356572/2sB2cd4dZF" target="_blank">📖 View Full API Docs (opens in new tab)</a>

# To run please follow these instructions:

1. Clone the repository.
2. cd into it.
3. run `composer install` command.
4. run `php artisan key:generate` command.
5. run `npm install` command.
6. cd into the folder in a separate terminal and run `npm run dev`.
7. Create a `.env` from from the `.env.example` file.
8. set up the database. You can use sqlite by replacing the database configurations with

```
    DB_CONNECTION=sqlite
    DB_DATABASE=database/database.sqlite
```

9. run `php artisan migrate --seed` to migrate and seed the database.
10. run `php artisan serve`.
11. Go to the given address in the terminal in your browser.

# You can login with the following credentials:

1. Admin

```
 email = admin@test.com
 password = password
```

2. Manager

```
 email = manager@test.com
 password = password
```

3. Employee

```
 email = employee@test.com
 password = password
```

# Running the test

run `php artisan test` to run the tests.

But be careful, if you are using sqlite as the main database for the project, you may deleting the records while running test.
