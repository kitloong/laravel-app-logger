<?php
/**
 * Created by PhpStorm.
 * User: liow.kitloong
 * Date: 2021/01/08
 */

namespace KitLoong\AppLogger\HttpLog;

use Illuminate\Http\Request;

interface HttpLogWriter
{
    public function log(Request $request, string $uniqId): void;
}
