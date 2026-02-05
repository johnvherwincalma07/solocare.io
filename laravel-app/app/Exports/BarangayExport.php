<?php

namespace App\Exports;

use App\Models\Barangay;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BarangayExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Barangay::all(['name', 'population', 'created_at']);
    }

    public function headings(): array
    {
        return [
            'Barangay Name',
            'Population',
            'Date Created',
        ];
    }
}
