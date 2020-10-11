<?php declare(strict_types=1);

namespace App\Domain\Sudoku\Entity;

use App\Domain\Sudoku\ValueObject\CellContent\CellContent;
use App\Domain\Sudoku\ValueObject\CellContent\Tied;
use App\Domain\Sudoku\Exception\CellDoesNotAllowTips;
use App\Domain\Sudoku\Exception\InvalidPosition;
use App\Domain\Sudoku\ValueObject\Event\AddTip;
use App\Domain\Sudoku\ValueObject\Event\ClearTips;
use App\Domain\Sudoku\ValueObject\Event\SetContent;
use App\Domain\Sudoku\ValueObject\Position;

class Cell extends Entity
{
    private Square $square;
    private Row $row;
    private Column $column;
    private CellContent $content;

    /** @var int[] */
    private array $tips = [];

    public function __construct(CellContent $content)
    {
        $this->content = $content;
    }

    /**
     * @return Position
     * @throws InvalidPosition
     */
    public function position(): Position
    {
        return new Position($this->row()->number(), $this->column()->number());
    }

    public function setRow(Row $row): void
    {
        $this->row = $row;
    }

    public function row(): Row
    {
        return $this->row;
    }

    public function setColumn(Column $column): void
    {
        $this->column = $column;
    }

    public function column(): Column
    {
        return $this->column;
    }

    public function setSquare(Square $square): void
    {
        $this->square = $square;
    }

    public function square(): Square
    {
        return $this->square;
    }

    /**
     * @param CellContent $content
     * @throws InvalidPosition
     */
    public function setContent(CellContent $content): void
    {
        $this->content = $content;

        if ($this->value()) {
            $this->tips = [];
        }

        $this->publish(new SetContent($this->position(), $content));
    }

    public function content(): CellContent
    {
        return $this->content;
    }

    public function value(): ?int
    {
        return $this->content->value();
    }

    public function isEmpty(): bool
    {
        if ($this->value() !== null) {
            return false;
        }

        if ($this->content() instanceof Tied) {
            return false;
        }

        return true;
    }

    /**
     * @param int[]
     * @throws CellDoesNotAllowTips
     * @throws InvalidPosition
     */
    public function setTied(int ...$ties)
    {
        $this->checkTipIsAllowed();

        $this->setContent(new Tied(...$ties));
        $this->clearTips();
        foreach ($ties as $tie) {
            $this->addTip($tie);
        }
    }

    public function isTied(): bool
    {
        return $this->content instanceof Tied;
    }

    public function ties(): array
    {
        /** @var Tied $content */
        $content = $this->content;

        if (!$content instanceof Tied) {
            return [];
        }

        return $content->ties();
    }

    /**
     * @param int $digit
     * @throws CellDoesNotAllowTips
     * @throws InvalidPosition
     */
    public function addTip(int $digit): void
    {
        $this->checkTipIsAllowed();

        $this->tips[] = $digit;
        sort($this->tips);

        $this->publish(new AddTip($this->position(), $digit));
    }

    /**
     * @param int|null $digit
     * @throws CellDoesNotAllowTips
     * @throws InvalidPosition
     */
    public function clearTips(?int $digit = null): void
    {
        $this->checkTipIsAllowed();

        //TODO: if a tip is cleared, then check if there is a linked Tied

        $tips = [];

        if ($digit !== null) {
            foreach ($this->tips as $tip) {
                if ($tip !== $digit) {
                    $tips[] = $tip;
                }
            }
        }

        $this->tips = $tips;

        $this->publish(new ClearTips($this->position(), $digit));
    }

    /**
     * @param int $digit
     * @return bool
     * @throws CellDoesNotAllowTips
     */
    public function findTip(int $digit): bool
    {
        $this->checkTipIsAllowed();
        return array_search($digit, $this->tips) !== false;
    }

    /**
     * @return array|int[]
     * @throws CellDoesNotAllowTips
     */
    public function tips(): array
    {
        $this->checkTipIsAllowed();
        return $this->tips;
    }

    public function isTipAllowed(): bool
    {
        return $this->value() === null;
    }

    /**
     * @throws CellDoesNotAllowTips
     */
    private function checkTipIsAllowed(): void
    {
        if (!$this->isTipAllowed()) {
            throw new CellDoesNotAllowTips();
        }
    }
}
