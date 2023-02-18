<?php

namespace KitLoong\AppLogger\QueryLog;

class LogProfile implements QueryLogProfile
{
    public function shouldLog(): bool
    {
        return config('app-logger.query.enabled');
    }
}
