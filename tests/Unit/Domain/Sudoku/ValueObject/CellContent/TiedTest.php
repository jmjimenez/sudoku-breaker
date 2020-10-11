<?php declare(strict_types=1);

namespace App\Tests\Unit\Domain\Sudoku\ValueObject\CellContent;

use App\Domain\Sudoku\ValueObject\CellContent\Tied;
use PHPUnit\Framework\TestCase;

class TiedTest extends TestCase
{
    public function testTies(): void
    {
        $ties = [ 1, 2, 3, 4, 5];

        $tied = new Tied(...$ties);
        $this->assertEquals($ties, $tied->ties());
    }
}
