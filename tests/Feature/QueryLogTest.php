<?php

namespace KitLoong\AppLogger\Tests\Feature;

use Illuminate\Support\Facades\DB;
use KitLoong\AppLogger\Tests\TestCase;

class QueryLogTest extends TestCase
{
    public function testLog()
    {
        $this->assertFalse(file_exists(storage_path('logs/laravel.log')));

        $this->loadMigrationsFrom(base_path('tests/migrations'));

        DB::table('users')->insert([
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'password',
        ]);

        $users = DB::select('select * from users where id = :id and name = :name', ['id' => 1, 'name' => 'Name']);
        $this->assertNotEmpty($users);

        $users = DB::select('select * from users where id = :id and name = :name', ['name' => 'Name', 'id' => 1]);
        $this->assertNotEmpty($users);

        $users = DB::select('select * from users where id = ? and name = :name', [1, 'name' => 'Name']);
        $this->assertNotEmpty($users);

        $users = DB::select('select * from users where id = :id and name = ?', ['id' => 1, 1 => 'Name']);
        $this->assertNotEmpty($users);

        $this->assertFileExists(storage_path('logs/laravel.log'));
    }
}
