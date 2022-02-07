<?php
/**
 * Created by PhpStorm.
 * User: liow.kitloong
 * Date: 2021/01/10
 */

namespace KitLoong\AppLogger\Tests\QueryLog;

use Closure;
use Illuminate\Database\Connection;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use KitLoong\AppLogger\QueryLog\LogWriter;
use KitLoong\AppLogger\Tests\TestCase;
use Mockery;

class LogWriterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('app-logger.query.channel', 'query');
    }

    public function testLog()
    {
        $logWriter = $this->getLogWriter();

        DB::shouldReceive('listen')
            ->with(Mockery::on(function (Closure $closure) {
                $connection = Mockery::mock(Connection::class);
                $connection->shouldReceive('getName')
                        ->andReturn('mysql')
                        ->once();
                $query = new QueryExecuted(
                    'SELECT * FROM users WHERE id = :id',
                    [
                        'id' => 1,
                    ],
                    10,
                    $connection
                );
                $closure($query);
                return true;
            }))
            ->once();

        Log::shouldReceive('channel')
            ->with('query')
            ->andReturnSelf()
            ->once();

        Log::shouldReceive('info')
            ->with('Took: 10 ms mysql Sql: SELECT * FROM users WHERE id = 1')
            ->once();

        $logWriter->log();
    }

    public function testGetMessage()
    {
        $connection = Mockery::mock(Connection::class);
        $connection->shouldReceive('getName')
            ->andReturn('mysql')
            ->once();

        $query = new QueryExecuted(
            'SELECT * FROM users WHERE id = :id AND name = :name',
            [
                'id' => 1,
                'name' => 'Name',
            ],
            10,
            $connection
        );

        $logWriter = $this->getLogWriter();
        $messages = $logWriter->testGetMessages($query);

        $this->assertSame([
            'time' => 10,
            'connection_name' => 'mysql',
            'sql' => "SELECT * FROM users WHERE id = 1 AND name = 'Name'",
        ], $messages);
    }

    public function testGetMessageWithNull()
    {
        $connection = Mockery::mock(Connection::class);
        $connection->shouldReceive('getName')
            ->andReturn('mysql')
            ->once();

        $query = new QueryExecuted(
            'INSERT INTO users SET name = :name, email = :email, remember_token = :remember_token',
            [
                'id' => 1,
                'name' => 'Name',
                'remember_token' => null,
            ],
            10,
            $connection
        );

        $logWriter = $this->getLogWriter();
        $messages = $logWriter->testGetMessages($query);

        $this->assertSame([
            'time' => 10,
            'connection_name' => 'mysql',
            'sql' => "INSERT INTO users SET name = 'Name', email = :email, remember_token = null",
        ], $messages);
    }

    public function testFormatMessage()
    {
        $logWriter = $this->getLogWriter();

        $message = $logWriter->testFormatMessage([
            'time' => 10,
            'connection_name' => 'mysql',
            'sql' => "SELECT * FROM users WHERE id = 1 AND name = 'Name'",
        ]);

        $this->assertSame(
            "Took: 10 ms mysql Sql: SELECT * FROM users WHERE id = 1 AND name = 'Name'",
            $message
        );
    }

    public function testQuote()
    {
        $logWriter = $this->getLogWriter();
        $escaped = $logWriter->testQuote("string with \\, \x00, \n, \r, ', \", \x1a");
        $this->assertSame(
            '\'string with \\\\, \0, \n, \r, \\\', \", \Z\'',
            $escaped
        );
    }

    private function getLogWriter(): LogWriter
    {
        return new class() extends LogWriter {
            public function testGetMessages(QueryExecuted $query): array
            {
                return $this->getMessages($query);
            }

            public function testFormatMessage(array $message): string
            {
                return $this->formatMessage($message);
            }

            public function testQuote(string $value): string
            {
                return $this->quote($value);
            }
        };
    }
}
