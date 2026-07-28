<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CollectionExport implements FromCollection, WithEvents, WithHeadings
{
    public function __construct(
        private readonly string $title,
        private readonly array $data,
        private readonly array $headers,
        private readonly object $user,
        private readonly string $color = '00695C',
        private readonly bool $includeHeader = true,
    ) {}

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return $this->includeHeader ? $this->headers : [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $highestColumn = $sheet->getHighestColumn();

                $headerRowOffset = $this->title ? 4 : 3;
                $sheet->insertNewRowBefore(1, $headerRowOffset - 1);

                $currentRow = 1;

                if ($this->title) {
                    $sheet->mergeCells("A{$currentRow}:{$highestColumn}{$currentRow}");
                    $sheet->setCellValue("A{$currentRow}", $this->title);

                    $sheet->getStyle("A{$currentRow}:{$highestColumn}{$currentRow}")->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 16,
                            'color' => ['rgb' => 'FFFFFF'],
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => $this->color],
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);

                    $sheet->getRowDimension($currentRow)->setRowHeight(36);
                    $currentRow++;
                }

                $userName = $this->user->name ?? 'Sistema';
                $userEmail = $this->user->email ?? '';

                $requestDate = now()->format('d/m/Y H:i:s');

                $sheet->setCellValue("A{$currentRow}", "Data da Solicitação: {$requestDate}");
                $sheet->getStyle("A{$currentRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);
                $currentRow++;

                $userLabel = $userEmail
                    ? "Usuário: {$userName} ({$userEmail})"
                    : "Usuário: {$userName}";

                $sheet->setCellValue("A{$currentRow}", $userLabel);
                $sheet->getStyle("A{$currentRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);

                $metaStartRow = $this->title ? 2 : 1;

                $sheet->getStyle("A{$metaStartRow}:{$highestColumn}{$currentRow}")->applyFromArray([
                    'font' => [
                        'size' => 10,
                        'color' => ['rgb' => '000000'],
                    ],
                ]);

                $sheet->getRowDimension($currentRow)->setRowHeight(22);
                $currentRow++;

                $headerRange = "A{$currentRow}:{$highestColumn}{$currentRow}";

                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $this->color],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'FFFFFF'],
                        ],
                    ],
                ]);

                $sheet->getRowDimension($currentRow)->setRowHeight(28);

                $dataStartRow = $currentRow + 1;
                $dataEndRow = $sheet->getHighestRow();

                if ($dataEndRow >= $dataStartRow) {
                    $dataRange = "A{$dataStartRow}:{$highestColumn}{$dataEndRow}";

                    $sheet->getStyle($dataRange)->applyFromArray([
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'DDDDDD'],
                            ],
                        ],
                    ]);

                    for ($row = $dataStartRow; $row <= $dataEndRow; $row++) {
                        if ($row % 2 === 0) {
                            $sheet->getStyle("A{$row}:{$highestColumn}{$row}")->applyFromArray([
                                'fill' => [
                                    'fillType' => Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => 'F5F5F5'],
                                ],
                            ]);
                        }
                    }
                }

                foreach (range('A', $highestColumn) as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}
