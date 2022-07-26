<?php

namespace MyCoolPay\Logging;

use Exception;

interface LoggerInterface
{
    /**
     * @param string $message
     * @param int $log_level
     * @return false|int
     */
    public function log($message, $log_level = LogLevel::INFO);

    /**
     * @param string $message
     * @return false|int
     */
    public function error($message);

    /**
     * @param string $message
     * @return false|int
     */
    public function warning($message);

    /**
     * @param string $message
     * @return false|int
     */
    public function info($message);

    /**
     * @param string $message
     * @return false|int
     */
    public function debug($message);

    /**
     * @param Exception $exception
     * @return false|int
     */
    public function logException($exception);
}
