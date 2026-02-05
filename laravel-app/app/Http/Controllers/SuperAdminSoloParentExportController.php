<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\SuperAdminSoloParentApplicationsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class SuperAdminSoloParentExportController extends Controller
{


public function exportPdf(Request $request)
{
    $year  = $request->query('year');
    $month = $request->query('month');

    $export = new SuperAdminSoloParentApplicationsExport($year, $month);

    // ✅ Already an array — DO NOT convert again
    $applications = collect($export->collection())->values();

    $date = Carbon::now()->format('F d, Y');

    $pdf = Pdf::loadView('super-admin.solo-parent-pdf', [
        'applications' => $applications,
        'date' => $date
    ])->setPaper('A4', 'landscape');

    $filename = 'solo_parent_applications_' . ($year ?? 'all') . '_' . ($month ?? 'all') . '.pdf';

    return $pdf->download($filename);
}





    // Export Excel
    public function exportExcel(Request $request)
    {
        $year = $request->query('year', null);
        $month = $request->query('month', null);

        $export = new SuperAdminSoloParentApplicationsExport($year, $month);

        $filename = 'solo_parent_applications_' . ($year ?? 'all') . '_' . ($month ?? 'all') . '.xlsx';
        return Excel::download($export, $filename);
    }
}
