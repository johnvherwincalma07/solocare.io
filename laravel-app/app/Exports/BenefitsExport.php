<?php

namespace App\Exports;

use App\Models\Benefit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BenefitsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Benefit::all(['benefit_name', 'description', 'amount', 'created_at']);
    }

    public function headings(): array
    {
        return [
            'Benefit Name',
            'Description',
            'Amount',
            'Date Created',
        ];
    }
}
