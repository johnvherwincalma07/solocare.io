<?php

namespace App\Exports;

use App\Models\SoloParentApplication;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class SuperAdminSoloParentApplicationsExport
    implements FromCollection, WithHeadings, WithStyles
{
    protected $year;
    protected $month;

    public function __construct($year = null, $month = null)
    {
        $this->year  = $year;
        $this->month = $month;
    }

    public function collection(): Collection
    {
        $query = SoloParentApplication::query();

        if ($this->year && $this->year !== 'All') {
            $query->whereYear('created_at', $this->year);
        }

        if ($this->month && $this->month !== 'All') {
            $query->whereMonth('created_at', $this->month);
        }

        return $query->orderBy('created_at', 'desc')->get()->map(function ($app) {
            return [
                'reference_no'           => $app->reference_no,
                'last_name'              => $app->last_name,
                'first_name'             => $app->first_name,
                'middle_name'            => $app->middle_name,
                'name_extension'         => $app->name_extension,
                'full_name'              => $app->full_name
                    ?? trim(($app->last_name ?? '') . ', ' . ($app->first_name ?? '')),
                'sex'                    => $app->sex,
                'age'                    => $app->age,
                'place_of_birth'         => $app->place_of_birth,
                'birth_date'             => $app->birth_date,
                'street'                 => $app->street,
                'barangay'               => $app->barangay,
                'municipality'           => $app->municipality,
                'province'               => $app->province,
                'educational_attainment' => $app->educational_attainment,
                'civil_status'           => $app->civil_status,
                'occupation'             => $app->occupation,
                'religion'               => $app->religion,
                'company_agency'         => $app->company_agency,
                'monthly_income'         => $app->monthly_income,
                'employment_status'      => $app->employment_status,
                'contact_number'         => $app->contact_number,
                'email'                  => $app->email,
                'pantawid'               => $app->pantawid,
                'indigenous_person'      => $app->indigenous_person,
                'lgbtq'                  => $app->lgbtq,
                'pwd'                    => $app->pwd,
                'family'                 => $app->family,
                'solo_parent_reason'     => $app->solo_parent_reason,
                'solo_parent_needs'      => $app->solo_parent_needs,
                'emergency_name'         => $app->emergency_name,
                'emergency_relationship' => $app->emergency_relationship,
                'emergency_address'      => $app->emergency_address,
                'emergency_contact'      => $app->emergency_contact,
                'category'               => $app->category,
                'status'                 => $app->status ?? 'Pending',
                'rejection_reason'       => $app->rejection_reason,
                'application_stage'      => $app->application_stage ?? 'Review Application',
                'created_at'             => optional($app->created_at)->format('Y-m-d'),
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
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function pageSetup(PageSetup $pageSetup)
    {
        $pageSetup->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $pageSetup->setPaperSize(PageSetup::PAPERSIZE_LEGAL);
    }
}
