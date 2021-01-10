<?php
/**
 * Created by PhpStorm.
 * User: liow.kitloong
 * Date: 2021/01/08
 */

namespace KitLoong\AppLogger\PerformanceLog;

use Illuminate\Http\Request;

interface PerformanceLogWriter
{
    public function log(Request $request, string $uniqId): void;
}
