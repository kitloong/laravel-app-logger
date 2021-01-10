<?php

namespace KitLoong\AppLogger;

use Illuminate\Support\ServiceProvider;
use KitLoong\AppLogger\HttpLog\HttpLogProfile;
use KitLoong\AppLogger\HttpLog\HttpLogWriter;
use KitLoong\AppLogger\Middlewares\AppLogger;
use KitLoong\AppLogger\PerformanceLog\PerformanceLogProfile;
use KitLoong\AppLogger\PerformanceLog\PerformanceLogWriter;
use KitLoong\AppLogger\QueryLog\QueryLogProfile;
use KitLoong\AppLogger\QueryLog\QueryLogWriter;

class AppLoggerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/app-logger.php', 'app-logger');

        $this->app->singleton(HttpLogProfile::class, config('app-logger.http.log_profile'));
        $this->app->singleton(HttpLogWriter::class, config('app-logger.http.log_writer'));
        $this->app->singleton(PerformanceLogProfile::class, config('app-logger.performance.log_profile'));
        $this->app->singleton(PerformanceLogWriter::class, config('app-logger.performance.log_writer'));
        $this->app->singleton(QueryLogProfile::class, config('app-logger.query.log_profile'));
        $this->app->singleton(QueryLogWriter::class, config('app-logger.query.log_writer'));

        $this->app->singleton(AppLogger::class);
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/app-logger.php' => config_path('app-logger.php'),
        ], 'config');

        if (app(QueryLogProfile::class)->shouldLog()) {
            app(QueryLogWriter::class)->log();
        }
    }
}
