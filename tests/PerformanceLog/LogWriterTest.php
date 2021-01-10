<?php
/**
 * Created by PhpStorm.
 * User: liow.kitloong
 * Date: 2021/01/10
 */

namespace KitLoong\AppLogger\Tests\PerformanceLog;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use KitLoong\AppLogger\PerformanceLog\LogWriter;
use KitLoong\AppLogger\Tests\TestCase;

class LogWriterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('app-logger.performance.channel', 'performance');
    }

    public function testConstruct()
    {
        $logWriter = $this->getLogWriter();
        $this->assertIsFloat($logWriter->getStart());

        define('LARAVEL_START', 12345);
        $logWriter = $this->getLogWriter();
        $this->assertSame(12345, $logWriter->getStart());
    }

    public function testLog()
    {
        $logWriter = $this->getLogWriter();

        $request = $this->makeRequest(Request::METHOD_POST, self::TEST_URI);

        Log::shouldReceive('channel')
            ->with('performance')
            ->andReturnSelf()
            ->once();

        Log::shouldReceive('info')
            ->withArgs(function ($args) {
                $this->assertStringMatchesFormat(
                    'test-uniqid POST /test/uri - Time: %f - Memory: %f',
                    $args
                );
                return true;
            })
            ->once();

        $logWriter->log($request, 'test-uniqid');
    }

    public function testGetMessages()
    {
        $logWriter = $this->getLogWriter();

        $request = $this->makeRequest(Request::METHOD_POST, self::TEST_URI);

        $messages = $logWriter->testGetMessages($request, 'test-uniqid');

        $this->assertSame(5, count($messages));
        $this->assertSame('test-uniqid', $messages['uniqid']);
        $this->assertSame('POST', $messages['method']);
        $this->assertSame($request->getPathInfo(), $messages['uri']);
        $this->assertArrayHasKey('time', $messages);
        $this->assertArrayHasKey('memory', $messages);
    }

    public function testFormatMessage()
    {
        $logWriter = $this->getLogWriter();

        $message = $logWriter->testFormatMessage([
            'uniqid' => 'test-uniqid',
            'method' => 'post',
            'uri' => '/test/url',
            'time' => '50',
            'memory' => '20',
        ]);

        $this->assertSame('test-uniqid post /test/url - Time: 50 - Memory: 20', $message);
    }

    private function getLogWriter(): LogWriter
    {
        return new class() extends LogWriter {
            public function getStart()
            {
                return $this->start;
            }

            public function testGetMessages(Request $request, string $uniqId): array
            {
                return $this->getMessages($request, $uniqId);
            }

            public function testFormatMessage(array $message): string
            {
                return $this->formatMessage($message);
            }
        };
    }
}
