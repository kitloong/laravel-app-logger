<?php

namespace KitLoong\AppLogger\Tests\HttpLog;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use KitLoong\AppLogger\HttpLog\LogProfile;
use KitLoong\AppLogger\Tests\TestCase;

class LogProfileTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('app-logger.http.enabled', true);
        Config::set('app-logger.http.should_log', [Request::METHOD_POST]);
    }

    public function testShouldLog()
    {
        $this->assertTrue($this->checkShouldLog());
    }

    public function testShouldLogDisabled()
    {
        Config::set('app-logger.http.enabled', false);
        $this->assertFalse($this->checkShouldLog());
    }

    public function testShouldLogMethodNotAllow()
    {
        Config::set('app-logger.http.should_log', [Request::METHOD_GET]);
        $this->assertFalse($this->checkShouldLog());
    }

    private function checkShouldLog(): bool
    {
        $logProfile = app(LogProfile::class);

        $request = $this->makeRequest(
            Request::METHOD_POST,
            self::TEST_URI
        );

        return $logProfile->shouldLog($request);
    }
}
