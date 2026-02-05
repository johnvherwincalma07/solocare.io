<?php

namespace App\Exports;

use App\Models\SoloParentApplication;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithPageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SoloParentApplicationsExport implements FromCollection, WithHeadings, WithStyles, WithPageSetup
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
        $query = SoloParentApplication::query()
            ->where('barangay', 'Tejero');

        if ($this->year) {
            $query->whereYear('created_at', $this->year);
        }

        if ($this->month) {
            $query->whereMonth('created_at', $this->month);
        }

        return $query->orderBy('created_at', 'desc')->get()->map(function ($app) {
            return [
                $app->reference_no,
                $app->last_name ?? '-',
                $app->first_name ?? '-',
                $app->middle_name ?? '-',
                $app->name_extension ?? '-',
                $app->full_name ?? $app->last_name . ', ' . $app->first_name,
                $app->sex ?? '-',
                $app->age ?? '-',
                $app->place_of_birth ?? '-',
                $app->birth_date ?? '-',
                $app->street ?? '-',
                $app->barangay ?? '-',
                $app->municipality ?? '-',
                $app->province ?? '-',
                $app->educational_attainment ?? '-',
                $app->civil_status ?? '-',
                $app->occupation ?? '-',
                $app->religion ?? '-',
                $app->company_agency ?? '-',
                $app->monthly_income ?? '-',
                $app->employment_status ?? '-',
                $app->contact_number ?? '-',
                $app->email ?? '-',
                $app->pantawid ?? '-',
                $app->indigenous_person ?? '-',
                $app->lgbtq ?? '-',
                $app->pwd ?? '-',
                $app->family ?? '-',
                $app->solo_parent_reason ?? '-',
                $app->solo_parent_needs ?? '-',
                $app->emergency_name ?? '-',
                $app->emergency_relationship ?? '-',
                $app->emergency_address ?? '-',
                $app->emergency_contact ?? '-',
                $app->category ?? '-',
                $app->status ?? 'Pending',
                $app->rejection_reason ?? '-',
                $app->application_stage ?? 'Review Application',
                $app->created_at->format('Y-m-d'),
            ];
        });
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
            'Emergency Contact Relationship',
            'Emergency Contact Address',
            'Emergency Contact Number',
            'Category',
            'Status',
            'Rejection Reason',
            'Application Stage',
            'Date Applied',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]] // Header row bold
        ];
    }

    // ✅ Landscape orientation
    public function pageSetup(Worksheet $sheet)
    {
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_LEGAL);
    }
}
