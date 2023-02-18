<?php

namespace KitLoong\AppLogger\HttpLog;

use Illuminate\Http\Request;

interface HttpLogProfile
{
    public function shouldLog(Request $request): bool;
}
