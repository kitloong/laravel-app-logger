<?php

namespace KitLoong\AppLogger\Tests\HttpLog;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use KitLoong\AppLogger\HttpLog\LogWriter;
use KitLoong\AppLogger\Tests\TestCase;

class LogWriterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('app-logger.http.except', ['password']);
        Config::set('app-logger.http.channel', 'request');
    }

    public function testLog()
    {
        $logWriter = $this->getLogWriter();

        $request = $this->makeRequest(Request::METHOD_POST, self::TEST_URI);

        Log::shouldReceive('channel')
            ->with('request')
            ->andReturnSelf()
            ->once();

        Log::shouldReceive('info')
            ->withArgs(function ($args) {
                $this->assertStringMatchesFormat(
                    'test-uniqid POST /test/uri - Body: %s - Headers: %s - Files: ',
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

        $testFile = UploadedFile::fake()->create('test.txt');

        $request = $this->makeRequest(
            Request::METHOD_POST,
            self::TEST_URI,
            [
                'name' => 'Name',
                'password' => 'password',
            ],
            [],
            [
                'file' => $testFile,
            ]
        );
        $message = $logWriter->testGetMessages($request, 'test-uniqid');

        $this->assertSame([
            'uniqid' => 'test-uniqid',
            'method' => 'POST',
            'uri' => $request->getPathInfo(),
            'body' => [
                'name' => 'Name',
                'file' => $testFile,
            ],
            'headers' => $request->headers->all(),
            'files' => [
                'test.txt',
            ],
        ], $message);
    }

    public function testFormatMessage()
    {
        $logWriter = $this->getLogWriter();
        $message = $logWriter->testFormatMessage([
            'uniqid' => 'test-uniqid',
            'method' => 'post',
            'uri' => '/test/url',
            'body' => [
                'name' => 'Name',
            ],
            'headers' => [
                'accept' => 'text/html',
            ],
            'files' => [
                'test.txt',
            ],
        ]);

        $this->assertSame(
            'test-uniqid post /test/url - Body: {"name":"Name"} - Headers: {"accept":"text\/html"} - Files: test.txt',
            $message
        );
    }

    private function getLogWriter(): LogWriter
    {
        return new class() extends LogWriter {
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
