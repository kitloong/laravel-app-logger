<?php
/**
 * Created by PhpStorm.
 * User: liow.kitloong
 * Date: 2020/10/14
 */

namespace KitLoong\AppLogger\QueryLog;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogWriter implements QueryLogWriter
{
    public function log(): void
    {
        DB::listen(function (QueryExecuted $query) {
            $message = $this->formatMessage($this->getMessages($query));

            Log::channel(config('app-logger.query.channel'))->info($message);
        });
    }

    protected function getMessages(QueryExecuted $query): array
    {
        $sql = $query->sql;

        foreach ($query->bindings as $key => $binding) {
            // https://github.com/barryvdh/laravel-debugbar/blob/master/src/DataCollector/QueryCollector.php#L138
            // This regex matches placeholders only, not the question marks,
            // nested in quotes, while we iterate through the bindings
            // and substitute placeholders by suitable values.
            $regex = is_numeric($key)
                ? "/(?<!\?)\?(?=(?:[^'\\\']*'[^'\\']*')*[^'\\\']*$)(?!\?)/"
                : "/:{$key}(?=(?:[^'\\\']*'[^'\\\']*')*[^'\\\']*$)/";

            // Mimic bindValue and only quote non-integer and non-float data types
            if (!is_int($binding) && !is_float($binding)) {
                $binding = $this->quote($binding);
            }

            $sql = preg_replace($regex, $binding, $sql, 1);
        }

        return [
            'time' => $query->time,
            'connection_name' => $query->connectionName,
            'sql' => $sql,
        ];
    }

    protected function formatMessage(array $message): string
    {
        return "Took: {$message['time']} ms {$message['connection_name']} Sql: {$message['sql']}";
    }

    /**
     * Mimic mysql_real_escape_string
     *
     * @param  string  $value
     * @return string
     */
    protected function quote(string $value): string
    {
        $search = ['\\', "\x00", "\n", "\r", "'", '"', "\x1a"];
        $replace = ['\\\\', '\\0', '\\n', '\\r', "\'", '\"', '\\Z'];

        return "'".str_replace($search, $replace, $value)."'";
    }
}
