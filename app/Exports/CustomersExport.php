<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::where('role', 3)
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
            'Mã khách hàng',
            'Họ tên',
            'Email',
            'Số điện thoại',
            'Công ty',
            'Địa chỉ',
            'Nhóm',
            'Trạng thái',
            'Trạng thái hồ sơ',
            'Ngày tạo',
        ];
    }

    /**
     * @param mixed $customer
     * @return array
     */
    public function map($customer): array
    {
        return [
            $customer->id,
            $customer->account_id ?? '',
            $customer->name,
            $customer->email,
            $customer->phone ?? '',
            $customer->company ?? '',
            $customer->address ?? '',
            $customer->group->name ?? '',
            $customer->is_active ? 'Hoạt động' : 'Ngừng hoạt động',
            $customer->must_update_profile ? 'Chưa cập nhật' : 'Đã cập nhật',
            $customer->created_at->format('d/m/Y H:i'),
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
            'B' => 15,
            'C' => 25,
            'D' => 30,
            'E' => 15,
            'F' => 25,
            'G' => 30,
            'H' => 20,
            'I' => 18,
            'J' => 18,
            'K' => 18,
        ];
    }
}
