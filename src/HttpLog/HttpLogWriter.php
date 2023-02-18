<?php

namespace KitLoong\AppLogger\HttpLog;

use Illuminate\Http\Request;

interface HttpLogWriter
{
    public function log(Request $request, string $uniqId): void;
}
