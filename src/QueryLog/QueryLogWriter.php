<?php

namespace KitLoong\AppLogger\QueryLog;

interface QueryLogWriter
{
    public function log(): void;
}
