<?php

namespace WBZXTDL\App\Core\Logger;


class Logger
{
    private string $logDir;

    public function __construct()
    {
        $date = date('Y-m-d');
        $this->logDir = rtrim(WBZX_TDL_PATH, '/') . DS . 'logs' . DS . $date;
        if (!is_dir($this->logDir)) {
            mkdir($this->logDir, 0755, true);
        }
    }

    private function getLogFilePath(string $level): string
    {
        return "{$this->logDir}/{$level}.log";
    }

    public function log(string $level, string $message): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] {$level}: {$message}" . PHP_EOL;

        $logFile = $this->getLogFilePath(strtolower($level));

        try {

            file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
        } catch (\Throwable $e) {
            error_log(__('Logger failed to write to file: ', 'wbzx-tdl') . $e->getMessage());
        }
    }

    public function info(string $message): void
    {
        $this->log('INFO', $message);
    }

    public function warning(string $message): void
    {
        $this->log('WARNING', $message);
    }

    public function error(string $message): void
    {
        $this->log('ERROR', $message);
    }
}
