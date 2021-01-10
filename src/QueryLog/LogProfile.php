<?php
/**
 * Created by PhpStorm.
 * User: liow.kitloong
 * Date: 2021/01/09
 */

namespace KitLoong\AppLogger\QueryLog;

class LogProfile implements QueryLogProfile
{
    public function shouldLog(): bool
    {
        return config('app-logger.query.enabled');
    }
}
