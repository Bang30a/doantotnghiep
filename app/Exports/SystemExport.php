<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SystemExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles, WithCustomStartCell
{
    protected $title;     // Thêm biến chứa Tên Báo cáo
    protected $headings;
    protected $data;

    // Cập nhật constructor để nhận thêm $title
    public function __construct(string $title, array $headings, array $data)
    {
        $this->title = $title;
        $this->headings = $headings;
        $this->data = $data;
    }

    // ÉP BẢNG DỮ LIỆU BẮT ĐẦU TỪ DÒNG SỐ 3 (Dòng 1 cho tiêu đề, dòng 2 để trống)
    public function startCell(): string
    {
        return 'A3'; 
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function array(): array
    {
        return $this->data;
    }

    // PHÙ PHÉP GIAO DIỆN Ở ĐÂY
    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();
        
        // 1. Viết Tên Báo Cáo vào ô A1 và gộp ô (Merge) đến cột cuối cùng
        $sheet->setCellValue('A1', mb_strtoupper($this->title, 'UTF-8'));
        $sheet->mergeCells("A1:{$lastColumn}1");

        // 2. Xác định vùng của Bảng dữ liệu (Từ A3 đến ô cuối cùng)
        $tableRange = "A3:{$lastColumn}{$lastRow}";

        return [
            // Style cho DÒNG 1 (Tên báo cáo: Chữ to, in đậm, căn giữa, màu Tím)
            1 => [
                'font' => ['bold' => true, 'size' => 16, 'color' => ['argb' => 'FF6B21A8']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
            
            // Style cho DÒNG 3 (Tiêu đề cột của Bảng: Màu Tím, chữ Trắng)
            3 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 12],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF6B21A8']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
            
            // Style cho TOÀN BỘ BẢNG (Kẻ khung đen rõ nét)
            $tableRange => [
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }
}