<?php declare(strict_types=1);

namespace App\Infrastructure\Helper;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleLoggerFactory
{
    public function getLogger(OutputInterface $output): LoggerInterface
    {
        return new ConsoleLogger($output);
    }
}
