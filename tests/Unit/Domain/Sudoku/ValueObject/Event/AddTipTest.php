<?php declare(strict_types=1);

namespace App\Tests\Unit\Domain\Sudoku\ValueObject\Event;

use App\Domain\Sudoku\Exception\InvalidPosition;
use App\Domain\Sudoku\ValueObject\Event\AddTip;
use App\Domain\Sudoku\ValueObject\Position;
use PHPUnit\Framework\TestCase;

class AddTipTest extends TestCase
{
    /**
     * @throws InvalidPosition
     */
    public function testPosition(): void
    {
        $position = new Position(1,1);
        $digit = 1;
        $addTip = new AddTip($position, $digit);
        $this->assertEquals($position, $addTip->position());
    }

    /**
     * @throws InvalidPosition
     */
    public function testDigit(): void
    {
        $position = new Position(1,1);
        $digit = 2;
        $addTip = new AddTip($position, $digit);
        $this->assertEquals($digit, $addTip->digit());
    }
}
