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
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @param  string  $uniqId
     * @return void
     */
    public function log(Request $request, $response, string $uniqId): void;
}
