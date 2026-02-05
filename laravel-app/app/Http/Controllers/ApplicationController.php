<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SoloParentApplication;
use App\Models\ApplicationFile;
use App\Models\ScheduledSubmission;
use App\Models\HomeVisit;
use App\Models\Announcement;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use setasign\Fpdi\Fpdi;
use FPDF;
use Illuminate\Support\Facades\Storage;


class ApplicationController extends Controller
{
    // -----------------------------
    // 1️⃣ User Dashboard
    // -----------------------------
public function userDashboard()
{
    $user = Auth::user();

    $application = SoloParentApplication::where('user_id', $user->id)
        ->where('is_submitted', true)
        ->latest()
        ->first();

    $hasSubmitted = $application ? true : false;
    $currentStage = $application ? $this->getCurrentStage($application) : null;

    $announcements = Announcement::where('status', 'pending')
        ->latest()
        ->take(5)
        ->get();

    $scheduledNotifications = [];

    if ($application) {
        $applicantName = $user->first_name . ' ' . $user->last_name;

        // ---------- Rejected ----------
        if ($application->status === 'Rejected') {
            $scheduledNotifications[] = [
                'type' => 'Rejected',
                'message' => "We're sorry, {$applicantName}. Your Solo Parent application has been rejected."
            ];
        }

        // ---------- Review Application ----------
        elseif (in_array($application->application_stage, ['Review Application', 'Application'])) {
            $scheduledNotifications[] = [
                'type' => 'Review',
                'message' => "Hello {$applicantName}, your Solo Parent application is now under review.",
            ];
        }

        // ---------- Scheduled Submission ----------
        if ($application->application_stage === 'Scheduled for Submission') {
            $scheduledSubmission = ScheduledSubmission::where('application_id', $application->application_id)
                ->orderBy('scheduled_date', 'asc')
                ->first();

            if ($application->status === 'Awaiting Documents') {
                $scheduledNotifications[] = [
                    'type' => 'Scheduled Submission',
                    'message' => "Hello {$applicantName}, your application is awaiting document submission.",
                ];
            } elseif ($application->status === 'Documents Scheduled' && $scheduledSubmission) {
                $scheduledNotifications[] = [
                    'type' => 'Scheduled Submission',
                    'date' => $scheduledSubmission->scheduled_date,
                    'message' => "Hello {$applicantName}, your application submission is scheduled on "
                        . \Carbon\Carbon::parse($scheduledSubmission->scheduled_date)->format('F j, Y')
                        . " at " . \Carbon\Carbon::parse($scheduledSubmission->scheduled_time)->format('h:i A'),
                ];
            }
        }

        // ---------- Home Visit ----------
        if ($application->application_stage === 'Home Visit') {
            $homeVisit = HomeVisit::where('application_id', $application->application_id)
                ->orderBy('visit_date', 'asc')
                ->first();

            if ($application->status === 'Awaiting Home Visit') {
                $scheduledNotifications[] = [
                    'type' => 'Home Visit',
                    'message' => "Hello {$applicantName}, your application is awaiting home visit scheduling.",
                ];
            } elseif ($application->status === 'Home Visit Scheduled' && $homeVisit) {
                $scheduledNotifications[] = [
                    'type' => 'Home Visit',
                    'date' => $homeVisit->visit_date,
                    'message' => "Hello {$applicantName}, your home visit is scheduled on "
                        . \Carbon\Carbon::parse($homeVisit->visit_date)->format('F j, Y')
                        . " at " . \Carbon\Carbon::parse($homeVisit->visit_time)->format('h:i A'),
                ];
            }
        }

        // ---------- Ready to Process ----------
        if ($application->application_stage === 'Ready to Process') {
            $scheduledNotifications[] = [
                'type' => 'Ready to Process',
                'message' => "Hello {$applicantName}, your application is now ready for final processing.",
            ];
        }

        // ---------- Approved ----------
        if ($application->application_stage === 'Verified Solo Parent') {
            $scheduledNotifications[] = [
                'type' => 'Approved',
                'message' => "Congratulations {$applicantName}! Your Solo Parent application has been approved.",
            ];
        }
    }
    
    
        // ---------- 2️⃣ Fetch real notifications from DB ----------
    $dbNotifications = \DB::table('notifications')
        ->where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get()
        ->map(function($n) {
            return [
                'type' => $n->type,
                'message' => $n->message,
                'date' => $n->created_at,
                'is_read' => $n->is_read,
            ];
        })->toArray();

    // Merge both arrays (DB notifications + scheduled notifications)
    $allNotifications = array_merge($dbNotifications, $scheduledNotifications);

    // Sort by date descending if possible
    usort($allNotifications, function($a, $b) {
        $dateA = isset($a['date']) ? strtotime($a['date']) : 0;
        $dateB = isset($b['date']) ? strtotime($b['date']) : 0;
        return $dateB <=> $dateA;
    });
    
    

    return view('user.dashboard', compact(
        'user',
        'application',
        'hasSubmitted',
        'currentStage',
        'announcements',
        'allNotifications'
    ));
}

// -----------------------------
// Helper: Get Current Stage (Fixed with Rejected)
// -----------------------------
private function getCurrentStage(SoloParentApplication $application)
{
    // ---------- Rejected ----------
    if ($application->status === 'Rejected') {
        return 'Rejected';
    }

    $appId = $application->application_id;

    if (ScheduledSubmission::where('application_id', $appId)->exists()) {
        return 'Scheduled for Submission';
    } elseif (HomeVisit::where('application_id', $appId)->exists()) {
        return 'Home Visit';
    } elseif (\App\Models\ReadyToProcess::where('application_id', $appId)->exists()) {
        return 'Ready to Process';
    } elseif (\App\Models\SoloParentBeneficiary::where('application_id', $appId)->exists()) {
        return 'Verified / Beneficiary';
    }

    return $application->application_stage ?? 'Application';
}

