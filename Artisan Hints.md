
# Laravel Artisan Command Reference (Version 12.8.1)

## Usage

```
php artisan command [options] [arguments]

```

## Global Options

| **Option**     | **Shorthand** | **Description**                                                                                |
| -------------------- | ------------------- | ---------------------------------------------------------------------------------------------------- |
| `--help`           | `-h`              | Display help for the given command. If no command is given, displays help for the `list`command.   |
| `--silent`         | ``           | Do not output any messages.                                                                          |
| `--quiet`          | `-q`              | Only errors are displayed. All other output is suppressed.                                           |
| `--version`        | `-V`              | Display this application version (12.8.1).                                                           |
| `--ansi`           | ``           | Force ANSI output.                                                                                   |
| `--no-ansi`        | ``           | Disable ANSI output.                                                                                 |
| `--no-interaction` | `-n`              | Do not ask any interactive questions.                                                                |
| `--env[=ENV]`      | ``           | The environment the command should run under (e.g.,`--env=staging`).                               |
| `--verbose`        | `-v\|vv\|vvv`       | Increase the verbosity of messages:`1`for normal,`2`for more verbose, and `3`for debug output. |

## Available Commands

### Basic Commands

| **Command**  | **Description**                                                    |
| ------------------ | ------------------------------------------------------------------------ |
| `about`          | Display basic information about your application.                        |
| `clear-compiled` | Remove the compiled class file.                                          |
| `completion`     | Dump the shell completion script.                                        |
| `db`             | Start a new database CLI session.                                        |
| `docs`           | Access the Laravel documentation.                                        |
| `down`           | Put the application into maintenance / demo mode.                        |
| `env`            | Display the current framework environment.                               |
| `help`           | Display help for a specific command (e.g.,`php artisan help migrate`). |
| `inspire`        | Display an inspiring quote.                                              |
| `list`           | List all available Artisan commands.                                     |
| `migrate`        | Run the database migrations.                                             |
| `optimize`       | Cache framework bootstrap, configuration, and metadata for performance.  |
| `pail`           | Tails the application logs.                                              |
| `serve`          | Serve the application on the PHP development server.                     |
| `test`           | Run the application tests.                                               |
| `tinker`         | Interact with your application using a REPL environment.                 |
| `up`             | Bring the application out of maintenance mode.                           |

### `auth` Commands

| **Command**     | **Description**                |
| --------------------- | ------------------------------------ |
| `auth:clear-resets` | Flush expired password reset tokens. |

### `cache` Commands

| **Command**          | **Description**                                                          |
| -------------------------- | ------------------------------------------------------------------------------ |
| `cache:clear`            | Flush the application cache.                                                   |
| `cache:forget`           | Remove a specific item from the cache (e.g.,`php artisan cache:forget key`). |
| `cache:prune-stale-tags` | Prune stale cache tags from the cache (Redis only).                            |

### `channel` Commands

| **Command** | **Description**                           |
| ----------------- | ----------------------------------------------- |
| `channel:list`  | List all registered private broadcast channels. |

### `config` Commands

| **Command**  | **Description**                                     |
| ------------------ | --------------------------------------------------------- |
| `config:cache`   | Create a cache file for faster configuration loading.     |
| `config:clear`   | Remove the configuration cache file.                      |
| `config:publish` | Publish configuration files to your application.          |
| `config:show`    | Display all values for a given configuration file or key. |

### `db` Commands

| **Command** | **Description**                                        |
| ----------------- | ------------------------------------------------------------ |
| `db:monitor`    | Monitor the number of connections on the specified database. |
| `db:seed`       | Seed the database with records (using seeders).              |
| `db:show`       | Display information about the given database.                |
| `db:table`      | Display information about the given database table.          |
| `db:wipe`       | Drop all tables, views, and types.                           |

### `env` Commands

| **Command** | **Description**        |
| ----------------- | ---------------------------- |
| `env:decrypt`   | Decrypt an environment file. |
| `env:encrypt`   | Encrypt an environment file. |

