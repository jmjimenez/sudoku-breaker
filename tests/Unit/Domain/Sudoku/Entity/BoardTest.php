<?php declare(strict_types=1);

namespace App\Tests\Unit\Domain\Sudoku\Entity;

use App\Domain\Sudoku\Entity\Board;
use App\Domain\Sudoku\ValueObject\CellContent\Fixed;
use App\Domain\Sudoku\ValueObject\CellContent\Nothing;
use App\Domain\Sudoku\Exception\InvalidColumnValue;
use App\Domain\Sudoku\Exception\InvalidPosition;
use App\Domain\Sudoku\Exception\InvalidRowValue;
use App\Domain\Sudoku\Exception\InvalidValue;
use PHPUnit\Framework\TestCase;

class BoardTest extends TestCase
{
    use BoardGeneratorMethods;

    private Board $board;

    protected function setUp(): void
    {
        $this->board = new Board();
    }

    /**
     * @throws InvalidColumnValue
     * @throws InvalidPosition
     * @throws InvalidRowValue
     * @throws InvalidValue
     */
    public function testLoad()
    {
        $data = [
            1 => [
                1 => 1,
                2 => 2,
                3 => 3
            ],
        ];

        $this->board->load($data);

        for ($i = 1; $i <= 9; $i++) {
            for ($j = 1; $j <= 9; $j++) {
                if (isset($data[$i][$j])) {
                    $this->assertEquals(new Fixed($data[$i][$j]), $this->board->content($i, $j));
                } else {
                    $this->assertEquals(new Nothing(), $this->board->content($i, $j));
                }
            }
        }
    }

    /**
     * @throws InvalidColumnValue
     * @throws InvalidPosition
     * @throws InvalidRowValue
     * @throws InvalidValue
     */
    public function testLoadWhenRowIsIncorrect()
    {
        $data = [
            10 => [
                1 => 1,
                2 => 2,
                3 => 3
            ],
        ];

        $this->expectExceptionObject(new InvalidRowValue(10));
        $this->board->load($data);
    }

    /**
     * @throws InvalidColumnValue
     * @throws InvalidPosition
     * @throws InvalidRowValue
     * @throws InvalidValue
     */
    public function testLoadWhenColumnIsIncorrect()
    {
        $data = [
            1 => [
                1 => 1,
                2 => 2,
                30 => 3
            ],
        ];

        $this->expectExceptionObject(new InvalidColumnValue(30));
        $this->board->load($data);
    }

    /**
     * @throws InvalidColumnValue
     * @throws InvalidPosition
     * @throws InvalidRowValue
     * @throws InvalidValue
     */
    public function testLoadWhenValueIsIncorrect()
    {
        $data = [
            1 => [
                1 => 1,
                2 => 2,
                3 => 0
            ],
        ];

        $this->expectExceptionObject(new InvalidValue(0));
        $this->board->load($data);
    }

    /**
     * @throws InvalidColumnValue
     * @throws InvalidRowValue
     * @throws InvalidValue
     * @throws InvalidPosition
     */
    public function testRows(): void
    {
        $testRowNumber = 1;
        $data = [1,2,3,4,5,6,7,8,9];

        for ($j = 0; $j < 9; $j++) {
            $this->board->setFixed($testRowNumber, $j+1, $data[$j]);
        }

        foreach ($this->board->rows() as $rowNumber => $row) {
            if ($rowNumber == $testRowNumber) {
                for ($i = 0; $i < 9; $i++) {
                    $this->assertEquals(new Fixed($data[$i]), $row->content($i+1));
                }
            } else {
                for ($i = 1; $i <= 9; $i++) {
                    $this->assertEquals(new Nothing(), $row->content($i));
                }
            }
        }
    }

    /**
     * @throws InvalidColumnValue
     * @throws InvalidRowValue
     * @throws InvalidValue
     * @throws InvalidPosition
     */
    public function testColumns(): void
    {
        $testColumnNumber = 1;
        $data = [1,2,3,4,5,6,7,8,9];

        for ($j = 0; $j < 9; $j++) {
            $this->board->setFixed($j+1, $testColumnNumber, $data[$j]);
        }

        foreach ($this->board->columns() as $columnNumber => $column) {
            if ($columnNumber == $testColumnNumber) {
                for ($i = 0; $i < 9; $i++) {
                    $this->assertEquals(new Fixed($data[$i]), $column->content($i+1));
                }
            } else {
                for ($i = 1; $i <= 9; $i++) {
                    $this->assertEquals(new Nothing(), $column->content($i));
                }
            }
        }
    }

    /**
     * @throws InvalidColumnValue
     * @throws InvalidRowValue
     * @throws InvalidValue
     * @throws InvalidPosition
     */
    public function testSquares(): void
    {
        for ($i = 4, $value = 1; $i <= 6; $i++) {
            for ($j = 4; $j <= 6; $j++) {
                $this->board->setFixed($i, $j, $value++);
            }
        }

        foreach ($this->board->squares() as $index => $square) {
            if ($index == 5) {
                for ($i = 1, $value=1; $i <= 3; $i++) {
                    for ($j = 1; $j <= 3; $j++) {
                        $this->assertEquals(new Fixed($value), $square->content($i, $j));
                        $value++;
                    }
                }
            } else {
                for ($i = 1; $i <= 3; $i++) {
                    for ($j = 1; $j <= 3; $j++) {
                        $this->assertEquals(new Nothing(), $square->content($i, $j));
                    }
                }
            }
        }
    }

