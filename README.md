# Laravel Application Logger

![Run tests](https://github.com/kitloong/laravel-app-logger/workflows/Run%20tests/badge.svg?branch=main)
[![Latest Stable Version](https://poser.pugx.org/kitloong/laravel-app-logger/v/stable.png)](https://packagist.org/packages/kitloong/laravel-app-logger)
[![License](https://poser.pugx.org/kitloong/laravel-app-logger/license.png)](https://packagist.org/packages/kitloong/laravel-app-logger)

This package provides middleware that generate **http request** and **performance** logs for you application.

This package also provides Database **query log** to track all executed queries by your application.

## Installation

```bash
composer require "kitloong/laravel-app-logger"
```

## Usage

To start using **http request** and **performance** logger please add package's middleware in your `app/Http/Kernel.php` or routes.

```
\KitLoong\AppLogger\Middlewares\AppLogger::class
```

No code modification needed to use **DB query log**, you only need to enable it through `.env`.

## Configuration

By default, **Http request** and **performance** are enabled while **query log** is disabled.

However, you could change each setting respectively by difference environment.

```dotenv
# By default

RUN_HTTP_LOG=true
RUN_PERFORMANCE_LOG=true
RUN_QUERY_LOG=false
```

You could also publish config file to change more configuration or even use your own implementation

```bash
php artisan vendor:publish --provider="KitLoong\AppLogger\AppLoggerServiceProvider" --tag=config
```

This is content of config file

```
[
    'http' => [
        'enabled' => env('RUN_HTTP_LOG', true),

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

        /*
         * If you are using default `HttpLogProfile` provided by the package,
         * you could define which HTTP methods should be logged.
         */
        'should_log' => [
            \Illuminate\Http\Request::METHOD_POST,
            \Illuminate\Http\Request::METHOD_PUT,
            \Illuminate\Http\Request::METHOD_PATCH,
            \Illuminate\Http\Request::METHOD_DELETE,
        ],

        /*
         * Filter out body fields which will never be logged.
         */
        'except' => [
            'password',
            'password_confirmation',
        ],

        /*
         * Log channel name define in config/logging.php
         * null value to use default channel.
         */
        'channel' => null,
    ],

    'performance' => [
        'enabled' => env('RUN_PERFORMANCE_LOG', true),

        /*
         * The log profile which determines whether a request should be logged.
         * It should implement `PerformanceLogProfile`.
         */
        'log_profile' => \KitLoong\AppLogger\PerformanceLog\LogProfile::class,

        /*
         * The log writer used to write the request to a log.
         * It should implement `PerformanceLogWriter`.
         */
        'log_writer' => \KitLoong\AppLogger\PerformanceLog\LogWriter::class,

        /*
         * If you are using default `PerformanceLogProfile` provided by the package,
         * you could define which HTTP methods should be logged.
         */
        'should_log' => [
            \Illuminate\Http\Request::METHOD_GET,
            \Illuminate\Http\Request::METHOD_POST,
            \Illuminate\Http\Request::METHOD_PUT,
            \Illuminate\Http\Request::METHOD_PATCH,
            \Illuminate\Http\Request::METHOD_DELETE,
        ],

        /*
         * Log channel name define in config/logging.php
         * null value to use default channel.
         */
        'channel' => null,
    ],

    'query' => [
        'enabled' => env('RUN_QUERY_LOG', false),

        /*
         * The log profile which determines whether a request should be logged.
         * It should implement `QueryLogProfile`.
         */
        'log_profile' => \KitLoong\AppLogger\QueryLog\LogProfile::class,

        /*
         * The log writer used to write the request to a log.
         * It should implement `QueryLogWriter`.
         */
        'log_writer' => \KitLoong\AppLogger\QueryLog\LogWriter::class,

        /*
         * Log channel name define in config/logging.php
         * null value to use default channel.
         */
        'channel' => null,
    ],
];
```

This package used https://github.com/spatie/laravel-http-logger as base for **http request** log, as well as the code design pattern.

We could receive tons of access in a real life production application.

In order to ease for analyze, a unique string is embedded into **http request** and **performance** log to indicate both log entries are related.

```bash
# Http request, unique: 2725ffb10adeae3f
[2021-01-10 23:35:25] local.INFO: 2725ffb10adeae3f GET /path - Body ...

# Performance, unique: 2725ffb10adeae3f
[2021-01-10 23:35:27] local.INFO: 2725ffb10adeae3f GET /path - Time: 55 - Memory: 5
```

If you found any high memory usage or slow requests you could easily grep request log by the unique string for more information.  

# License

The Laravel Application Logger is open-sourced software licensed under the [MIT license](LICENSE)
