<?php declare(strict_types=1);

namespace App\Domain\Sudoku\Entity;

use App\Domain\Sudoku\ValueObject\Event\Event;
use Psr\Log\LoggerInterface;

abstract class Entity
{
    private static ?EventBus $eventBus;
    private static ?LoggerInterface $loggerObject;
    private static ?LoggerInterface $loggerProxy;

    public static function eventBus(): EventBus
    {
        if (!isset(self::$eventBus)) {
            self::$eventBus = new EventBus();
        }

        return self::$eventBus;
    }

    protected function publish(Event $event): void
    {
        self::eventBus()->publish($event);
    }

    public static function subscribe(callable $subscriptor): void
    {
        self::eventBus()->subscribe($subscriptor);
    }

    public static function setLogger(LoggerInterface $logger): void
    {
        self::$loggerObject = $logger;
    }

    protected function logger(): LoggerInterface
    {
        if (!isset(self::$loggerProxy)) {
            self::$loggerProxy = $this->generateLoggerProxy();
        }

        return self::$loggerProxy;
    }

    private function generateLoggerProxy(): LoggerInterface
    {
        return new class(self::$loggerObject ?? null) implements LoggerInterface {
            private ?LoggerInterface $logger;

            public function __construct(?LoggerInterface $logger = null)
            {
                if ($logger !== null) {
                    $this->logger = $logger;
                }
            }

            public function emergency($message, array $context = array())
            {
                if (isset($this->logger)) {
                    $this->logger->emergency($message, $context);
                }
            }

            public function alert($message, array $context = array())
            {
                if (isset($this->logger)) {
                    $this->logger->alert($message, $context);
                }
            }

            public function critical($message, array $context = array())
            {
                if (isset($this->logger)) {
                    $this->logger->critical($message, $context);
                }
            }

            public function error($message, array $context = array())
            {
                if (isset($this->logger)) {
                    $this->logger->error($message, $context);
                }
            }

            public function warning($message, array $context = array())
            {
                if (isset($this->logger)) {
                    $this->logger->warning($message, $context);
                }
            }

            public function notice($message, array $context = array())
            {
                if (isset($this->logger)) {
                    $this->logger->notice($message, $context);
                }
            }

            public function info($message, array $context = array())
            {
                if (isset($this->logger)) {
                    $this->logger->info($message, $context);
                }
            }

            public function debug($message, array $context = array())
            {
                if (isset($this->logger)) {
                    $this->logger->debug($message, $context);
                }
            }

            public function log($level, $message, array $context = array())
            {
                if (isset($this->logger)) {
                    $this->logger->log($level, $message, $context);
                }
            }
        };
    }
}
