<?php

use Database\Seeders\OrderStatusSeeder;
use Database\Seeders\TransactionStatusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

// // This will run the RefreshDatabase trait before each test
// // and after each test, it will roll back the database transactions
// // to ensure a clean state for the next test.

// uses(RefreshDatabase::class);


// test('Seeding the database', function () {
//     // Run the DatabaseSeeder...
//     $this->seed();

//     // Check if the database has been seeded correctly
//     $this->assertDatabaseCount('users', 3);
// });