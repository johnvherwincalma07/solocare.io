<?php

namespace App\Exports;

use App\Models\SoloParentBeneficiary;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SuperAdminSoloParentBeneficiaryExport implements FromCollection, WithHeadings
{
    protected $year;
    protected $month;

    public function __construct($request = null)
    {
        // Allow passing the whole request
        $this->year = $request?->query('year');
        $this->month = $request?->query('month');
    }

    public function collection()
    {
        $query = SoloParentBeneficiary::query();

        if ($this->year && $this->year != 'all') {
            $query->whereYear('created_at', $this->year);
        }

        if ($this->month && $this->month != 'all') {
            $query->whereMonth('created_at', $this->month);
        }

        return $query->orderBy('barangay')
                     ->orderBy('last_name')
                     ->get([
                         'first_name',
                         'last_name',
                         'barangay',
                         'created_at',
                         'assistance_status',
                         'category'
                     ]);
    }

    public function headings(): array
    {
        return ['First Name', 'Last Name', 'Barangay', 'Date Added', 'Assistance Status', 'Category'];
    }
}
