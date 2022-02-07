<?php

namespace KitLoong\AppLogger\Middlewares;

use Closure;
use Illuminate\Http\Request;
use KitLoong\AppLogger\HttpLog\HttpLogProfile;
use KitLoong\AppLogger\HttpLog\HttpLogWriter;
use KitLoong\AppLogger\PerformanceLog\PerformanceLogProfile;
use KitLoong\AppLogger\PerformanceLog\PerformanceLogWriter;

class AppLogger
{
    protected $httpLogProfile;

    protected $httpLogWriter;

    protected $performanceLogProfile;

    protected $performanceLogWriter;

    protected $uniqId;

    public function __construct(
        HttpLogProfile $httpLogProfile,
        HttpLogWriter $httpLogWriter,
        PerformanceLogProfile $performanceLogProfile,
        PerformanceLogWriter $performanceLogWriter
    ) {
        $this->httpLogProfile = $httpLogProfile;
        $this->httpLogWriter = $httpLogWriter;
        $this->performanceLogProfile = $performanceLogProfile;
        $this->performanceLogWriter = $performanceLogWriter;
    }

    public function handle(Request $request, Closure $next)
    {
        $this->uniqId = uniqid(getmypid());

        if ($this->httpLogProfile->shouldLog($request)) {
            $this->httpLogWriter->log($request, $this->uniqId);
        }

        return $next($request);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     */
    public function terminate(Request $request, $response)
    {
        if ($this->performanceLogProfile->shouldLog($request)) {
            $this->performanceLogWriter->log($request, $response, $this->uniqId);
        }
    }
}
