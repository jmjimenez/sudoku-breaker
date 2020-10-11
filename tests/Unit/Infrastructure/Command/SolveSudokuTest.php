<?php declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Command;

use App\Infrastructure\Command\SolveSudoku;
use App\Infrastructure\Exception\ErrorDecodingJsonData;
use App\Infrastructure\Exception\ErrorReadingFile;
use App\Infrastructure\Exception\IncorrectFormat;
use App\Infrastructure\Helper\ConsoleLoggerFactory;
use App\Infrastructure\Helper\SudokuFileReader;
use App\Tests\Unit\Domain\Sudoku\Entity\BoardGeneratorMethods;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SolveSudokuTest extends TestCase
{
    use BoardGeneratorMethods;

    private SudokuFileReader $sudokuFileReaderMock;
    private LoggerInterface $loggerMock;
    private SolveSudoku $solveSudoku;

    protected function setUp()
    {
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $loggerFactoryMock = $this->generateMockLoggerFactory();
        $this->sudokuFileReaderMock = $this->createMock(SudokuFileReader::class);
        $this->solveSudoku = new SolveSudoku(
            'Test',
            $loggerFactoryMock,
            $this->sudokuFileReaderMock
        );

        parent::setUp();
    }

    /**
     * @throws Exception
     */
    public function testExecute(): void
    {
        $filename = 'testfilename.json';
        $returnSteps = false;
        $maxIterations = 30;
        $data = $this->generateCompletedBoardExceptSomeEmptyCells([[1,1]]);

        $inputMock = $this->generateInputMock($filename, $maxIterations, $returnSteps);
        $outputMock = $this->createMock(OutputInterface::class);

        /** @noinspection PhpParamsInspection */
        $this->sudokuFileReaderMock
            ->expects($this->once())
            ->method('read')
            ->with($filename)
            ->willReturn($data);

        $this->assertEquals(0, $this->solveSudoku->run($inputMock, $outputMock));
    }

    /**
     * @throws Exception
     */
    public function testExecuteErrorWhenNotPossibleToSolve(): void
    {
        $filename = 'testfilename.json';
        $returnSteps = false;
        $maxIterations = 3;
        $data = [
            1 => [ 1 => 1, 2 => 2],
        ];

        $inputMock = $this->generateInputMock($filename, $maxIterations, $returnSteps);
        $outputMock = $this->createMock(OutputInterface::class);

        /** @noinspection PhpParamsInspection */
        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with('Not possible to solve sudoku');

        /** @noinspection PhpParamsInspection */
        $this->sudokuFileReaderMock
            ->expects($this->once())
            ->method('read')
            ->with($filename)
            ->willReturn($data);

        $this->assertEquals(-1, $this->solveSudoku->run($inputMock, $outputMock));
    }

    /**
     * @throws Exception
     */
    public function testExecuteErrorWhenNotValidSudoku(): void
    {
        $filename = 'testfilename.json';
        $returnSteps = false;
        $maxIterations = 30;
        $data = [
            1 => [ 1 => 1, 2 => 2 ],
            2 => [ 2 => 1 ],
        ];

        $inputMock = $this->generateInputMock($filename, $maxIterations, $returnSteps);
        $outputMock = $this->createMock(OutputInterface::class);

        /** @noinspection PhpParamsInspection */
        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with('Sudoku is not valid');

        /** @noinspection PhpParamsInspection */
        $this->sudokuFileReaderMock
            ->expects($this->once())
            ->method('read')
            ->with($filename)
            ->willReturn($data);

        $this->assertEquals(-2, $this->solveSudoku->run($inputMock, $outputMock));
    }

    /**
     * @throws Exception
     */
    public function testExecuteErrorWhenSudokuAlreadyCompleted(): void
    {
        $filename = 'testfilename.json';
        $maxIterations = 30;
        $returnSteps = false;
        $data = $this->generateCompletedBoard();

        $inputMock = $this->generateInputMock($filename, $maxIterations, $returnSteps);
        $outputMock = $this->createMock(OutputInterface::class);

        /** @noinspection PhpParamsInspection */
        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with('Sudoku already completed');

        /** @noinspection PhpParamsInspection */
        $this->sudokuFileReaderMock
            ->expects($this->once())
            ->method('read')
            ->with($filename)
            ->willReturn($data);

        $this->assertEquals(-3, $this->solveSudoku->run($inputMock, $outputMock));
    }

    /**
     * @throws Exception
     */
    public function testExecuteErrorWhenErrorReadingFile(): void
    {
        $filename = 'testfilename.json';
        $maxIterations = 30;
        $returnSteps = false;

        $inputMock = $this->generateInputMock($filename, $maxIterations, $returnSteps);
        $outputMock = $this->createMock(OutputInterface::class);

        $this->sudokuFileReaderMock
            ->expects($this->once())
            ->method('read')
            ->willThrowException(new ErrorReadingFile($filename));

        /** @noinspection PhpParamsInspection */
        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with(sprintf('Error reading file %s', $filename));

        $this->assertEquals(-4, $this->solveSudoku->run($inputMock, $outputMock));
    }

    /**
     * @throws Exception
     */
    public function testExecuteErrorWhenErrorDecodingJsonData(): void
    {
        $filename = 'testfilename.json';
        $maxIterations = 30;
        $returnSteps = false;

        $inputMock = $this->generateInputMock($filename, $maxIterations, $returnSteps);
        $outputMock = $this->createMock(OutputInterface::class);

        $this->sudokuFileReaderMock
            ->expects($this->once())
            ->method('read')
            ->willThrowException(new ErrorDecodingJsonData());

        /** @noinspection PhpParamsInspection */
        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with('Error decoding json data');

        $this->assertEquals(-5, $this->solveSudoku->run($inputMock, $outputMock));
    }

    /**
     * @throws Exception
     */
    public function testExecuteErrorWhenErrorIncorrectFormat(): void
    {
        $filename = 'testfilename.json';
        $maxIterations = 30;
        $returnSteps = false;

        $inputMock = $this->generateInputMock($filename, $maxIterations, $returnSteps);
        $outputMock = $this->createMock(OutputInterface::class);

        $this->sudokuFileReaderMock
            ->expects($this->once())
            ->method('read')
            ->willThrowException(new IncorrectFormat('format error'));

        /** @noinspection PhpParamsInspection */
        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with('Incorrect format: format error');

        $this->assertEquals(-6, $this->solveSudoku->run($inputMock, $outputMock));
    }

    private function generateMockLoggerFactory(): ConsoleLoggerFactory
    {
        return new class($this) extends ConsoleLoggerFactory {
            private SolveSudokuTest $parent;

            public function __construct(SolveSudokuTest $parent)
            {
                $this->parent = $parent;
            }

            public function getLogger(OutputInterface $output): LoggerInterface
            {
                return $this->parent->generateLoggerMock();
            }
        };
    }

    public function generateLoggerMock(): LoggerInterface
    {
        return $this->loggerMock;
    }

    private function generateInputMock(string $filename, int $maxIterations, bool $returnSteps): InputInterface
    {
        $inputMock = $this->createMock(InputInterface::class);
        $inputMock->expects($this->once())
            ->method('getArgument')
            ->willReturn($filename);

        $inputMock->expects($this->any())
            ->method('getOption')
            ->will($this->returnCallback(function(string $option) use ($maxIterations, $returnSteps) {
                switch ($option) {
                    case 'return_steps':
                        return $returnSteps;
                        break;
                    case 'max_iterations':
                        return $maxIterations;
                        break;
                    default:
                        throw new Exception(sprintf('Invalid option %s', $option));
                }
            }));

        return $inputMock;
    }
}
