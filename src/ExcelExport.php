<?php
require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelExport {
    public function generate($data, $userId) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Jadval tepasidagi sarlavhalar
        $sheet->setCellValue('A1', 'Sana');
        $sheet->setCellValue('B1', 'Summa (so\'m)');
        $sheet->setCellValue('C1', 'Kategoriya');

        $row = 2;
        foreach ($data as $item) {
            // MongoDB vaqtini o'qiladigan formatga o'tkazish
            $date = $item['timestamp']->toDateTime()->format('d.m.Y H:i');
            
            $sheet->setCellValue('A' . $row, $date);
            $sheet->setCellValue('B' . $row, $item['amount']);
            $sheet->setCellValue('C' . $row, $item['category']);
            $row++;
        }

        $fileName = "hisobot_$userId.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileName);

        return $fileName;
    }
}
