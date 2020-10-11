<?php declare(strict_types=1);

namespace App\Infrastructure\Helper;

use App\Infrastructure\Exception\ErrorDecodingJsonData;
use App\Infrastructure\Exception\ErrorReadingFile;
use App\Infrastructure\Exception\IncorrectFormat;

class SudokuFileReader
{
    /**
     * @param string $fileName
     * @return array
     * @throws ErrorDecodingJsonData
     * @throws ErrorReadingFile
     * @throws IncorrectFormat
     */
    public function read(string $fileName): array
    {
        $string = $this->readFile($fileName);
        if ($string === false) {
            throw new ErrorReadingFile($fileName);
        }

        $jsonData = json_decode($string, true);
        if ($jsonData === null) {
            throw new ErrorDecodingJsonData();
        }

        return $this->parseJson($jsonData);
    }

    /**
     * @param string $fileName
     * @return false|string
     */
    protected function readFile(string $fileName)
    {
        return file_get_contents($fileName);
    }

    /**
     * @param array $jsonData
     * @return array
     * @throws IncorrectFormat
     */
    private function parseJson(array $jsonData): array
    {
        if (!isset($jsonData['board'])) {
            throw new IncorrectFormat('tag board not found');
        }

        if (!isset($jsonData['board']['fixedCells'])) {
            throw new IncorrectFormat('tag board::fixedCells not found');
        }

        $data = [];

        foreach ($jsonData['board']['fixedCells'] as $cell) {
            if (!isset($cell['row'])) {
                throw new IncorrectFormat('cell without tag row found');
            }
            if (!isset($cell['column'])) {
                throw new IncorrectFormat('cell without tag column found');
            }
            if (!isset($cell['value'])) {
                throw new IncorrectFormat('cell without tag value found');
            }
            if (!isset($data[$cell['row']])) {
                $data[$cell['row']] = [];
            }
            $data[$cell['row']][$cell['column']] = $cell['value'];
        }

        return $data;
    }
}
