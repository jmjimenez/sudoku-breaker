<?php declare(strict_types=1);

namespace App\Tests\Unit\Domain\Sudoku\Strategy\Strategy;

use App\Domain\Sudoku\Entity\Entity;
use App\Domain\Sudoku\ValueObject\CellContent\Fixed;
use App\Domain\Sudoku\ValueObject\CellContent\Found;
use App\Domain\Sudoku\ValueObject\CellContent\Tied;
use App\Domain\Sudoku\ValueObject\Event\AddTip;
use App\Domain\Sudoku\ValueObject\Event\ClearTips;
use App\Domain\Sudoku\ValueObject\Event\Event;
use App\Domain\Sudoku\ValueObject\Event\SetContent;

trait DebugEvents
{
    /**
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    private function debugEvents(): void
    {
        $subscriptor = function (Event $event) {
            switch (get_class($event)) {
                case ClearTips::class:
                    printf("%s: clear tips %s\n", $event->position(), $event->digit() ?? 'all digits');
                    break;
                case AddTip::class:
                    printf("%s: add tip %s\n", $event->position(), $event->digit());
                    break;
                case SetContent::class:
                    switch (get_class($event->content())) {
                        case Fixed::class:
                            $contentType = 'fixed';
                            break;
                        case Found::class:
                            $contentType = 'found';
                            break;
                        case Tied::class:
                            $contentType = 'tied';
                            break;
                        default:
                            $contentType = 'unknown';
                            break;
                    }
                    printf("%s: add %s %s\n", $event->position(), $contentType, $event->content()->value());
                    break;
                default:
                    printf("unknown operation %s\n", get_class($event));
                    break;
            }
        };

        Entity::subscribe($subscriptor);
    }
}
