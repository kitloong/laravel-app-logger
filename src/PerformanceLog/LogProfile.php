<?php
/**
 * Created by PhpStorm.
 * User: liow.kitloong
 * Date: 2021/01/08
 */

namespace KitLoong\AppLogger\PerformanceLog;

use Illuminate\Http\Request;

class LogProfile implements PerformanceLogProfile
{
    public function shouldLog(Request $request): bool
    {
        return config('app-logger.performance.enabled') &&
            in_array(strtoupper($request->method()), config('app-logger.performance.should_log'));
    }
}
