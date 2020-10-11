<?php /** @noinspection PhpMissingFieldTypeInspection */
declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Helper;

use App\Infrastructure\Exception\ErrorDecodingJsonData;
use App\Infrastructure\Exception\ErrorReadingFile;
use App\Infrastructure\Exception\IncorrectFormat;
use App\Infrastructure\Helper\SudokuFileReader;
use Exception;
use PHPUnit\Framework\TestCase;

class SudokuFileReaderTest extends TestCase
{
    private $dataMock;
    private $sudokuFileReader;

    protected function setUp()
    {
        $this->sudokuFileReader = new class($this) extends SudokuFileReader {
            private SudokuFileReaderTest $test;

            public function __construct(SudokuFileReaderTest $test)
            {
                $this->test = $test;
            }

            protected function readFile(string $fileName)
            {
                return $this->test->dataMock();
            }
        };
    }

    /**
     * @throws ErrorDecodingJsonData
     * @throws ErrorReadingFile
     * @throws IncorrectFormat
     */
    public function testExecute(): void
    {
        $data = "{ \"board\": { \"fixedCells\": [ { \"row\": 1, \"column\": 1, \"value\": 1 }]}}";
        $result = [
            '1' => [ '1' => 1 ]
        ];
        $filename = 'test.json';
        $this->setDataMock($data);
        $this->assertEquals($result, $this->sudokuFileReader->read($filename));
    }

    /**
     * @throws Exception
     */
    public function testExecuteErrorReadingFile(): void
    {
        $filename = 'test.json';
        $this->setDataMock(false);
        $this->expectException(ErrorReadingFile::class);
        $this->sudokuFileReader->read($filename);
    }

    /**
     * @throws Exception
     */
    public function testExecuteErrorDecodingJson(): void
    {
        $filename = 'test.json';
        $this->setDataMock('I am not json');
        $this->expectException(ErrorDecodingJsonData::class);
        $this->sudokuFileReader->read($filename);
    }

    /**
     * @param string $data
     * @throws ErrorDecodingJsonData
     * @throws ErrorReadingFile
     * @throws IncorrectFormat
     *
     * @dataProvider executeErrorParsingDataDataProvider
     */
    public function testExecuteErrorParsingData(string $data): void
    {
        $filename = 'test.json';
        $this->setDataMock($data);
        $this->expectException(IncorrectFormat::class);
        $this->sudokuFileReader->read($filename);
    }

    public function executeErrorParsingDataDataProvider(): array
    {
        return [
            [ 'wrong_board_key' => "{ \"noboard\": { \"fixedCells\": [ { \"row\": 1, \"column\": 1, \"value\": 1 }]}}" ],
            [ 'wrong_fixedCells_key' => "{ \"board\": { \"nofixedCells\": [ { \"row\": 1, \"column\": 1, \"value\": 1 }]}}" ],
            [ 'wrong_row_key' => "{ \"board\": { \"fixedCells\": [ { \"norow\": 1, \"column\": 1, \"value\": 1 }]}}" ],
            [ 'wrong_column_key' => "{ \"board\": { \"fixedCells\": [ { \"row\": 1, \"nocolumn\": 1, \"value\": 1 }]}}" ],
            [ 'wrong_value_key' => "{ \"board\": { \"fixedCells\": [ { \"row\": 1, \"column\": 1, \"novalue\": 1 }]}}" ],
        ];
    }

    /**
     * @return bool|string
     */
    public function dataMock()
    {
        return $this->dataMock;
    }

    private function setDataMock($dataMock): void
    {
        $this->dataMock = $dataMock;
    }
}
