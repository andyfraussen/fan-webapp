<?php

namespace App\Service;

class CsvService
{
    public function parse(string $filename): array
    {
        $data = [];
        if (($handle = fopen($filename, "r")) !== FALSE) {
            $header = fgetcsv($handle);
            while (($row = fgetcsv($handle)) !== FALSE) {
                $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }
        return $data;
    }
}