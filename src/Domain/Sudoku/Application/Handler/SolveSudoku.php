<?php declare(strict_types=1);

namespace App\Domain\Sudoku\Application\Handler;

use App\Domain\Sudoku\Application\SolveSudoku as GamePayload;
use App\Domain\Sudoku\Application\Handler\Exception\NotPossibleToSolve;
use App\Domain\Sudoku\Application\Handler\Exception\NotValidSudoku;
use App\Domain\Sudoku\Application\Handler\Exception\ReachedInvalidStatus;
use App\Domain\Sudoku\Application\Handler\Exception\SudokuAlreadyCompleted;
use App\Domain\Sudoku\Application\SolveSudoku as SolveSudokuPayload;
use App\Domain\Sudoku\Entity\Board;
use App\Domain\Sudoku\Entity\Entity;
use App\Domain\Sudoku\Exception\InvalidColumnValue;
use App\Domain\Sudoku\Exception\InvalidPosition;
use App\Domain\Sudoku\Exception\InvalidRowValue;
use App\Domain\Sudoku\Exception\InvalidValue;
use App\Domain\Sudoku\Strategy\StrategiesLoader;
use App\Domain\Sudoku\ValueObject\CellContent\Fixed;
use App\Domain\Sudoku\ValueObject\CellContent\Found;
use App\Domain\Sudoku\ValueObject\CellContent\Tied;
use App\Domain\Sudoku\ValueObject\Event\AddTip;
use App\Domain\Sudoku\ValueObject\Event\ClearTips;
use App\Domain\Sudoku\ValueObject\Event\Event;
use App\Domain\Sudoku\ValueObject\Event\SetContent;
use App\Domain\Sudoku\ValueObject\Result;
use Psr\Log\LoggerInterface;

class SolveSudoku
{
    public const MAX_ITERATIONS = 30;

    private Board $board;
    private StrategiesLoader $strategiesLoader;
    private array $events = [];
    private bool $returnSteps;
    private ?LoggerInterface $logger;

    public function __construct(
        StrategiesLoader $strategiesLoader,
        ?LoggerInterface $logger = null
    ) {
        if ($logger !== null) {
           $this->logger = $logger;
        }
        $this->strategiesLoader = $strategiesLoader;
        $this->board = new Board();
    }

    /**
     * @param SolveSudokuPayload $game
     * @return Result
     * @throws InvalidColumnValue
     * @throws InvalidRowValue
     * @throws InvalidValue
     * @throws NotPossibleToSolve
     * @throws NotValidSudoku
     * @throws ReachedInvalidStatus
     * @throws SudokuAlreadyCompleted
     * @throws InvalidPosition
     */
    public function execute(GamePayload $game): Result
    {
        $this->board->load($game->data());

        $status = $this->board->status();
        if (!$status->isValid()) {
            throw new NotValidSudoku();
        }
        if ($status->isCompleted()) {
            throw new SudokuAlreadyCompleted();
        }

        $strategies = $this->strategiesLoader->load();

        $this->returnSteps = $game->returnSteps();
        Entity::subscribe(function(Event $event) { $this->recordEvent($event); } );

        $iterations = 1;
        while ($iterations <= $game->maxIterations()) {
            $this->logger()->debug(sprintf('Iteration #%s', $iterations));

            foreach ($strategies as $strategy) {
                if ($strategy->execute($this->board)) {
                    $this->logger()->debug(sprintf('Strategy %s succeded', get_class($strategy)));
                    $status = $this->board->status();
                    if (!$status->isValid()) {
                        throw new ReachedInvalidStatus();
                    }
                    if ($status->isCompleted()) {
                        return new Result($this->board, $this->events);
                    }
                    continue;
                }
            }

            $iterations++;
        }

        throw new NotPossibleToSolve();
    }

    /**
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    private function recordEvent(Event $event): void
    {
        switch (get_class($event)) {
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
                $message = sprintf("%s: add %s %s", $event->position(), $contentType, $event->content()->value());
                $this->logger()->debug($message);
                if ($this->returnSteps) {
                    $this->events[] = $message;
                }
                break;
            case ClearTips::class:
            case AddTip::class:
            default:
                break;
        }
    }

    protected function logger(): LoggerInterface
    {
        if (isset($this->logger)) {
            return $this->logger;
        }

        $this->logger = new class implements LoggerInterface {
            public function emergency($message, array $context = array()) { }

            public function alert($message, array $context = array()) { }

            public function critical($message, array $context = array()) { }

            public function error($message, array $context = array()) { }

            public function warning($message, array $context = array()) { }

            public function notice($message, array $context = array()) { }

            public function info($message, array $context = array()) { }

            public function debug($message, array $context = array()) { }

            public function log($level, $message, array $context = array()) { }
        };

        return $this->logger;
    }
}
