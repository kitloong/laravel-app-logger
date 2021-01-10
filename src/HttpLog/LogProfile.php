<?php
/**
 * Created by PhpStorm.
 * User: liow.kitloong
 * Date: 2021/01/08
 */

namespace KitLoong\AppLogger\HttpLog;

use Illuminate\Http\Request;

class LogProfile implements HttpLogProfile
{
    public function shouldLog(Request $request): bool
    {
        return config('app-logger.http.enabled') &&
            in_array(strtoupper($request->method()), config('app-logger.http.should_log'));
    }
}
