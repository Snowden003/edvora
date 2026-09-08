<?php

namespace App\Services;

use RuntimeException;
use ZipArchive;

class CurriculumSpreadsheetImporter
{
    public function import(string $path): array
    {
        // Check if file is CSV
        if ($this->isCsvFile($path)) {
            $rows = $this->readCsvRows($path);
        } else {
            $rows = $this->readXlsxRows($path);
        }

        if (count($rows) < 2) {
            throw new RuntimeException('فایل اکسل باید شامل سطر عنوان (هدر) و حداقل یک سطر درس باشد. / Add a header row and at least one lesson row.');
        }

        $headers = $this->headers(array_shift($rows));

        $titleColumn = $this->column($headers, [
            'lesson title', 'title', 'lesson', 'name',
            'عنوان درس', 'عنوان', 'سرفصل', 'درس', 'نام درس', 'موضوع درس', 'موضوع',
        ]);

        $durationColumn = $this->column($headers, [
            'duration', 'duration minutes', 'duration_minutes', 'duration_min', 'duration (min)', 'duration (minutes)', 'minutes', 'time',
            'مدت زمان', 'مدت زمان (دقیقه)', 'مدت', 'زمان', 'دقیقه', 'مدت (دقیقه)', 'تایم',
        ]);

        $descriptionColumn = $this->column($headers, [
            'description', 'short description', 'desc', 'summary',
            'توضیحات', 'توضیح', 'شرح', 'خلاصه', 'یادداشت',
        ]);

        if ($titleColumn === null) {
            throw new RuntimeException('ستون «عنوان درس» (Lesson Title یا عنوان) در سطر اول فایل پیدا نشد. / Column "Lesson Title" or "عنوان درس" is required.');
        }

        $lessons = [];

        foreach ($rows as $rowNumber => $row) {
            $title = trim((string) ($row[$titleColumn] ?? ''));
            $rawDuration = $durationColumn !== null ? trim((string) ($row[$durationColumn] ?? '')) : '';
            $description = $descriptionColumn !== null ? trim((string) ($row[$descriptionColumn] ?? '')) : '';

            if ($title === '' && $rawDuration === '' && $description === '') {
                continue;
            }

            $displayRow = $rowNumber + 2;

            if ($title === '') {
                throw new RuntimeException("عنوان درس در سطر {$displayRow} الزامی است. / Lesson title is required on row {$displayRow}.");
            }

            if (mb_strlen($title) > 255) {
                throw new RuntimeException("عنوان درس در سطر {$displayRow} نباید بیش از ۲۵۵ کاراکتر باشد.");
            }

            // Parse duration (support Persian digits, "45 min", "30 دقیقه", etc.)
            $durationMinutes = $this->parseDuration($rawDuration, 30);

            if (mb_strlen($description) > 500) {
                $description = mb_substr($description, 0, 500);
            }

            $lessons[] = [
                'order' => count($lessons) + 1,
                'title' => $title,
                'duration_minutes' => $durationMinutes,
                'description' => $description,
            ];
        }

        if ($lessons === []) {
            throw new RuntimeException('هیچ سطری برای دروس در این فایل یافت نشد. / The spreadsheet does not contain any lesson rows.');
        }

        return $lessons;
    }

    private function isCsvFile(string $path): bool
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($ext === 'csv') return true;

        // Check first few bytes if not zip
        $fp = @fopen($path, 'r');
        if ($fp) {
            $magic = fread($fp, 4);
            fclose($fp);
            if ($magic !== "PK\x03\x04") {
                return true;
            }
        }
        return false;
    }

    private function readCsvRows(string $path): array
    {
        $rows = [];
        if (($handle = fopen($path, 'r')) !== false) {
            while (($data = fgetcsv($handle, 2000, ',')) !== false) {
                if (count($data) === 1 && str_contains($data[0], ';')) {
                    $data = str_getcsv($data[0], ';');
                }
                $rows[] = $data;
            }
            fclose($handle);
        }
        return $rows;
    }

    private function readXlsxRows(string $path): array
    {
        $archive = new ZipArchive();

        if ($archive->open($path) !== true) {
            throw new RuntimeException('فایل اکسل قابل خواندن نیست. لطفاً یک فایل معتبر .xlsx بارگذاری کنید. / Upload a valid .xlsx spreadsheet.');
        }

        try {
            $sharedStrings = $this->sharedStrings($archive);
            $sheet = $archive->getFromName('xl/worksheets/sheet1.xml');

            if ($sheet === false) {
                // Fallback to first sheet found
                for ($i = 0; $i < $archive->numFiles; $i++) {
                    $name = $archive->getNameIndex($i);
                    if (str_starts_with($name, 'xl/worksheets/') && str_ends_with($name, '.xml')) {
                        $sheet = $archive->getFromIndex($i);
                        break;
                    }
                }
            }

            if ($sheet === false) {
                throw new RuntimeException('کاربرگ اول در فایل اکسل یافت نشد. / The spreadsheet must contain a first worksheet.');
            }

            return $this->rows($sheet, $sharedStrings);
        } finally {
            $archive->close();
        }
    }

    private function parseDuration(string $rawDuration, int $default = 30): int
    {
        if ($rawDuration === '') {
            return $default;
        }

        // Convert Persian & Arabic numbers to English
        $str = strtr($rawDuration, [
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
            '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
            '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
        ]);

        if (preg_match('/(\d+)/', $str, $matches)) {
            $val = (int) $matches[1];
            return $val > 0 ? $val : $default;
        }

        return $default;
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
            $normalized = mb_strtolower(trim(preg_replace('/\s+/u', ' ', (string) $header)));
            $headers[$normalized] = $column;
        }

        return $headers;
    }

    private function column(array $headers, array $acceptedHeaders): ?int
    {
        foreach ($acceptedHeaders as $header) {
            $norm = mb_strtolower(trim(preg_replace('/\s+/u', ' ', $header)));
            if (array_key_exists($norm, $headers)) {
                return $headers[$norm];
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
