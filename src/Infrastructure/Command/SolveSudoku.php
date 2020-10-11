<?php declare(strict_types=1);

namespace App\Infrastructure\Command;

use App\Domain\Sudoku\Application\Handler\Exception\NotPossibleToSolve;
use App\Domain\Sudoku\Application\Handler\Exception\NotValidSudoku;
use App\Domain\Sudoku\Application\Handler\Exception\SudokuAlreadyCompleted;
use App\Domain\Sudoku\Application\Handler\SolveSudoku as SolveSudokuHandler;
use App\Domain\Sudoku\Application\SolveSudoku as SolveSudokuPayload;
use App\Domain\Sudoku\Entity\Entity;
use App\Infrastructure\Domain\Sudoku\Strategy\StrategiesLoaderHardcoded;
use App\Infrastructure\Exception\ErrorDecodingJsonData;
use App\Infrastructure\Exception\ErrorReadingFile;
use App\Infrastructure\Exception\IncorrectFormat;
use App\Infrastructure\Helper\ConsoleLoggerFactory;
use App\Infrastructure\Helper\SudokuFileReader;
use App\Infrastructure\Helper\SudokuResultFormatter;
use App\Infrastructure\Helper\SudokuResultPrinter;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SolveSudoku extends Command
{
    protected ConsoleLoggerFactory $loggerFactory;
    protected SudokuFileReader $sudokuFileReader;
    protected LoggerInterface $logger;
    private SudokuResultPrinter $resultPrinter;
    private SudokuResultFormatter $resultFormatter;

    public function __construct(
        string $name = null,
        ConsoleLoggerFactory $loggerFactory = null,
        SudokuFileReader $sudokuFileReader = null,
        SudokuResultFormatter $resultFormatter = null,
        SudokuResultPrinter $resultPrinter = null
    ) {
        $this->loggerFactory = $loggerFactory ?? new ConsoleLoggerFactory();
        $this->sudokuFileReader = $sudokuFileReader ?? new SudokuFileReader();
        $this->resultFormatter = $resultFormatter ?? new SudokuResultFormatter();
        $this->resultPrinter = $resultPrinter ?? new SudokuResultPrinter();
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('sudoku:solve')
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'file with the sudoku'
            )
            ->addOption(
                'return_steps',
                's',
                InputOption::VALUE_NONE,
                'Include steps in the output'
            )
            ->addOption(
                'max_iterations',
                'i',
                InputOption::VALUE_OPTIONAL,
                'Max iterations before giving up',
                SolveSudokuHandler::MAX_ITERATIONS
            )
            ->setDescription('Solves the sudoku passed as a file as argument');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->logger = $this->loggerFactory->getLogger($output);
            Entity::setLogger($this->logger);

            $this->logger->info('Sudoku started');

            $data = $this->sudokuFileReader->read($input->getArgument('file'));
            $returnSteps = (boolean) $input->getOption('return_steps');
            $maxIterations = (int) $input->getOption('max_iterations');

            $payload = new SolveSudokuPayload($data, $maxIterations, $returnSteps);
            $handler = new SolveSudokuHandler(new StrategiesLoaderHardcoded());
            $result = $handler->execute($payload);
            $this->resultPrinter->print($output, $this->resultFormatter->format($result, $returnSteps) );
        } catch (NotPossibleToSolve $exception) {
            $this->logger->error('Not possible to solve sudoku');
            return -1;
        } catch (NotValidSudoku $exception) {
            $this->logger->error('Sudoku is not valid');
            return -2;
        } catch (SudokuAlreadyCompleted $exception) {
            $this->logger->error('Sudoku already completed');
            return -3;
        } catch (ErrorReadingFile $exception) {
            $this->logger->error(sprintf('Error reading file %s', $exception->getMessage()));
            return -4;
        } catch (ErrorDecodingJsonData $exception) {
            $this->logger->error('Error decoding json data');
            return -5;
        } catch (IncorrectFormat $exception) {
            $this->logger->error(sprintf('Incorrect format: %s', $exception->getMessage()));
            return -6;
        } catch (Exception $exception) {
            $this->logger->error('Unexpected error');
            $this->logger->debug(
                sprintf(
                    'class: %s - message: %s - file: %s - line: %s',
                    get_class($exception),
                    $exception->getMessage(),
                    $exception->getFile(),
                    $exception->getLine()
                )
            );
            $this->logger->debug($exception->getMessage());
            $this->logger->debug($exception->getTraceAsString());
            return -7;
        }

        return 0;
    }
}
