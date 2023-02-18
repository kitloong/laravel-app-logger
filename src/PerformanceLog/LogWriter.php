<?php

namespace KitLoong\AppLogger\PerformanceLog;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogWriter implements PerformanceLogWriter
{
    /**
     * Application start time in microtime
     *
     * @var float|string
     */
    protected $start;

    public function __construct()
    {
        if (defined('LARAVEL_START')) {
            $this->start = LARAVEL_START;
        } else {
            $this->start = microtime(true);
        }
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @param  string  $uniqId
     * @return void
     */
    public function log(Request $request, $response, string $uniqId): void
    {
        $message = $this->formatMessage($this->getMessages($request, $response, $uniqId));

        Log::channel(config('app-logger.performance.channel'))->info($message);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @param  string  $uniqId
     * @return array
     */
    protected function getMessages(Request $request, $response, string $uniqId): array
    {
        return [
            'uniqid' => $uniqId,
            'method' => strtoupper($request->getMethod()),
            'uri' => $request->getPathInfo(),
            'status' => $response->getStatusCode(),
            'time' => $this->getTimeInMilliSeconds(),
            'memory' => $this->getMemoryInMB(),
        ];
    }

    protected function getTimeInMilliSeconds(): string
    {
        $milliSeconds = (microtime(true) - $this->start) * 1000;
        return (string) round($milliSeconds, 2);
    }

    protected function getMemoryInMB(): string
    {
        return (string) round(memory_get_peak_usage(true) / 1048576, 2);
    }

    protected function formatMessage(array $message): string
    {
        // phpcs:ignore
        return "{$message['uniqid']} {$message['method']} {$message['uri']} {$message['status']} - Time: {$message['time']} ms - Memory: {$message['memory']} MiB";
    }
}
