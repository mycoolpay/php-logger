<?php

namespace MyCoolPay\Logging;

class LogLevel
{
    const ERROR = 10;
    const WARNING = 20;
    const INFO = 30;
    const DEBUG = 40;

    /**
     * @param int $log_level
     * @return ?string
     */
    public static function getTitle($log_level)
    {
        if ($log_level === self::ERROR)
            return 'ERROR';
        elseif ($log_level === self::WARNING)
            return 'WARNING';
        elseif ($log_level === self::INFO)
            return 'INFO';
        elseif ($log_level === self::DEBUG)
            return 'DEBUG';
        else
            return null;
    }
}
