# Background Job Testing Guide

This guide provides instructions for testing the background job processing functionality, specifically the weekly expense report job.

## Prerequisites

Ensure you have Redis installed and running on your local machine. If not, you can install it with:

```bash
sudo apt-get install redis-server
```

## Test Steps

### 1. Configure Environment

Ensure your `.env` file has the correct queue driver configuration:

```
QUEUE_CONNECTION=redis
```

### 2. Clear Previous Queue Jobs (Optional)

If you want to clear any existing jobs in the queue:

```bash
php artisan queue:clear
```

### 3. Dispatch the Weekly Report Job

Run the following command to manually dispatch the job:

```bash
php artisan app:dispatch-weekly-report-job
```

### 4. Process the Queue

In a separate terminal, run the queue worker to process the job:

```bash
php artisan queue:work
```

You should see output indicating that the job is being processed. If there are any errors, they will be displayed in the console.

### 5. Monitor Redis Queue

You can monitor the Redis queue with:

```bash
redis-cli
```

Then, in the Redis CLI, run:

```
KEYS *
```

To see the queues, and:

```
LLEN queues:default
```

To check the length of the default queue.

### 6. Check Email (In Development)

In a development environment, check the Laravel log files to see the email content, or use a tool like Mailhog/Mailpit to catch and view outgoing emails.

### 7. Testing the Scheduler

To test that the scheduler is correctly configured, you can run:

```bash
php artisan schedule:list
```

This will show all scheduled tasks, including our weekly expense report job.

To manually run all scheduled tasks:

```bash
php artisan schedule:run
```

## Production Setup

In a production environment, set up a cron job to run the Laravel scheduler:

```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

And ensure the queue worker is running (or use a process manager like Supervisor):

```
php artisan queue:work --daemon
```

## Troubleshooting

If you encounter issues:

1. Check the Laravel logs (`storage/logs/laravel.log`)
2. Ensure Redis is running properly
3. Make sure your mail configuration is correct
4. Verify that you have data (expenses, companies, admin users) in your database

Remember that the job will only send emails if there are admins in the system and if there are expenses created within the last week. 