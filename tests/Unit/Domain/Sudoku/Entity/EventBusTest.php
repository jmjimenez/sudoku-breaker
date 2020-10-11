<?php declare(strict_types=1);

namespace App\Tests\Unit\Domain\Sudoku\Entity;

use App\Domain\Sudoku\Entity\EventBus;
use App\Domain\Sudoku\ValueObject\Event\Event;
use PHPUnit\Framework\TestCase;

class EventBusTest extends TestCase
{
    public function testPublish(): void
    {
        $eventBus = new EventBus();
        $testEvent = new Event();
        $countEvents = 0;
        $eventBus->subscribe(
            function(Event $event) use ($testEvent, &$countEvents) {
                $countEvents++;
                $this->assertEquals($testEvent, $event);
            }
        );
        $eventBus->publish($testEvent);
        $this->assertEquals(1, $countEvents);
    }
}
