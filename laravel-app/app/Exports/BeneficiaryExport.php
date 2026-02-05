<?php

namespace App\Exports;

use App\Models\Beneficiary;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BeneficiaryExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Beneficiary::all(['full_name', 'barangay', 'status', 'assistance_amount', 'created_at']);
    }

    public function headings(): array
    {
        return [
            'Full Name',
            'Barangay',
            'Status',
            'Assistance Amount',
            'Date Created',
        ];
    }
}
