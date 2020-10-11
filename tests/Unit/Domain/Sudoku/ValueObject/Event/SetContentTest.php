<?php declare(strict_types=1);

namespace App\Tests\Unit\Domain\Sudoku\ValueObject\Event;

use App\Domain\Sudoku\Exception\InvalidPosition;
use App\Domain\Sudoku\ValueObject\CellContent\Fixed;
use App\Domain\Sudoku\ValueObject\Event\SetContent;
use App\Domain\Sudoku\ValueObject\Position;
use PHPUnit\Framework\TestCase;

class SetContentTest extends TestCase
{
    /**
     * @throws InvalidPosition
     */
    public function testPosition(): void
    {
        $position = new Position(1,1);
        $content = new Fixed(1);
        $setContent = new SetContent($position, $content);
        $this->assertEquals($position, $setContent->position());
    }

    /**
     * @throws InvalidPosition
     */
    public function testContent(): void
    {
        $position = new Position(1,1);
        $content = new Fixed(1);
        $setContent = new SetContent($position, $content);
        $this->assertEquals($content, $setContent->content());
    }
}
