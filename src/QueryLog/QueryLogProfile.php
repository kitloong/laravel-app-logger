<?php

namespace KitLoong\AppLogger\QueryLog;

interface QueryLogProfile
{
    public function shouldLog(): bool;
}
