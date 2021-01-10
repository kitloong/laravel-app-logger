<?php
/**
 * Created by PhpStorm.
 * User: liow.kitloong
 * Date: 2021/01/08
 */

namespace KitLoong\AppLogger\PerformanceLog;

use Illuminate\Http\Request;

interface PerformanceLogProfile
{
    public function shouldLog(Request $request): bool;
}
