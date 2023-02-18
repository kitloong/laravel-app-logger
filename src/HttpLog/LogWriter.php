<?php

namespace KitLoong\AppLogger\HttpLog;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Spatie\HttpLogger\DefaultLogWriter;

class LogWriter extends DefaultLogWriter implements HttpLogWriter
{
    public function log(Request $request, string $uniqId): void
    {
        $message = $this->formatMessage($this->getMessages($request, $uniqId));

        Log::channel(config('app-logger.http.channel'))->info($message);
    }

    protected function getMessages(Request $request, string $uniqId): array
    {
        $files = (new Collection(iterator_to_array($request->files)))
            ->map([$this, 'flatFiles'])
            ->flatten();

        return [
            'uniqid' => $uniqId,
            'method' => strtoupper($request->getMethod()),
            'uri' => $request->getPathInfo(),
            'body' => $request->except(config('app-logger.http.except')),
            'headers' => $request->headers->all(),
            'files' => $files->toArray(),
        ];
    }

    protected function formatMessage(array $message): string
    {
        $bodyAsJson = json_encode($message['body']);
        $headersAsJson = json_encode($message['headers']);
        $files = implode(',', $message['files']);

        // phpcs:ignore
        return "{$message['uniqid']} {$message['method']} {$message['uri']} - Body: {$bodyAsJson} - Headers: {$headersAsJson} - Files: ".$files;
    }
}