    /**
     * @param int $row
     * @param int $column
     * @param int $value
     * @throws InvalidColumnValue
     * @throws InvalidRowValue
     * @throws InvalidValue
     * @throws InvalidPosition
     *
     * @dataProvider contentDataProvider
     */
    public function testContent(int $row, int $column, int $value): void
    {
        $this->board->setFixed($row, $column, $value);
        for ($i = 1; $i <= 9; $i++) {
            for ($j = 1; $j <= 9; $j++) {
                $this->assertEquals(
                    $i == $row && $j == $column ? new Fixed($value) : new Nothing(),
                    $this->board->content($i, $j)
                );
            }
        }
    }

    public function contentDataProvider(): array
    {
        return [
            [1,1,1],
            [1,2,3],
        ];
    }

    /**
     * @throws InvalidColumnValue
     * @throws InvalidRowValue
     * @throws InvalidValue
     * @throws InvalidPosition
     */
    public function testSetFoundWhenRowIsIncorrect(): void
    {
        $this->expectExceptionObject(new InvalidRowValue(10));
        $this->board->setFound(10,1, 9);
    }

    /**
     * @throws InvalidColumnValue
     * @throws InvalidRowValue
     * @throws InvalidValue
     * @throws InvalidPosition
     */
    public function testSetFoundWhenColumnIsIncorrect(): void
    {
        $this->expectExceptionObject(new InvalidColumnValue(10));
        $this->board->setFound(1,10, 9);
    }

    /**
     * @throws InvalidColumnValue
     * @throws InvalidRowValue
     * @throws InvalidValue
     * @throws InvalidPosition
     */
    public function testSetFoundWhenValueIsIncorrect(): void
    {
        $this->expectExceptionObject(new InvalidValue(10));
        $this->board->setFound(1,1, 10);
    }

    /**
     * @param array $data
     * @param int $digit
     * @param int $count
     *
     * @throws InvalidColumnValue
     * @throws InvalidPosition
     * @throws InvalidRowValue
     * @throws InvalidValue
     * @dataProvider countDigitDataProvider
     */
    public function testCountDigit(array $data, int $digit, int $count): void
    {
        $this->board->load($data);
        $this->assertEquals($count, $this->board->countDigit($digit));
    }

    public function countDigitDataProvider(): array
    {
        return [
            [
                [
                    1 => [
                        1 => 1,
                        2 => 2,
                        3 => 3,
                    ]
                ],
                1,
                1,
            ],
            [
                [
                    1 => [
                        1 => 1,
                        2 => 2,
                        3 => 3,
                    ],
                    2 => [
                        4 => 1,
                        5 => 2,
                        6 => 3,
                    ],
                ],
                1,
                2,
            ],
            [
                $this->generateCompletedBoard(),
                1,
                9,
            ],
        ];

    }

    /**
     * @param array $data
     * @param bool $isValid
     * @param bool $isCompleted
     * @throws InvalidColumnValue
     * @throws InvalidPosition
     * @throws InvalidRowValue
     * @throws InvalidValue
     * @dataProvider statusDataProvider
     */
    public function testStatus(array $data, bool $isValid, bool $isCompleted): void
    {
        $board = new Board();
        $board->load($data);
        $status = $board->status();
        $this->assertEquals($status->isValid(), $isValid);
        $this->assertEquals($status->isCompleted(), $isCompleted);
    }

    public function statusDataProvider(): array
    {
        return [
            [
                [
                    1 => [
                        1 => 1,
                        2 => 2,
                        3 => 3,
                    ]
                ],
                true,
                false,
            ],
            [
                [
                    1 => [
                        1 => 1,
                        2 => 1,
                        3 => 3,
                    ]
                ],
                false,
                false,
            ],
            [
                $this->generateCompletedBoard(),
                true,
                true,
            ],
        ];
    }

    /**
     * @throws InvalidColumnValue
     * @throws InvalidPosition
     * @throws InvalidRowValue
     * @throws InvalidValue
     */
    public function testStatusWhenRowIsNotValid(): void
    {
        $data = [
            1 => [
                1 => 1,
                2 => 1,
                3 => 3,
            ]
        ];

        $this->board->load($data);
        $status = $this->board->status();
        $this->assertFalse($status->isValid());
        $this->assertFalse($status->isCompleted());
    }

    /**
     * @throws InvalidColumnValue
     * @throws InvalidPosition
     * @throws InvalidRowValue
     * @throws InvalidValue
     */
    public function testStatusWhenColumnIsNotValid(): void
    {
        $data = [
            1 => [
                1 => 1,
                2 => 2,
                3 => 3,
            ],
            2 => [
                1 => 4,
                2 => 5,
                3 => 6,
            ],
            3 => [
                1 => 1,
                2 => 8,
                3 => 9,
            ],
        ];

        $this->board->load($data);
        $status = $this->board->status();
        $this->assertFalse($status->isValid());
        $this->assertFalse($status->isCompleted());
    }

    /**
     * @throws InvalidColumnValue
     * @throws InvalidPosition
     * @throws InvalidRowValue
     * @throws InvalidValue
     */
    public function testStatusWhenSquareIsNotValid(): void
    {
        $data = [
            1 => [
                1 => 1,
                2 => 2,
                3 => 3,
            ],
            2 => [
                1 => 4,
                2 => 5,
                3 => 6,
            ],
            3 => [
                1 => 7,
                2 => 8,
                3 => 1,
            ],
        ];

        $this->board->load($data);
        $status = $this->board->status();
        $this->assertFalse($status->isValid());
        $this->assertFalse($status->isCompleted());
    }
}
