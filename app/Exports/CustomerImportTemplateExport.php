<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CustomerImportTemplateExport implements
    FromArray,
    WithHeadings,
    WithTitle,
    WithEvents,
    ShouldAutoSize
{
    use Exportable;

    public function array(): array
    {
        return [];
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Email',
            'Nomor Telepon',
            'Nomor Dokumen',
            'Nama Instansi',
            'Jenis Instansi',
            'Kota',
            'Provinsi',
        ];
    }

    public function title(): string
    {
        return 'customers';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();

                $highestColumn = 'H';

                $sheet->getStyle(
                    "A1:{$highestColumn}1"
                )->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => '0F172A',
                        ],
                    ],

                    'font' => [
                        'bold' => true,
                        'color' => [
                            'rgb' => 'FFFFFF',
                        ],
                    ],

                    'alignment' => [
                        'horizontal' =>
                            Alignment::HORIZONTAL_CENTER,

                        'vertical' =>
                            Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getRowDimension(1)
                    ->setRowHeight(28);

                $sheet->freezePane('A2');

                $sheet->setAutoFilter(
                    "A1:{$highestColumn}1"
                );

                $sheet->getColumnDimension('A')
                    ->setWidth(28);

                $sheet->getColumnDimension('B')
                    ->setWidth(32);

                $sheet->getColumnDimension('C')
                    ->setWidth(20);

                $sheet->getColumnDimension('D')
                    ->setWidth(20);

                $sheet->getColumnDimension('E')
                    ->setWidth(34);

                $sheet->getColumnDimension('F')
                    ->setWidth(24);

                $sheet->getColumnDimension('G')
                    ->setWidth(20);

                $sheet->getColumnDimension('H')
                    ->setWidth(22);

                /*
                |--------------------------------------------------------------------------
                | Format nomor telepon dan nomor dokumen sebagai text
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle('C2:D1000')
                    ->getNumberFormat()
                    ->setFormatCode('@');

                /*
                |--------------------------------------------------------------------------
                | Dropdown Jenis Instansi
                |--------------------------------------------------------------------------
                */

                for ($row = 2; $row <= 1000; $row++) {
                    $validation = $sheet
                        ->getCell("F{$row}")
                        ->getDataValidation();

                    $validation->setType(
                        DataValidation::TYPE_LIST
                    );

                    $validation->setErrorStyle(
                        DataValidation::STYLE_STOP
                    );

                    $validation->setAllowBlank(true);

                    $validation->setShowInputMessage(true);

                    $validation->setShowErrorMessage(true);

                    $validation->setErrorTitle(
                        'Jenis instansi tidak valid'
                    );

                    $validation->setError(
                        'Pilih jenis instansi dari daftar yang tersedia.'
                    );

                    $validation->setFormula1(
                        '"Pemerintah,Sekolah,Perguruan Tinggi,Perusahaan,Yayasan,Lembaga,Lainnya"'
                    );
                }
            },
        ];
    }
}