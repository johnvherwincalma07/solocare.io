<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SoloParentApplication;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SoloParentExportController extends Controller
{
    /**
     * Export PDF of Solo Parent Applications
     */
    public function exportPdf(Request $request)
    {
        $year  = $request->query('year');
        $month = $request->query('month');

        $applications = SoloParentApplication::where('barangay', 'Tejero')
            ->when($year, fn($q) => $q->whereYear('created_at', $year))
            ->when($month, fn($q) => $q->whereMonth('created_at', $month))
            ->orderBy('created_at', 'desc')
            ->get();

        // ✅ Set PDF to landscape
        $pdf = Pdf::loadView('admin.solo-parent-pdf', compact('applications'))
                  ->setPaper('legal', 'landscape'); // <-- LANDSCAPE

        return $pdf->download("solo-parent-applications-{$year}-{$month}.pdf");
    }

    /**
     * Export Excel of Solo Parent Applications
     */
    public function exportExcel(Request $request)
    {
        $year  = $request->query('year');
        $month = $request->query('month');

        return Excel::download(
            new SoloParentExport($year, $month),
            "solo-parent-applications-{$year}-{$month}.xlsx"
        );
    }
}

/**
 * Excel Export Class
 */
class SoloParentExport implements FromCollection, WithHeadings
{
    protected $year;
    protected $month;

    public function __construct($year = null, $month = null)
    {
        $this->year  = $year;
        $this->month = $month;
    }

    public function collection()
    {
        return SoloParentApplication::where('barangay', 'Tejero')
            ->when($this->year, fn($q) => $q->whereYear('created_at', $this->year))
            ->when($this->month, fn($q) => $q->whereMonth('created_at', $this->month))
            ->select(
                'reference_no',
                'last_name',
                'first_name',
                'middle_name',
                'name_extension',
                'full_name',
                'sex',
                'age',
                'place_of_birth',
                'birth_date',
                'street',
                'barangay',
                'municipality',
                'province',
                'educational_attainment',
                'civil_status',
                'occupation',
                'religion',
                'company_agency',
                'monthly_income',
                'employment_status',
                'contact_number',
                'email',
                'pantawid',
                'indigenous_person',
                'lgbtq',
                'pwd',
                'family',
                'solo_parent_reason',
                'solo_parent_needs',
                'emergency_name',
                'emergency_relationship',
                'emergency_address',
                'emergency_contact',
                'category',
                'status',
                'rejection_reason',
                'created_at'
            )
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Reference No',
            'Last Name',
            'First Name',
            'Middle Name',
            'Name Extension',
            'Full Name',
            'Sex',
            'Age',
            'Place of Birth',
            'Birth Date',
            'Street',
            'Barangay',
            'Municipality',
            'Province',
            'Educational Attainment',
            'Civil Status',
            'Occupation',
            'Religion',
            'Company/Agency',
            'Monthly Income',
            'Employment Status',
            'Contact Number',
            'Email',
            'Pantawid',
            'Indigenous Person',
            'LGBTQ',
            'PWD',
            'Family Composition',
            'Solo Parent Reason',
            'Solo Parent Needs',
            'Emergency Contact Name',
            'Emergency Relationship',
            'Emergency Address',
            'Emergency Contact Number',
            'Category',
            'Status',
            'Rejection Reason',
            'Application Date',
        ];
    }
}
