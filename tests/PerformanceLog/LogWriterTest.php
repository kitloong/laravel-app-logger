<?php

namespace KitLoong\AppLogger\Tests\PerformanceLog;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
                    'test-uniqid POST /test/uri %d - Time: %f ms - Memory: %f MiB',
                    $args
                );
                return true;
            })
            ->once();

        $response = new Response('content', Response::HTTP_OK);

        $logWriter->log($request, $response, 'test-uniqid');
    }

    public function testGetMessages()
    {
        $logWriter = $this->getLogWriter();

        $request = $this->makeRequest(Request::METHOD_POST, self::TEST_URI);
        $response = new Response('content', Response::HTTP_OK);

        $messages = $logWriter->testGetMessages($request, $response, 'test-uniqid');

        $this->assertSame(6, count($messages));
        $this->assertSame('test-uniqid', $messages['uniqid']);
        $this->assertSame('POST', $messages['method']);
        $this->assertSame($request->getPathInfo(), $messages['uri']);
        $this->assertSame(Response::HTTP_OK, $messages['status']);
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
            'status' => '200',
            'time' => '50',
            'memory' => '20',
        ]);

        $this->assertSame('test-uniqid post /test/url 200 - Time: 50 ms - Memory: 20 MiB', $message);
    }

    private function getLogWriter(): LogWriter
    {
        return new class() extends LogWriter {
            public function getStart()
            {
                return $this->start;
            }

            public function testGetMessages(Request $request, $response, string $uniqId): array
            {
                return $this->getMessages($request, $response, $uniqId);
            }

            public function testFormatMessage(array $message): string
            {
                return $this->formatMessage($message);
            }
        };
    }
}