### `event` Commands

| **Command** | **Description**                                      |
| ----------------- | ---------------------------------------------------------- |
| `event:cache`   | Discover and cache the application's events and listeners. |
| `event:clear`   | Clear all cached events and listeners.                     |
| `event:list`    | List the application's events and listeners.               |

### `install` Commands

| **Command**        | **Description**                                                      |
| ------------------------ | -------------------------------------------------------------------------- |
| `install:api`          | Create an API routes file and install Laravel Sanctum or Laravel Passport. |
| `install:broadcasting` | Create a broadcasting channel routes file.                                 |

### `key` Commands

| **Command** | **Description**    |
| ----------------- | ------------------------ |
| `key:generate`  | Set the application key. |

### `lang` Commands

| **Command** | **Description**                                   |
| ----------------- | ------------------------------------------------------- |
| `lang:publish`  | Publish all language files available for customization. |

### `make` Commands (Generating New Files)

| **Command**            | **Description**                                        |
| ---------------------------- | ------------------------------------------------------------ |
| `make:cache-table`         | Create a migration for the cache database table.             |
| `make:cast`                | Create a new custom Eloquent cast class.                     |
| `make:channel`             | Create a new channel class (for broadcasting).               |
| `make:class`               | Create a new class.                                          |
| `make:command`             | Create a new Artisan command.                                |
| `make:component`           | Create a new view component class (Blade components).        |
| `make:controller`          | Create a new controller class.                               |
| `make:enum`                | Create a new enum.                                           |
| `make:event`               | Create a new event class.                                    |
| `make:exception`           | Create a new custom exception class.                         |
| `make:factory`             | Create a new model factory (for database seeding).           |
| `make:interface`           | Create a new interface.                                      |
| `make:job`                 | Create a new job class (for queues).                         |
| `make:job-middleware`      | Create a new job middleware class.                           |
| `make:listener`            | Create a new event listener class.                           |
| `make:mail`                | Create a new email class (for sending emails).               |
| `make:middleware`          | Create a new HTTP middleware class.                          |
| `make:migration`           | Create a new migration file (for database schema changes).   |
| `make:model`               | Create a new Eloquent model class.                           |
| `make:notification`        | Create a new notification class.                             |
| `make:notifications-table` | Create a migration for the notifications table.              |
| `make:observer`            | Create a new observer class (for Eloquent model events).     |
| `make:policy`              | Create a new policy class (for authorization).               |
| `make:provider`            | Create a new service provider class.                         |
| `make:queue-batches-table` | Create a migration for the queue batches database table.     |
| `make:queue-failed-table`  | Create a migration for the failed queue jobs database table. |
| `make:queue-table`         | Create a migration for the queue jobs database table.        |
| `make:request`             | Create a new form request class (for request validation).    |
| `make:resource`            | Create a new resource (for API data transformation).         |
| `make:rule`                | Create a new validation rule.                                |
| `make:scope`               | Create a new scope class (for Eloquent model queries).       |
| `make:seeder`              | Create a new seeder class (for database seeding).            |
| `make:session-table`       | Create a migration for the session database table.           |
| `make:test`                | Create a new test class (PHPUnit).                           |
| `make:trait`               | Create a new trait (for code reusability).                   |
| `make:view`                | Create a new Blade view file.                                |

### `migrate` Commands

| **Command**    | **Description**                                                |
| -------------------- | -------------------------------------------------------------------- |
| `migrate:fresh`    | Drop all tables and re-run all migrations.                           |
| `migrate:install`  | Create the migration repository table.                               |
| `migrate:refresh`  | Reset and re-run all migrations.                                     |
| `migrate:reset`    | Rollback all database migrations.                                    |
| `migrate:rollback` | Rollback the last batch of database migrations (or a specific step). |
| `migrate:status`   | Show the status of each migration (pending, run).                    |

### `model` Commands

