<?php

namespace KitLoong\AppLogger\Tests\QueryLog;

use Illuminate\Support\Facades\Config;
use KitLoong\AppLogger\QueryLog\LogProfile;
use KitLoong\AppLogger\Tests\TestCase;

class LogProfileTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('app-logger.query.enabled', true);
    }

    public function testShouldLog()
    {
        $this->assertTrue(app(LogProfile::class)->shouldLog());
    }

    public function testShouldLogDisabled()
    {
        Config::set('app-logger.query.enabled', false);
        $this->assertFalse(app(LogProfile::class)->shouldLog());
    }
}
