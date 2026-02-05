<?php

namespace App\Http\Controllers;

use App\Models\SoloParentBeneficiary;
use Illuminate\Http\Request;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SoloParentBeneficiaryExport;


class SoloParentBeneficiaryController extends Controller
{
    /**
     * Export Solo Parent Beneficiaries as PDF
     */
    public function exportPdf(Request $request)
    {
        $year = $request->query('year');
        $month = $request->query('month');

        $query = SoloParentBeneficiary::query();

        if ($year && $year != 'all') {
            $query->whereYear('created_at', $year);
        }

        if ($month && $month != 'all') {
            $query->whereMonth('created_at', $month);
        }

        $beneficiaries = $query->orderBy('barangay')
                              ->orderBy('last_name')
                              ->get();

        $pdf = PDF::loadView(
            'admin.solo_parent_beneficiaries_pdf',
            compact('beneficiaries')
        )->setPaper('legal', 'landscape');

        $fileName = 'SoloParentBeneficiaries_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Export Solo Parent Beneficiaries as Excel
     */
    public function exportExcel(Request $request)
    {
        return Excel::download(new SoloParentBeneficiaryExport($request), 
                               'SoloParentBeneficiaries_' . now()->format('Ymd_His') . '.xlsx');
    }
}
