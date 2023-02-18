<?php

namespace KitLoong\AppLogger\PerformanceLog;

use Illuminate\Http\Request;

interface PerformanceLogProfile
{
    public function shouldLog(Request $request): bool;
}
