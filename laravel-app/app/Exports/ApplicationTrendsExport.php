<?php

namespace App\Exports;

use App\Models\SoloParentApplication;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class ApplicationTrendsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Example: count applications per month grouped by status
        return DB::table('solo_parent_applications')
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw("SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) as approved"),
                DB::raw("SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending"),
                DB::raw("SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) as rejected")
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Month',
            'Approved',
            'Pending',
            'Rejected',
        ];
    }
}
