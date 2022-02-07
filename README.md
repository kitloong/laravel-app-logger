# Laravel Application Logger

![Run tests](https://github.com/kitloong/laravel-app-logger/workflows/Run%20tests/badge.svg?branch=1.x)
[![Latest Stable Version](https://poser.pugx.org/kitloong/laravel-app-logger/v/stable.png)](https://packagist.org/packages/kitloong/laravel-app-logger)
[![License](https://poser.pugx.org/kitloong/laravel-app-logger/license.png)](https://packagist.org/packages/kitloong/laravel-app-logger)

This package provides middleware that generates **HTTP request** and **performance** logs from incoming requests.

This package also provides a Database **query log** to log all executed queries in your application.

## Installation

```bash
composer require kitloong/laravel-app-logger
```

## Usage

To start using **HTTP request** and **performance** logger please add the package's middleware in your `app/Http/Kernel.php` or routes.

```
\KitLoong\AppLogger\Middlewares\AppLogger::class
```

No code modification needed to use the Database **query log**, you only need to enable it through `.env`.

By default, **HTTP request** and **performance** are enabled while the **query log** is disabled.

However, you could change each setting respectively to a different environment.

```dotenv
# By default

RUN_HTTP_LOG=true
RUN_PERFORMANCE_LOG=true
RUN_QUERY_LOG=false
```

## Log format

### Http request log

```log
[2021-01-10 23:35:27] local.INFO: 2725ffb10adeae3f POST /path - Body: {"test":true} - Headers: {"cookie":["Phpstorm-12345"],"accept-language":["en-GB"]} - Files: uploaded.txt
```

### Performance log

```log
[2021-01-10 23:35:27] local.INFO: 2725ffb10adeae3f POST /path 201 - Time: 55.82 ms - Memory: 22.12 MiB
```

### Query log

```log
[2021-01-10 23:35:27] local.INFO: Took: 2.45 ms mysql Sql: select * from `users` where `id` = 1
```

## What's more

This package uses https://github.com/spatie/laravel-http-logger as the base for the **HTTP request** log, as well as the code design pattern.

It is common to receive tons of incoming requests in a real-life production application.

To ease for analysis, a unique string is embedded into **HTTP request** and **performance** log to indicate both log entries are related.

```log
# HTTP request, unique: 2725ffb10adeae3f
[2021-01-10 23:35:25] local.INFO: 2725ffb10adeae3f GET /path - Body ...

# Performance, unique: 2725ffb10adeae3f
[2021-01-10 23:35:27] local.INFO: 2725ffb10adeae3f GET /path 200 - Time: 55.82 ms - Memory: 5.12 MiB
```

If you found any high memory usage or slow requests you could easily grep request log by the unique string for more information.

## Configuration

You could also publish the config file to change more configuration or even use your own implementation:

```bash
php artisan vendor:publish --provider="KitLoong\AppLogger\AppLoggerServiceProvider" --tag=config
```

You could check the content of the config file [here](config/app-logger.php).

### Config: Logging channel

By default, Laravel App Logger writes logs into your default logging channel.

However, you may implement a new logging channel in Laravel `config/logging.php`, and overwrite the `channel` in the published config file.

An example is written for a better explanation.

In Laravel `config/logging.php`:

```bash

'channels' => [
    'request' => [
        'driver' => 'daily',
        'path' => storage_path('logs/request.log'),
        'level' => 'debug',
        'days' => 14,
    ],
    
    'performance' => [
        'driver' => 'daily',
        'path' => storage_path('logs/performance.log'),
        'level' => 'debug',
        'days' => 14,
    ],
    
    'query' => [
        'driver' => 'daily',
        'path' => storage_path('logs/query.log'),
        'level' => 'debug',
        'days' => 14,
    ],
]
```

In `config/app-logger.php`:

```bash
'http' => [
    ...
    'channel' => 'request'
],
'performance' => [
    ...
    'channel' => 'performance'
],
'query' => [
    ...
    'channel' => 'query'
]
```

### Config: Implement own logger

You could even write your own logger implementation and overwrite it in the config file.

Here is the code snippet of **HTTP request**:

```bash
/*
 * The log profile which determines whether a request should be logged.
 * It should implement `HttpLogProfile`.
 */
'log_profile' => \KitLoong\AppLogger\HttpLog\LogProfile::class,

/*
 * The log writer used to write the request to a log.
 * It should implement `HttpLogWriter`.
 */
'log_writer' => \KitLoong\AppLogger\HttpLog\LogWriter::class,
```

You could find a similar configuration in the `performance` and `query` section. 

When you write your own `log_profile`, you must implement each loggers' own `LogProfile` interface.

|Logger|Interface|
|---|---|
|http|`\KitLoong\AppLogger\HttpLog\HttpLogProfile`|
|performance|`\KitLoong\AppLogger\PerformanceLog\PerformanceLogProfile`|
|query|`\KitLoong\AppLogger\QueryLog\QueryLogProfile`|

The interface requires `shouldLog` implementation. This is where you place your log condition.

When you write your own `log_writer`, you must implement each loggers' own `LogWriter` interface.

|Logger|Interface|
|---|---|
|http|`\KitLoong\AppLogger\HttpLog\HttpLogWriter`|
|performance|`\KitLoong\AppLogger\PerformanceLog\PerformanceLogWriter`|
|query|`\KitLoong\AppLogger\QueryLog\QueryLogWriter`|

The interface requires `log` implementation. This is where you define your log body message.

# License

The Laravel Application Logger is open-sourced software licensed under the [MIT license](LICENSE)
