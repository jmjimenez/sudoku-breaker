<?php declare(strict_types=1);

namespace App\Tests\Unit\Domain\Sudoku\ValueObject\Event;

use App\Domain\Sudoku\Exception\InvalidPosition;
use App\Domain\Sudoku\ValueObject\Event\ClearTips;
use App\Domain\Sudoku\ValueObject\Position;
use PHPUnit\Framework\TestCase;

class ClearTipsTest extends TestCase
{
    /**
     * @throws InvalidPosition
     */
    public function testPosition(): void
    {
        $position = new Position(1,1);
        $digit = 1;
        $clearTips = new ClearTips($position, $digit);
        $this->assertEquals($position, $clearTips->position());
    }

    /**
     * @throws InvalidPosition
     */
    public function testDigit(): void
    {
        $position = new Position(1,1);
        $digit = 2;
        $clearTips = new ClearTips($position, $digit);
        $this->assertEquals($digit, $clearTips->digit());
    }

    /**
     * @throws InvalidPosition
     */
    public function testDigitWhenIsNull(): void
    {
        $position = new Position(1,1);
        $clearTips = new ClearTips($position);
        $this->assertNull($clearTips->digit());
    }
}
