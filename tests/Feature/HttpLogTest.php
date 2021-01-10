<?php
/**
 * Created by PhpStorm.
 * User: liow.kitloong
 * Date: 2021/01/08
 */

namespace KitLoong\AppLogger\Tests\Feature;

use KitLoong\AppLogger\Tests\TestCase;

class HttpLogTest extends TestCase
{
    public function testLog()
    {
        $this->assertFalse(file_exists(storage_path('logs/laravel.log')));

        $this->post(self::TEST_URI)
            ->assertJson(['health' => 1]);

        $this->assertFileExists(storage_path('logs/laravel.log'));
    }
}
