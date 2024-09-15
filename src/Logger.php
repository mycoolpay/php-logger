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
     * @var int $permissions
     */
    protected $permissions;
    /**
     * @var string $datetime_format
     */
    protected $datetime_format;
    /**
     * @var string $timezone
     */
    protected $timezone;

    /**
     * @param string $filename
     * @param string|null $dir
     * @param int $permissions
     * @param string|null $timezone
     * @param string $datetime_format
     */
    public function __construct($filename = 'app.log', $dir = null, $permissions = 0777, $timezone = null, $datetime_format = '[Y-m-d H:i:s]')
    {
        $this->setDir($dir);
        $this->permissions = $permissions;
        $this->filename = $filename;
        $this->datetime_format = $datetime_format;
        $this->timezone = $timezone;
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
     * @param string $dir
     * @return $this
     */
    public function setDir($dir)
    {
        if (is_null($dir))
            $dir = sys_get_temp_dir();
        if (preg_match('#[/\\\]$#', $dir))
            $dir = preg_replace('#[/\\\]$#', '', $dir); // Remove trailing slash
        if (!file_exists($dir))
            mkdir($dir, $this->permissions, true);

        $this->dir = $dir;
        return $this;
    }

    /**
     * @return int
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @param int $permissions
     * @return $this
     */
    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;
        return $this;
    }

    /**
     * @return string
     */
    public function getDatetimeFormat()
    {
        return $this->datetime_format;
    }

    /**
     * @param string $datetime_format
     * @return $this
     */
    public function setDatetimeFormat($datetime_format)
    {
        $this->datetime_format = $datetime_format;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @param string|null $timezone
     * @return $this
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
        return $this;
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
            $now = is_null($this->timezone)
                ? new DateTime('now')
                : new DateTime('now', new DateTimeZone($this->timezone));

            return $now->format($this->datetime_format);

        } catch (Exception $exception) {
            return date($this->datetime_format);
        }
    }

    /**
     * @inheritDoc
     */
    public function log($message, $log_level = LogLevel::INFO)
    {
        $log = '';
        $datetime = $this->getDatetime();
        $log_level = LogLevel::getTitle($log_level);
        $parts = preg_split('/\r?\n/', $message);

        foreach ($parts as $part) {
            $log .= "$datetime $log_level: $part" . PHP_EOL;
        }

        return file_put_contents($this->getFilepath(), $log, FILE_APPEND);
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
