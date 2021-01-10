<?php
/**
 * Created by PhpStorm.
 * User: liow.kitloong
 * Date: 2021/01/09
 */

namespace KitLoong\AppLogger\QueryLog;

interface QueryLogWriter
{
    public function log(): void;
}
