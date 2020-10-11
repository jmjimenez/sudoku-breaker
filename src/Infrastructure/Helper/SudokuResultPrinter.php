<?php declare(strict_types=1);

namespace App\Infrastructure\Helper;

use Symfony\Component\Console\Output\OutputInterface;

class SudokuResultPrinter
{
    public function print(OutputInterface $output, string $result): void
    {
        $output->writeln($result);
    }
}
