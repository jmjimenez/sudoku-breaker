<?php declare(strict_types=1);

namespace App\Domain\Sudoku\Entity;

use App\Domain\Sudoku\ValueObject\Event\Event;

class EventBus
{
    /** @var callable[]  */
    private array $subscriptors = array();

    public function publish(Event $event): void
    {
        foreach ($this->subscriptors as $subscriptor) {
            $subscriptor($event);
        }
    }

    public function subscribe(callable $subscritor): void
    {
        $this->subscriptors[] = $subscritor;
    }
}