    // -----------------------------
    // 2️⃣ Store Application
    // -----------------------------
    public function store(Request $request)
    {
        try {
            if (SoloParentApplication::where('user_id', Auth::id())->where('is_submitted', true)->exists()) {
                return redirect()
                    ->route('user.dashboard')
                    ->with('error', 'You have already submitted an application.');
            }

            $validated = $request->validate([
                'last_name' => 'required|string|max:255',
                'first_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'name_extension' => 'nullable|string|max:50',
                'sex' => 'required|string',
                'age' => 'required|integer|min:18',
                'place_of_birth' => 'required|string|max:255',
                'birth_date' => 'required|date',
                'street' => 'required|string|max:255',
                'barangay' => 'required|string|max:255',
                'municipality' => 'required|string|max:255',
                'province' => 'required|string|max:255',
                'educational_attainment' => 'nullable|string|max:255',
                'civil_status' => 'required|string',
                'occupation' => 'nullable|string|max:255',
                'religion' => 'nullable|string|max:100',
                'company_agency' => 'nullable|string|max:255',
                'monthly_income' => 'nullable|numeric|min:0',
                'employment_status' => 'required|string',
                'contact_number' => 'required|string|max:20',
                'email' => 'nullable|email|max:100',
                'pantawid' => 'nullable|string|max:255',
                'indigenous_person' => 'nullable|string|max:255',
                'lgbtq' => 'nullable|string|max:255',
                'pwd' => 'nullable|string|max:255',
                'solo_parent_reason' => 'required|string',
                'solo_parent_needs' => 'required|string',
                'emergency_name' => 'required|string|max:255',
                'emergency_relationship' => 'required|string|max:100',
                'emergency_address' => 'required|string|max:255',
                'emergency_contact' => 'required|string|max:20',
                'category' => 'required|string|max:255',
                'declaration' => 'required|accepted',
                'family_name.*' => 'nullable|string|max:255',
                'family_relationship.*' => 'nullable|string|max:255',
                'family_age.*' => 'nullable|integer|min:0',
                'family_dob.*' => 'nullable|date',
                'family_civil_status.*' => 'nullable|string|max:50',
                'family_occupation.*' => 'nullable|string|max:255',
                'family_income.*' => 'nullable|numeric|min:0',
                'family_education.*' => 'nullable|string|max:255',
                'voters_id' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
                'barangay_certificate' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
                'documents.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',

                
            ]);

            $family = [];
            if ($request->filled('family_name')) {
                foreach ($request->family_name as $i => $name) {
                    if (!empty($name)) {
                        $family[] = [
                            'name' => $name,
                            'relationship' => $request->family_relationship[$i] ?? '',
                            'age' => $request->family_age[$i] ?? null,
                            'birth_date' => $request->family_dob[$i] ?? null,
                            'civil_status' => $request->family_civil_status[$i] ?? '',
                            'occupation' => $request->family_occupation[$i] ?? '',
                            'monthly_income' => $request->family_income[$i] ?? 0,
                            'educational_attainment' => $request->family_education[$i] ?? '',
                        ];
                    }
                }
            }

            $referenceNo = 'SP-' . now()->format('Ymd') . '-' . rand(1000, 9999);

            $application = SoloParentApplication::create([
                'user_id' => Auth::id(),
                'reference_no' => $referenceNo,
                'last_name' => $validated['last_name'],
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'name_extension' => $validated['name_extension'] ?? null,
                'sex' => $validated['sex'],
                'age' => $validated['age'],
                'place_of_birth' => $validated['place_of_birth'],
                'birth_date' => $validated['birth_date'],
                'street' => $validated['street'],
                'barangay' => $validated['barangay'],
                'municipality' => $validated['municipality'],
                'province' => $validated['province'],
                'educational_attainment' => $validated['educational_attainment'] ?? null,
                'civil_status' => $validated['civil_status'],
                'occupation' => $validated['occupation'] ?? null,
                'religion' => $validated['religion'] ?? null,
                'company_agency' => $validated['company_agency'] ?? null,
                'monthly_income' => $validated['monthly_income'] ?? null,
                'employment_status' => $validated['employment_status'],
                'contact_number' => $validated['contact_number'],
                'email' => $validated['email'] ?? null,
                'pantawid' => $validated['pantawid'] ?? 'No',
                'indigenous_person' => $validated['indigenous_person'] ?? 'No',
                'lgbtq' => $validated['lgbtq'] ?? 'No',
                'pwd' => $validated['pwd'] ?? 'No',
                'family' => json_encode($family),
                'solo_parent_reason' => $validated['solo_parent_reason'],
                'solo_parent_needs' => $validated['solo_parent_needs'],
                'emergency_name' => $validated['emergency_name'],
                'emergency_relationship' => $validated['emergency_relationship'],
                'emergency_address' => $validated['emergency_address'],
                'emergency_contact' => $validated['emergency_contact'],
                'category' => $validated['category'],
                'declaration' => true,
                'status' => 'Pending',
                'application_stage' => 'Review Application',
                'is_submitted' => true,
            ]);

            Log::info('Application created successfully: ' . $application->application_id);
            
            AuditLog::create([
                'user' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                'action' => "Submitted a new Solo Parent Application (Reference No: {$application->reference_no})",
                'module' => 'Solo Parent Applications',
                'status' => 'Success',
            ]);
            
                    // ---------- Notify User ----------
            \DB::table('notifications')->insert([
                'user_id' => Auth::id(),
                'type' => 'Submitted',
                'message' => "Your Solo Parent application (Ref: {$application->reference_no}) has been successfully submitted and is now under review.",
                'is_read' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

 // Save supporting documents
if ($request->hasFile('documents')) {
    foreach ($request->file('documents') as $file) {
        $path = $file->store('applications/' . $application->application_id, 'public');
        ApplicationFile::create([
            'application_id' => $application->application_id,
            'path' => $path,
            'document_type' => 'Supporting Document', // <--- add this
        ]);
    }
}

// Save Voters ID
if ($request->hasFile('voters_id')) {
    $file = $request->file('voters_id');
    $path = $file->store('applications/' . $application->application_id, 'public');
    ApplicationFile::create([
        'application_id' => $application->application_id,
        'path' => $path,
        'document_type' => 'Voters ID',
    ]);
}

// Save Barangay Certificate
if ($request->hasFile('barangay_certificate')) {
    $file = $request->file('barangay_certificate');
    $path = $file->store('applications/' . $application->application_id, 'public');
    ApplicationFile::create([
        'application_id' => $application->application_id,
        'path' => $path,
        'document_type' => 'Barangay Certificate',
    ]);
}

            
            

            return redirect()
                ->route('user.dashboard')
                ->with('success', 'Your application has been successfully submitted!');
        

        } catch (\Exception $e) {
            Log::error('Application Store Error: ' . $e->getMessage());
            return redirect()
    ->back()
    ->withInput()
    ->with('error', 'Something went wrong while submitting the application.');

        }
    }

    // -----------------------------
    // 3️⃣ Track Application
    // -----------------------------
    public function trackApplication(Request $request, $reference_no = null)
    {
        if ($request->isMethod('post') && !$reference_no) {
            $reference_no = $request->input('reference_no');
        }

        $application = SoloParentApplication::where('reference_no', $reference_no)
            ->where('user_id', Auth::id())
            ->first();

        if (!$application) {
            return response()->json(['error' => 'Application not found'], 404);
        }

        $currentStage = $this->getCurrentStage($application);

        return response()->json([
            'reference_no' => $application->reference_no,
            'stage' => $currentStage,
            'status' => $application->status,
            'updated_at' => $application->updated_at->format('M d, Y H:i')
        ]);
    }

    // -----------------------------
    // 4️⃣ Download PDF
    // -----------------------------
// -----------------------------
// 4️⃣ Download PDF
// -----------------------------
public function downloadPdf($id)
{
    $app = SoloParentApplication::where('application_id', $id)
        ->where('user_id', Auth::id()) // ensure only owner can download
        ->firstOrFail();

    $pdfFile = public_path('form/SOLO PARENT APPLICATION.pdf'); // same as viewPdf
    if (!file_exists($pdfFile)) abort(404, 'Template PDF not found.');

    $pdf = new Fpdi();
    $pdf->AddPage();
    $pdf->setSourceFile($pdfFile);
    $template = $pdf->importPage(1);
    $pdf->useTemplate($template);

    $pdf->SetFont('Arial', '', 10);
    $check = fn($condition) => $condition ? $pdf->Write(5, 'X') : null;

    // Helper: shrink text if too long
    $writeFitText = function($x, $y, $maxWidth, $text, $initialFont = 10) use ($pdf) {
        $pdf->SetXY($x, $y);
        $fontSize = $initialFont;
        $pdf->SetFont('Arial', '', $fontSize);
        while ($pdf->GetStringWidth($text) > $maxWidth && $fontSize > 5) {
            $fontSize -= 0.5;
            $pdf->SetFont('Arial', '', $fontSize);
        }
        $pdf->Write(5, $text);
    };

    // === COORDINATES MAP ===
    $coords = [
        'reference_no' => ['x' => 38, 'y' => 25],
        'full_name' => ['x' => 38, 'y' => 44],
        'age' => ['x' => 123, 'y' => 44],
        'sex' => ['x' => 167, 'y' => 44],
        'birth_date' => ['x' => 39, 'y' => 49],
        'place_of_birth' => ['x' => 139, 'y' => 49, 'maxWidth' => 50],
        'address' => ['x' => 32, 'y' => 54, 'width' => 160, 'line_height' => 5],

        'pantawid_yes' => ['x' => 20, 'y' => 87],
        'pantawid_no' => ['x' => 41, 'y' => 87],
        'indigenous_yes' => ['x' => 74, 'y' => 87],
        'indigenous_no' => ['x' => 94, 'y' => 87],
        'lgbtq_yes' => ['x' => 119, 'y' => 87],
        'lgbtq_no' => ['x' => 140, 'y' => 87],
        'pwd_yes' => ['x' => 165, 'y' => 87],
        'pwd_no' => ['x' => 186, 'y' => 87],

        'employed' => ['x' => 57, 'y' => 68],
        'self_employed' => ['x' => 99, 'y' => 74],
        'not_employed' => ['x' => 146, 'y' => 73],
        'civil_status_text' => ['x' => 135, 'y' => 59, 'maxWidth' => 40],

        'category_check' => ['x' => [20, 45, 70, 95], 'y' => 160],
        'category_text' => ['x' => 53, 'y' => 253, 'width' => 170, 'line_height' => 5, 'maxWidth' => 170],

        'circumstances' => ['x' => 20, 'y' => 154, 'width' => 170, 'line_height' => 5],
        'needs' => ['x' => 20, 'y' => 167, 'width' => 170, 'line_height' => 5],

        'emergency_name' => ['x' => 26, 'y' => 186],
        'emergency_relationship' => ['x' => 133, 'y' => 186],
        'emergency_contact' => ['x' => 144, 'y' => 191],
        'emergency_address' => ['x' => 30, 'y' => 191],

        'contact_number' => ['x' => 49, 'y' => 78, 'maxWidth' => 50],
        'email' => ['x' => 141, 'y' => 78, 'maxWidth' => 50],
        'religion' => ['x' => 130, 'y' => 64, 'maxWidth' => 50],
        'monthly_income' => ['x' => 144, 'y' => 68, 'maxWidth' => 50],
        'occupation' => ['x' => 36, 'y' => 64, 'maxWidth' => 50],
        'company_agency' => ['x' => 49, 'y' => 68, 'maxWidth' => 50],
        'educational_attainment' => ['x' => 58, 'y' => 59, 'maxWidth' => 50],

        'family_composition' => ['x' => 16, 'y' => 111],
        'signature' => ['x' => 48, 'y' => 209],
    ];

    // === NORMALIZE EMPLOYMENT STATUS ===
    $employmentMap = [
        'employed' => 'employed',
        'self-employed' => 'self-employed',
        'self employed' => 'self-employed',
        'not employed' => 'not-employed',
    ];
    $employment = $employmentMap[strtolower(trim($app->employment_status))] ?? '';
    $app->employment_status = $employment;

    // === PERSONAL INFO ===
    $pdf->SetXY($coords['reference_no']['x'], $coords['reference_no']['y']);
    $pdf->Write(5, $app->reference_no);

    $pdf->SetXY($coords['full_name']['x'], $coords['full_name']['y']);
    $pdf->Write(5, strtoupper("{$app->first_name} " . ($app->middle_name ?? '') . " {$app->last_name}"));

    $pdf->SetXY($coords['age']['x'], $coords['age']['y']);
    $pdf->Write(5, $app->age);

    $pdf->SetXY($coords['sex']['x'], $coords['sex']['y']);
    $pdf->Write(5, strtoupper($app->sex));

    $pdf->SetXY($coords['birth_date']['x'], $coords['birth_date']['y']);
    $pdf->Write(5, $app->birth_date);

    $writeFitText($coords['place_of_birth']['x'], $coords['place_of_birth']['y'], $coords['place_of_birth']['maxWidth'], strtoupper($app->place_of_birth));

    $pdf->SetXY($coords['address']['x'], $coords['address']['y']);
    $pdf->MultiCell($coords['address']['width'], $coords['address']['line_height'], strtoupper($app->address));

    // === SOCIAL INDICATORS ===
    $pdf->SetXY($coords['pantawid_yes']['x'], $coords['pantawid_yes']['y']); $check(strtolower($app->pantawid) === 'yes');
    $pdf->SetXY($coords['pantawid_no']['x'], $coords['pantawid_no']['y']); $check(strtolower($app->pantawid) === 'no');
    $pdf->SetXY($coords['indigenous_yes']['x'], $coords['indigenous_yes']['y']); $check(strtolower($app->indigenous_person) === 'yes');
    $pdf->SetXY($coords['indigenous_no']['x'], $coords['indigenous_no']['y']); $check(strtolower($app->indigenous_person) === 'no');
    $pdf->SetXY($coords['lgbtq_yes']['x'], $coords['lgbtq_yes']['y']); $check(strtolower($app->lgbtq) === 'yes');
    $pdf->SetXY($coords['lgbtq_no']['x'], $coords['lgbtq_no']['y']); $check(strtolower($app->lgbtq) === 'no');
    $pdf->SetXY($coords['pwd_yes']['x'], $coords['pwd_yes']['y']); $check(strtolower($app->pwd) === 'yes');
    $pdf->SetXY($coords['pwd_no']['x'], $coords['pwd_no']['y']); $check(strtolower($app->pwd) === 'no');

    // === EMPLOYMENT STATUS ===
    $pdf->SetXY($coords['employed']['x'], $coords['employed']['y']); $check($employment === 'employed');
    $pdf->SetXY($coords['self_employed']['x'], $coords['self_employed']['y']); $check($employment === 'self-employed');
    $pdf->SetXY($coords['not_employed']['x'], $coords['not_employed']['y']); $check($employment === 'not-employed');

    // === CIVIL STATUS ===
    $writeFitText($coords['civil_status_text']['x'], $coords['civil_status_text']['y'], $coords['civil_status_text']['maxWidth'], strtoupper($app->civil_status));

    // === CATEGORY CHECKBOXES + TEXT ===
    $categoryFields = [$app->pantawid, $app->indigenous_person, $app->lgbtq, $app->pwd];
    foreach ($categoryFields as $i => $val) {
        $pdf->SetXY($coords['category_check']['x'][$i], $coords['category_check']['y']);
        $check(strtolower($val) === 'yes');
    }
    $writeFitText($coords['category_text']['x'], $coords['category_text']['y'], $coords['category_text']['maxWidth'], strtoupper($app->category));

    // === CIRCUMSTANCES & NEEDS ===
    $pdf->SetXY($coords['circumstances']['x'], $coords['circumstances']['y']);
    $pdf->MultiCell($coords['circumstances']['width'], $coords['circumstances']['line_height'], strtoupper($app->solo_parent_reason));

    $pdf->SetXY($coords['needs']['x'], $coords['needs']['y']);
    $pdf->MultiCell($coords['needs']['width'], $coords['needs']['line_height'], strtoupper($app->solo_parent_needs));

    // === EMERGENCY CONTACT ===
    $pdf->SetXY($coords['emergency_name']['x'], $coords['emergency_name']['y']); $pdf->Write(5, strtoupper($app->emergency_name));
    $pdf->SetXY($coords['emergency_relationship']['x'], $coords['emergency_relationship']['y']); $pdf->Write(5, strtoupper($app->emergency_relationship));
    $pdf->SetXY($coords['emergency_contact']['x'], $coords['emergency_contact']['y']); $pdf->Write(5, strtoupper($app->emergency_contact));
    $pdf->SetXY($coords['emergency_address']['x'], $coords['emergency_address']['y']); $pdf->Write(5, strtoupper($app->emergency_address));

    // === CONTACT INFO, RELIGION, INCOME, OCCUPATION, COMPANY, EDUCATION ===
    $writeFitText($coords['contact_number']['x'], $coords['contact_number']['y'], $coords['contact_number']['maxWidth'], strtoupper($app->contact_number ?? ''));
    $writeFitText($coords['email']['x'], $coords['email']['y'], $coords['email']['maxWidth'], strtolower($app->email ?? ''));
    $writeFitText($coords['religion']['x'], $coords['religion']['y'], $coords['religion']['maxWidth'], strtoupper($app->religion ?? ''));
    $writeFitText($coords['monthly_income']['x'], $coords['monthly_income']['y'], $coords['monthly_income']['maxWidth'], strtoupper($app->monthly_income ?? ''));
    $writeFitText($coords['occupation']['x'], $coords['occupation']['y'], $coords['occupation']['maxWidth'], strtoupper($app->occupation ?? ''));
    $writeFitText($coords['company_agency']['x'], $coords['company_agency']['y'], $coords['company_agency']['maxWidth'], strtoupper($app->company_agency ?? ''));
    $writeFitText($coords['educational_attainment']['x'], $coords['educational_attainment']['y'], $coords['educational_attainment']['maxWidth'], strtoupper($app->educational_attainment ?? ''));

    // === FAMILY COMPOSITION ===
    $family = json_decode($app->family, true);
    if (!is_array($family)) $family = [];

    if (!empty($family)) {
        $pdf->SetFont('Arial', '', 9);
        $startY = $coords['family_composition']['y'];
        $lineHeight = 5;
        $cols = [
            'name' => 25, 'relationship' => 55, 'age' => 83, 'dob' => 93,
            'civil_status' => 113, 'education' => 137, 'occupation' => 160, 'income' => 190,
        ];

        foreach ($family as $i => $member) {
            $rowY = $startY + ($i * $lineHeight);
            $pdf->SetXY($cols['name'], $rowY); $pdf->Write($lineHeight, strtoupper($member['name'] ?? ''));
            $pdf->SetXY($cols['relationship'], $rowY); $pdf->Write($lineHeight, strtoupper($member['relationship'] ?? ''));
            $pdf->SetXY($cols['age'], $rowY); $pdf->Write($lineHeight, $member['age'] ?? '');
            $pdf->SetXY($cols['dob'], $rowY); $pdf->Write($lineHeight, $member['birth_date'] ?? '');
            $pdf->SetXY($cols['civil_status'], $rowY); $pdf->Write($lineHeight, strtoupper($member['civil_status'] ?? ''));
            $pdf->SetXY($cols['occupation'], $rowY); $pdf->Write($lineHeight, strtoupper($member['occupation'] ?? ''));
            $pdf->SetXY($cols['income'], $rowY); $pdf->Write($lineHeight, strtoupper($member['monthly_income'] ?? ''));
            $pdf->SetXY($cols['education'], $rowY); $pdf->Write($lineHeight, strtoupper($member['educational_attainment'] ?? ''));
        }
    }

    // === SIGNATURE NAME ===
    $pdf->SetXY($coords['signature']['x'], $coords['signature']['y']);
    $pdf->Write(5, strtoupper("{$app->first_name} " . ($app->middle_name ?? '') . " {$app->last_name}"));

// === OUTPUT PDF FOR DOWNLOAD ===
$filename = 'SoloParent_' . $app->reference_no . '.pdf';
return response($pdf->Output('D', $filename)) // <-- 'D' forces download
    ->header('Content-Type', 'application/pdf');

}



public function viewPdf($id)
{
    $app = SoloParentApplication::where('application_id', $id)
        ->where('user_id', Auth::id())
        ->firstOrFail();

    $pdfFile = public_path('form/SOLO PARENT APPLICATION.pdf');
    if (!file_exists($pdfFile)) abort(404, 'Template PDF not found.');

    $pdf = new Fpdi();
    $pdf->AddPage();
    $pdf->setSourceFile($pdfFile);
    $template = $pdf->importPage(1);
    $pdf->useTemplate($template);

    $pdf->SetFont('Arial', '', 10);
    $check = fn($condition) => $condition ? $pdf->Write(5, 'X') : null;

    // === HELPER: shrink text if too long for single line fields ===
    $writeFitText = function($x, $y, $maxWidth, $text, $initialFont = 10) use ($pdf) {
        $pdf->SetXY($x, $y);
        $fontSize = $initialFont;
        $pdf->SetFont('Arial', '', $fontSize);
        while ($pdf->GetStringWidth($text) > $maxWidth && $fontSize > 5) {
            $fontSize -= 0.5;
            $pdf->SetFont('Arial', '', $fontSize);
        }
        $pdf->Write(5, $text);
    };

    // === COORDINATES MAP ===
    $coords = [
        'reference_no' => ['x' => 38, 'y' => 25],
        'full_name' => ['x' => 38, 'y' => 44],

        'age' => ['x' => 123, 'y' => 44],
        'sex' => ['x' => 167, 'y' => 44],

        'birth_date' => ['x' => 39, 'y' => 49],
        'place_of_birth' => ['x' => 139, 'y' => 49, 'maxWidth' => 50],
        'address' => ['x' => 32, 'y' => 54, 'width' => 160, 'line_height' => 5],

        'pantawid_yes' => ['x' => 20, 'y' => 87],
        'pantawid_no' => ['x' => 41, 'y' => 87],
        'indigenous_yes' => ['x' => 74, 'y' => 87],
        'indigenous_no' => ['x' => 94, 'y' => 87],
        'lgbtq_yes' => ['x' => 119, 'y' => 87],
        'lgbtq_no' => ['x' => 140, 'y' => 87],
        'pwd_yes' => ['x' => 165, 'y' => 87],
        'pwd_no' => ['x' => 186, 'y' => 87],

        'employed' => ['x' => 57, 'y' => 68],
        'not_employed' => ['x' => 146, 'y' => 73],
        'self_employed' => ['x' => 99, 'y' => 74],
        'civil_status_text' => ['x' => 135, 'y' => 59, 'maxWidth' => 40],

        'category_check' => ['x' => [20, 45, 70, 95], 'y' => 160],
        'category_text' => ['x' => 53, 'y' => 253, 'width' => 170, 'line_height' => 5, 'maxWidth' => 170],

        'circumstances' => ['x' => 20, 'y' => 154, 'width' => 170, 'line_height' => 5],
        'needs' => ['x' => 20, 'y' => 167, 'width' => 170, 'line_height' => 5],

        'emergency_name' => ['x' => 26, 'y' => 186],
        'emergency_relationship' => ['x' => 133, 'y' => 186],
        'emergency_contact' => ['x' => 144, 'y' => 191],
        'emergency_address' => ['x' => 30, 'y' => 191],

        'contact_number' => ['x' => 49, 'y' => 78, 'maxWidth' => 50],
        'email' => ['x' => 141, 'y' => 78, 'maxWidth' => 50],
        'religion' => ['x' => 130, 'y' => 64, 'maxWidth' => 50],
        'monthly_income' => ['x' => 144, 'y' => 68, 'maxWidth' => 50],
        'occupation' => ['x' => 36, 'y' => 64, 'maxWidth' => 50],
        'company_agency' => ['x' => 49, 'y' => 68, 'maxWidth' => 50],
        'educational_attainment' => ['x' => 58, 'y' => 59, 'maxWidth' => 50],

        'family_composition' => ['x' => 16, 'y' => 111],
        'signature' => ['x' => 48, 'y' => 209],

    ];

    // === NORMALIZE EMPLOYMENT STATUS ===
    $employment = strtolower(trim($app->employment_status));
    $employmentMap = [
        'employed' => 'employed',
        'self-employed' => 'self-employed',
        'self employed' => 'self-employed',
        'not employed' => 'not-employed',
    ];
    $employment = $employmentMap[$employment] ?? '';
    $app->employment_status = $employment;

    // === PERSONAL INFO ===
    $pdf->SetXY($coords['reference_no']['x'], $coords['reference_no']['y']);
    $pdf->Write(5, $app->reference_no);

    $pdf->SetXY($coords['full_name']['x'], $coords['full_name']['y']);
    $pdf->Write(5, strtoupper("{$app->first_name} " . ($app->middle_name ?? '') . " {$app->last_name}"));

    $pdf->SetXY($coords['age']['x'], $coords['age']['y']);
    $pdf->Write(5, $app->age);

    $pdf->SetXY($coords['sex']['x'], $coords['sex']['y']);
    $pdf->Write(5, strtoupper($app->sex));


    $pdf->SetXY($coords['birth_date']['x'], $coords['birth_date']['y']);
    $pdf->Write(5, $app->birth_date);

    // --- PLACE OF BIRTH (auto-shrink)
    $writeFitText($coords['place_of_birth']['x'], $coords['place_of_birth']['y'], $coords['place_of_birth']['maxWidth'], strtoupper($app->place_of_birth));

    $pdf->SetXY($coords['address']['x'], $coords['address']['y']);
    $pdf->MultiCell($coords['address']['width'], $coords['address']['line_height'], strtoupper($app->address));

    // === SOCIAL INDICATORS ===
    $pdf->SetXY($coords['pantawid_yes']['x'], $coords['pantawid_yes']['y']); $check(strtolower($app->pantawid) === 'yes');
    $pdf->SetXY($coords['pantawid_no']['x'], $coords['pantawid_no']['y']); $check(strtolower($app->pantawid) === 'no');
    $pdf->SetXY($coords['indigenous_yes']['x'], $coords['indigenous_yes']['y']); $check(strtolower($app->indigenous_person) === 'yes');
    $pdf->SetXY($coords['indigenous_no']['x'], $coords['indigenous_no']['y']); $check(strtolower($app->indigenous_person) === 'no');
    $pdf->SetXY($coords['lgbtq_yes']['x'], $coords['lgbtq_yes']['y']); $check(strtolower($app->lgbtq) === 'yes');
    $pdf->SetXY($coords['lgbtq_no']['x'], $coords['lgbtq_no']['y']); $check(strtolower($app->lgbtq) === 'no');
    $pdf->SetXY($coords['pwd_yes']['x'], $coords['pwd_yes']['y']); $check(strtolower($app->pwd) === 'yes');
    $pdf->SetXY($coords['pwd_no']['x'], $coords['pwd_no']['y']); $check(strtolower($app->pwd) === 'no');

    // === EMPLOYMENT STATUS ===
    $pdf->SetXY($coords['employed']['x'], $coords['employed']['y']); $check($employment === 'employed');
    $pdf->SetXY($coords['self_employed']['x'], $coords['self_employed']['y']); $check($employment === 'self-employed');
    $pdf->SetXY($coords['not_employed']['x'], $coords['not_employed']['y']); $check($employment === 'not-employed');

    // === CIVIL STATUS (auto-shrink) ===
    $writeFitText($coords['civil_status_text']['x'], $coords['civil_status_text']['y'], $coords['civil_status_text']['maxWidth'], strtoupper($app->civil_status));

    // === SOLO PARENT CATEGORY CHECKBOXES + TEXT (auto-shrink) ===
    $categoryFields = [$app->pantawid, $app->indigenous_person, $app->lgbtq, $app->pwd];
    foreach ($categoryFields as $i => $val) {
        $pdf->SetXY($coords['category_check']['x'][$i], $coords['category_check']['y']);
        $check(strtolower($val) === 'yes');
    }
    $writeFitText($coords['category_text']['x'], $coords['category_text']['y'], $coords['category_text']['maxWidth'], strtoupper($app->category));

    // === CIRCUMSTANCES & NEEDS ===
    $pdf->SetXY($coords['circumstances']['x'], $coords['circumstances']['y']);
    $pdf->MultiCell($coords['circumstances']['width'], $coords['circumstances']['line_height'], strtoupper($app->solo_parent_reason));

    $pdf->SetXY($coords['needs']['x'], $coords['needs']['y']);
    $pdf->MultiCell($coords['needs']['width'], $coords['needs']['line_height'], strtoupper($app->solo_parent_needs));

    // === EMERGENCY CONTACT ===
    $pdf->SetXY($coords['emergency_name']['x'], $coords['emergency_name']['y']); $pdf->Write(5, strtoupper($app->emergency_name));
    $pdf->SetXY($coords['emergency_relationship']['x'], $coords['emergency_relationship']['y']); $pdf->Write(5, strtoupper($app->emergency_relationship));
    $pdf->SetXY($coords['emergency_contact']['x'], $coords['emergency_contact']['y']); $pdf->Write(5, strtoupper($app->emergency_contact));
    $pdf->SetXY($coords['emergency_address']['x'], $coords['emergency_address']['y']); $pdf->Write(5, strtoupper($app->emergency_address));

    // === CONTACT, EMAIL, RELIGION, INCOME, OCCUPATION, COMPANY, EDUCATION (auto-shrink) ===
    $writeFitText($coords['contact_number']['x'], $coords['contact_number']['y'], $coords['contact_number']['maxWidth'], strtoupper($app->contact_number ?? ''));
    $writeFitText($coords['email']['x'], $coords['email']['y'], $coords['email']['maxWidth'], strtolower($app->email ?? ''));
    $writeFitText($coords['religion']['x'], $coords['religion']['y'], $coords['religion']['maxWidth'], strtoupper($app->religion ?? ''));
    $writeFitText($coords['monthly_income']['x'], $coords['monthly_income']['y'], $coords['monthly_income']['maxWidth'], strtoupper($app->monthly_income ?? ''));
    $writeFitText($coords['occupation']['x'], $coords['occupation']['y'], $coords['occupation']['maxWidth'], strtoupper($app->occupation ?? ''));
    $writeFitText($coords['company_agency']['x'], $coords['company_agency']['y'], $coords['company_agency']['maxWidth'], strtoupper($app->company_agency ?? ''));
    $writeFitText($coords['educational_attainment']['x'], $coords['educational_attainment']['y'], $coords['educational_attainment']['maxWidth'], strtoupper($app->educational_attainment ?? ''));

    // === FAMILY COMPOSITION ===
    $family = json_decode($app->family, true);
    if (!is_array($family)) $family = [];

    if (!empty($family)) {
        $pdf->SetFont('Arial', '', 9);
        $startY = $coords['family_composition']['y'];
        $lineHeight = 5;
        $cols = [
            'name' => 25, 'relationship' => 55, 'age' => 83, 'dob' => 93,
            'civil_status' => 113, 'education' => 137, 'occupation' => 160, 'income' => 190,
        ];

        foreach ($family as $i => $member) {
            $rowY = $startY + ($i * $lineHeight);
            $pdf->SetXY($cols['name'], $rowY); $pdf->Write($lineHeight, strtoupper($member['name'] ?? ''));
            $pdf->SetXY($cols['relationship'], $rowY); $pdf->Write($lineHeight, strtoupper($member['relationship'] ?? ''));
            $pdf->SetXY($cols['age'], $rowY); $pdf->Write($lineHeight, $member['age'] ?? '');
            $pdf->SetXY($cols['dob'], $rowY); $pdf->Write($lineHeight, $member['birth_date'] ?? '');
            $pdf->SetXY($cols['civil_status'], $rowY); $pdf->Write($lineHeight, strtoupper($member['civil_status'] ?? ''));
            $pdf->SetXY($cols['occupation'], $rowY); $pdf->Write($lineHeight, strtoupper($member['occupation'] ?? ''));
            $pdf->SetXY($cols['income'], $rowY); $pdf->Write($lineHeight, strtoupper($member['monthly_income'] ?? ''));
            $pdf->SetXY($cols['education'], $rowY); $pdf->Write($lineHeight, strtoupper($member['educational_attainment'] ?? ''));
        }
    }

    $pdf->SetXY($coords['signature']['x'], $coords['signature']['y']);
    $pdf->Write(5, strtoupper("{$app->first_name} " . ($app->middle_name ?? '') . " {$app->last_name}"));


    // === OUTPUT PDF ===
    $filename = 'SoloParent_' . $app->reference_no . '.pdf';
    return response($pdf->Output('I', $filename))
        ->header('Content-Type', 'application/pdf');
}






}
