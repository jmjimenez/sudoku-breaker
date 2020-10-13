<?php declare(strict_types=1);

namespace App\Domain\Sudoku\Entity;

use App\Domain\Sudoku\ValueObject\Event\Event;

abstract class Entity
{
    private static ?EventBus $eventBus;

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
}
