<?php

namespace MyCoolPay\Logging;

use DateTime;
use DateTimeZone;
use Exception;

class Logger implements LoggerInterface
{
    /**
     * @var string $filename
     */
    protected $filename;
    /**
     * @var string $dir
     */
    protected $dir;
    /**
     * @var string $datetime_format
     */
    protected $datetime_format;

    /**
     * @param string $filename
     * @param string|null $dir
     * @param int $permissions
     * @param string $datetime_format
     */
    public function __construct($filename = 'app.log', $dir = null, $permissions = 0777, $datetime_format = '[Y-m-d\TH:i:sP]')
    {
        if (is_null($dir))
            $dir = sys_get_temp_dir();
        if (preg_match('#[/\\\]$#', $dir))
            $dir = preg_replace('#[/\\\]$#', '', $dir); // Remove trailing slash
        if (!file_exists($dir))
            mkdir($dir, $permissions, true);

        $this->dir = $dir;
        $this->filename = $filename;
        $this->datetime_format = $datetime_format;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     * @return $this
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return string
     */
    public function getDir()
    {
        return $this->dir;
    }

    /**
     * @return string
     */
    public function getFilepath()
    {
        return $this->dir . DIRECTORY_SEPARATOR . $this->filename;
    }

    /**
     * @return string
     */
    private function getDatetime()
    {
        try {
            return (new DateTime('now', new DateTimeZone('UTC')))
                ->format($this->datetime_format);

        } catch (Exception $exception) {
            return date($this->datetime_format);
        }
    }

    /**
     * @inheritDoc
     */
    public function log($message, $log_level = LogLevel::INFO)
    {
        return file_put_contents(
            $this->getFilepath(),
            $this->getDatetime() . ' ' . LogLevel::getTitle($log_level) . ': ' . $message . PHP_EOL,
            FILE_APPEND
        );
    }

    /**
     * @inheritDoc
     */
    public function error($message)
    {
        return $this->log($message, LogLevel::ERROR);
    }

    /**
     * @inheritDoc
     */
    public function warning($message)
    {
        return $this->log($message, LogLevel::WARNING);
    }

    /**
     * @inheritDoc
     */
    public function info($message)
    {
        return $this->log($message, LogLevel::INFO);
    }

    /**
     * @inheritDoc
     */
    public function debug($message)
    {
        return $this->log($message, LogLevel::DEBUG);
    }

    /**
     * @inheritDoc
     */
    public function logException($exception)
    {
        $class = get_class($exception);
        $class_parts = explode('\\', $class);
        $class_name = end($class_parts);
        $content = $class_name . PHP_EOL
            . "---------- Begin $class_name ----------" . PHP_EOL
            . 'TYPE | ' . $class . PHP_EOL
            . 'FILE | ' . $exception->getFile() . PHP_EOL
            . 'LINE | ' . $exception->getLine() . PHP_EOL
            . 'CODE | ' . $exception->getCode() . PHP_EOL
            . 'MESSAGE | ' . $exception->getMessage() . PHP_EOL
            . 'TRACE | ' . $exception->getTraceAsString() . PHP_EOL
            . "---------- End $class_name ----------";

        return $this->log($content, LogLevel::ERROR);
    }
}
