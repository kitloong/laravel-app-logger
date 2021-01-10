<?php
/**
 * Created by PhpStorm.
 * User: liow.kitloong
 * Date: 2021/01/08
 */

namespace KitLoong\AppLogger\Tests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use KitLoong\AppLogger\AppLoggerServiceProvider;
use KitLoong\AppLogger\Middlewares\AppLogger;
use Orchestra\Testbench\TestCase as Testbench;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

abstract class TestCase extends Testbench
{
    const TEST_URI = 'test/uri';

    protected function setupRoutes()
    {
        Route::match(['get', 'post', 'put', 'patch', 'delete'], self::TEST_URI, function () {
            return ['health' => 1];
        })->middleware([
            AppLogger::class
        ]);
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        app()->setBasePath(__DIR__.'/../');

        $app['config']->set('database.connections.single', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true
        ]);

        $app['config']->set('database.default', 'sqlite');

        $app['config']->set('logging.channels.single', [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
        ]);

        $app['config']->set('logging.channels.request', [
            'driver' => 'single',
            'path' => storage_path('logs/request.log'),
            'level' => 'debug',
        ]);

        $app['config']->set('logging.channels.performance', [
            'driver' => 'single',
            'path' => storage_path('logs/performance.log'),
            'level' => 'debug',
        ]);

        $app['config']->set('logging.channels.query', [
            'driver' => 'single',
            'path' => storage_path('logs/query.log'),
            'level' => 'debug',
        ]);

        $app['config']->set('logging.default', 'single');
    }

    protected function getPackageProviders($app)
    {
        return [
            AppLoggerServiceProvider::class
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->setupRoutes();
        $this->refreshStoragePath();
    }

    protected function refreshStoragePath()
    {
        File::deleteDirectory(storage_path());
        File::makeDirectory(storage_path());
    }

    protected function makeRequest(
        string $method,
        string $uri,
        array $parameters = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        $content = null
    ): Request {
        $files = array_merge($files, $this->extractFilesFromDataArray($parameters));

        return Request::createFromBase(
            SymfonyRequest::create(
                $this->prepareUrlForRequest($uri),
                $method,
                $parameters,
                $cookies,
                $files,
                array_replace($this->serverVariables, $server),
                $content
            )
        );
    }
}