| **Command** | **Description**                     |
| ----------------- | ----------------------------------------- |
| `model:prune`   | Prune models that are no longer needed.   |
| `model:show`    | Show information about an Eloquent model. |

### `optimize` Commands

| **Command**  | **Description**              |
| ------------------ | ---------------------------------- |
| `optimize:clear` | Remove the cached bootstrap files. |

### `package` Commands

| **Command**    | **Description**                |
| -------------------- | ------------------------------------ |
| `package:discover` | Rebuild the cached package manifest. |

### `pest` Commands

| **Command** | **Description**             |
| ----------------- | --------------------------------- |
| `pest:dataset`  | Create a new dataset file (Pest). |
| `pest:test`     | Create a new test file (Pest).    |

### `queue` Commands

| **Command**       | **Description**                                       |
| ----------------------- | ----------------------------------------------------------- |
| `queue:clear`         | Delete all jobs from the specified queue.                   |
| `queue:failed`        | List all of the failed queue jobs.                          |
| `queue:flush`         | Flush all of the failed queue jobs.                         |
| `queue:forget`        | Delete a specific failed queue job by its ID.               |
| `queue:listen`        | Listen to a given queue and process jobs in the foreground. |
| `queue:monitor`       | Monitor the size of the specified queues.                   |
| `queue:prune-batches` | Prune stale entries from the batches database.              |
| `queue:prune-failed`  | Prune stale entries from the failed jobs table.             |
| `queue:restart`       | Restart queue worker daemons after their current job.       |
| `queue:retry`         | Retry a specific failed queue job by its ID.                |
| `queue:retry-batch`   | Retry the failed jobs for a batch.                          |
| `queue:work`          | Start processing jobs on the queue as a daemon.             |

### `route` Commands

| **Command** | **Description**                                    |
| ----------------- | -------------------------------------------------------- |
| `route:cache`   | Create a route cache file for faster route registration. |
| `route:clear`   | Remove the route cache file.                             |
| `route:list`    | List all registered routes.                              |

### `sail` Commands (Laravel Sail - Docker Development Environment)

| **Command** | **Description**                               |
| ----------------- | --------------------------------------------------- |
| `sail:add`      | Add a service to an existing Sail installation.     |
| `sail:install`  | Install Laravel Sail's default Docker Compose file. |
| `sail:publish`  | Publish the Laravel Sail Docker files.              |

### `schedule` Commands

| **Command**        | **Description**                                    |
| ------------------------ | -------------------------------------------------------- |
| `schedule:clear-cache` | Delete the cached mutex files created by the scheduler.  |
| `schedule:interrupt`   | Interrupt the current schedule run.                      |
| `schedule:list`        | List all scheduled tasks defined in your `Kernel.php`. |
| `schedule:run`         | Run the scheduled commands.                              |
| `schedule:test`        | Run a specific scheduled command.                        |
| `schedule:work`        | Start the schedule worker process.                       |

### `schema` Commands

| **Command** | **Description**           |
| ----------------- | ------------------------------- |
| `schema:dump`   | Dump the given database schema. |

### `storage` Commands

| **Command**  | **Description**                                                         |
| ------------------ | ----------------------------------------------------------------------------- |
| `storage:link`   | Create the symbolic links configured for the application (for public assets). |
| `storage:unlink` | Delete existing symbolic links configured for the application.                |

### `stub` Commands

| **Command** | **Description**                                                         |
| ----------------- | ----------------------------------------------------------------------------- |
| `stub:publish`  | Publish all stubs that are available for customization (e.g., make commands). |

### `vendor` Commands

| **Command**  | **Description**                                                       |
| ------------------ | --------------------------------------------------------------------------- |
| `vendor:publish` | Publish any publishable assets from vendor packages (e.g., configurations). |

### `view` Commands

| **Command** | **Description**                             |
| ----------------- | ------------------------------------------------- |
| `view:cache`    | Compile all of the application's Blade templates. |
| `view:clear`    | Clear all compiled view files.                    |
