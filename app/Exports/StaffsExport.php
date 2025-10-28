<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StaffsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::where('role', 2)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Họ tên',
            'Email công ty',
            'Số điện thoại',
            'Phòng ban',
            'Chức vụ',
            'Trạng thái',
            'Ngày tạo',
        ];
    }

    /**
     * @param mixed $staff
     * @return array
     */
    public function map($staff): array
    {
        return [
            $staff->id,
            $staff->name,
            $staff->email,
            $staff->phone ?? '',
            $staff->department ?? '',
            $staff->position ?? '',
            $staff->is_active ? 'Hoạt động' : 'Ngừng hoạt động',
            $staff->created_at->format('d/m/Y H:i'),
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E8F4F8']
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 25,
            'C' => 30,
            'D' => 15,
            'E' => 20,
            'F' => 20,
            'G' => 18,
            'H' => 18,
        ];
    }
}
