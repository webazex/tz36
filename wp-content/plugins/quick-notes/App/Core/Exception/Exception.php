<?php
namespace WBZXTDL\App\Core\Exception;

use WBZXTDL\App\Core\Logger\Logger as Logger;

class Exception extends \Exception
{
    private const ERROR_PREFIX = "To do list plugin error: ";

    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(__(self::ERROR_PREFIX, 'wbzx-tdl') . $message, $code, $previous);
    }

    public function getFormattedMessage(): string
    {
        return sprintf("[%s] %s in %s on line %d",
            date('Y-m-d H:i:s'),
            $this->getMessage(),
            $this->getFile(),
            $this->getLine()
        );
    }

    public function log(Logger $logger): void
    {
        $logger->error($this->getFormattedMessage());
    }
}
