<?php

namespace App\Services;

use RuntimeException;
use ZipArchive;

class CurriculumSpreadsheetImporter
{
    public function import(string $path): array
    {
        $archive = new ZipArchive();

        if ($archive->open($path) !== true) {
            throw new RuntimeException('The uploaded file could not be read. Upload a valid .xlsx spreadsheet.');
        }

        try {
            $sharedStrings = $this->sharedStrings($archive);
            $sheet = $archive->getFromName('xl/worksheets/sheet1.xml');

            if ($sheet === false) {
                throw new RuntimeException('The spreadsheet must contain a first worksheet.');
            }

            $rows = $this->rows($sheet, $sharedStrings);
        } finally {
            $archive->close();
        }

        if (count($rows) < 2) {
            throw new RuntimeException('Add a header row and at least one lesson row before uploading.');
        }

        $headers = $this->headers(array_shift($rows));
        $titleColumn = $this->column($headers, ['lesson title', 'title']);
        $durationColumn = $this->column($headers, ['duration', 'duration minutes', 'duration_minutes']);
        $descriptionColumn = $this->column($headers, ['description', 'short description']);

        if ($titleColumn === null || $durationColumn === null || $descriptionColumn === null) {
            throw new RuntimeException('Use these column headers in the first row: Lesson Title, Duration, Description.');
        }

        $lessons = [];

        foreach ($rows as $rowNumber => $row) {
            $title = trim($row[$titleColumn] ?? '');
            $duration = trim($row[$durationColumn] ?? '');
            $description = trim($row[$descriptionColumn] ?? '');

            if ($title === '' && $duration === '' && $description === '') {
                continue;
            }

            $displayRow = $rowNumber + 2;

            if ($title === '') {
                throw new RuntimeException("Lesson title is required on row {$displayRow}.");
            }

            if (mb_strlen($title) > 255) {
                throw new RuntimeException("Lesson title on row {$displayRow} must be 255 characters or fewer.");
            }

            if (! is_numeric($duration) || (int) $duration < 1) {
                throw new RuntimeException("Duration on row {$displayRow} must be a whole number of minutes.");
            }

            if (mb_strlen($description) > 500) {
                throw new RuntimeException("Description on row {$displayRow} must be 500 characters or fewer.");
            }

            $lessons[] = [
                'order' => count($lessons) + 1,
                'title' => $title,
                'duration_minutes' => (int) $duration,
                'description' => $description,
            ];
        }

        if ($lessons === []) {
            throw new RuntimeException('The spreadsheet does not contain any lesson rows.');
        }

        return $lessons;
    }

    private function sharedStrings(ZipArchive $archive): array
    {
        $contents = $archive->getFromName('xl/sharedStrings.xml');

        if ($contents === false) {
            return [];
        }

        $xml = simplexml_load_string($contents);
        $xml->registerXPathNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

        return array_map(
            function ($node) {
                $node->registerXPathNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

                return trim(implode('', array_map('strval', $node->xpath('.//main:t') ?: [])));
            },
            $xml->xpath('//main:si') ?: [],
        );
    }

    private function rows(string $sheet, array $sharedStrings): array
    {
        $xml = simplexml_load_string($sheet);
        $xml->registerXPathNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $result = [];

        foreach ($xml->xpath('//main:sheetData/main:row') ?: [] as $row) {
            $row->registerXPathNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $values = [];

            foreach ($row->xpath('./main:c') ?: [] as $cell) {
                $cell->registerXPathNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                $reference = (string) $cell['r'];
                $column = $this->columnIndex($reference);
                $type = (string) $cell['t'];
                $value = (string) ($cell->xpath('./main:v')[0] ?? '');

                if ($type === 's') {
                    $value = $sharedStrings[(int) $value] ?? '';
                }

                if ($type === 'inlineStr') {
                    $value = trim(implode('', array_map('strval', $cell->xpath('./main:is/main:t') ?: [])));
                }

                $values[$column] = $value;
            }

            $result[] = $values;
        }

        return $result;
    }

    private function headers(array $headerRow): array
    {
        $headers = [];

        foreach ($headerRow as $column => $header) {
            $headers[mb_strtolower(trim($header))] = $column;
        }

        return $headers;
    }

    private function column(array $headers, array $acceptedHeaders): ?int
    {
        foreach ($acceptedHeaders as $header) {
            if (array_key_exists($header, $headers)) {
                return $headers[$header];
            }
        }

        return null;
    }

    private function columnIndex(string $reference): int
    {
        $letters = preg_replace('/\d+/', '', $reference);
        $index = 0;

        foreach (str_split($letters) as $letter) {
            $index = ($index * 26) + (ord($letter) - 64);
        }

        return $index - 1;
    }
}
