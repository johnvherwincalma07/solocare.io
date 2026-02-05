<?php

namespace App\Exports;

use App\Models\SoloParentBeneficiary;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SoloParentBeneficiaryExport implements FromCollection, WithHeadings, WithStyles
{
    protected $year;
    protected $month;

    public function __construct($year = null, $month = null)
    {
        $this->year = $year;
        $this->month = $month;
    }

    public function collection()
    {
        $query = SoloParentBeneficiary::query();

        // Optional: filter by year
        if ($this->year && $this->year != 'all') {
            $query->whereYear('created_at', $this->year);
        }

        // Optional: filter by month
        if ($this->month && $this->month != 'all') {
            $query->whereMonth('created_at', $this->month);
        }

        return $query->orderBy('created_at', 'desc')->get()->map(function ($item) {
            return [
                $item->first_name ?? '-',
                $item->last_name ?? '-',
                $item->barangay ?? '-',
                $item->assistance_status ?? '-',
                $item->category ?? '-',
                $item->created_at ? $item->created_at->format('Y-m-d') : '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'First Name',
            'Last Name',
            'Barangay',
            'Assistance Status',
            'Category',
            'Date Added',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], // Header row bold
        ];
    }
}
