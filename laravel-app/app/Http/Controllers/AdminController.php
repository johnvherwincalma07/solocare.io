<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\SoloParentApplication;
use App\Models\ScheduledSubmission;
use App\Models\HomeVisit;
use App\Models\ReadyToProcess;
use App\Models\SoloParentBeneficiary;
use App\Models\BeneficiaryBenefit;
use App\Models\AuditLog;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Announcement;
use App\Models\Event;
use App\Models\Attendance;
use App\Models\PayoutSchedule;
use App\Models\Barangay;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class AdminController extends Controller
{
    
 
    public function index(){
    $admin = Auth::user();

    // --- USER STATS ---
    $totalUsers = User::count();
    $adminUsers = User::where('role', 'Admin')->count();
    $activeUsers = User::where('status', 'Active')->count();
    $inactiveUsers = User::where('status', 'Inactive')->count();

    // --- APPLICATIONS ---
    $applications = SoloParentApplication::latest()->get();
    $recentApplications = $applications->take(5);
    $totalApplications = $applications->count();
    $approvedApplications = $applications->where('status', 'Approved')->count();
    $pendingApplications = $applications->where('status', 'Pending')->count();
    $rejectedApplications = $applications->where('status', 'Rejected')->count();

    // --- SCHEDULED SUBMISSIONS ---
    $scheduledSubmissions = ScheduledSubmission::latest()->get();

    // --- HOME VISITS ---
    $homeVisits = HomeVisit::latest()->get();
    $totalHomeVisits = $homeVisits->count();
    $pendingCount = $homeVisits->where('visit_status', 'Pending')->count();
    $scheduledCount = $homeVisits->where('visit_status', 'Scheduled')->count();
    $completedCount = $homeVisits->where('visit_status', 'Completed')->count();

    // --- READY TO PROCESS ---
    $readyToProcess = ReadyToProcess::latest()->get();


    // --- BARANGAYS ---
    $barangays = [
        "Alingaro","Arnaldo","Bacao I","Bacao II","Bagumbayan","Biclatan",
        "Buenavista I","Buenavista II","Buenavista III","Corregidor","Dulong Bayan",
        "Governor Ferrer","Javalera","Manggahan","Navarro","Panungyanan",
        "Pasong Camachile I","Pasong Camachile II","Pasong Kawayan I","Pasong Kawayan II",
        "Pinagtipunan","Prinza","Sampalucan","San Francisco","San Gabriel",
        "San Juan I","San Juan II","Santa Clara","Santiago","Tapia","Tejero","Vibora", "1897"
    ];

// ================================
// SOLO PARENT BENEFICIARIES (TEJERO ONLY)
// ================================
$beneficiaries = SoloParentBeneficiary::where('barangay', 'Tejero')
    ->orderBy('created_at', 'desc')
    ->get();

// Total solo parents (Tejero)
$beneficiaryTotal = $beneficiaries->count();

// Fixed values since only Tejero is shown
$highestDensityBarangay = 'Tejero';

// Barangays (locked to Tejero)
$barangays = ['Tejero'];

// Barangay counts (Tejero only)
$barangayCounts = collect([
    'Tejero' => $beneficiaryTotal
]);


    // Highest density barangay
    $highestDensityBarangay = $beneficiaries
        ->groupBy('barangay')
        ->map(fn($group) => $group->count())
        ->sortDesc()
        ->keys()
        ->first() ?? 'N/A';

    // --- CHAT ---
    $adminId = $admin->id;

// Get all users who have exchanged at least one message with anyone (admin will filter later in frontend if needed)
$userIdsWithMessages = Message::selectRaw('sender_id as user_id')
    ->union(Message::selectRaw('receiver_id as user_id'))
    ->pluck('user_id')
    ->unique();

$users = User::where('role', 'user')
    ->whereIn('id', $userIdsWithMessages)
    ->get();

foreach ($users as $user) {
    $lastMessage = Message::where(function($q) use ($user){
        $q->where('sender_id', $user->id)
          ->orWhere('receiver_id', $user->id);
    })->latest()->first();

    $user->last_message = $lastMessage?->message ?? '';
    $user->last_message_time = $lastMessage?->created_at?->diffForHumans() ?? '';
}



    $newChatCount = Message::where('receiver_id', $admin->id)
        ->where('is_read', false)->count();

    $recentChats = Message::with('sender')
        ->where('receiver_id', $admin->id)
        ->where('is_read', false)
        ->latest()
        ->take(5)
        ->get();

    // --- NOTIFICATIONS ---
    $newApplicationsCount = $pendingApplications;
    $totalNotifications = $newApplicationsCount + $newChatCount;

        $categories = [
        "A1. Birth of a child as a consequences of Rape",
        "A2. Widow/Widower",
        "A3. Spouse of person deprived of Liberty (PDL)",
        "A4. Spouse of person with Disability (PWD)",
        "A5. Due to de facto separation",
        "A6. Due to nullity of marriage",
        "A7. Abandoned",
        "B. Spouse of the OFW/Relative of the OFW",
        "C. Unmarried mother/father who keeps and rears his/her child/children",
        "D. Legal guardian, adoptive or foster parents",
        "E. Any relative within the fourth (4th) civil degree",
        "F. Pregnant woman who provides sole parental care and support to her unborn child or children",
    ];

    $categoryCounts = [];

    foreach ($categories as $cat) {
        $categoryCounts[$cat] = SoloParentBeneficiary::where('barangay', 'Tejero')
    ->where('category', $cat)
    ->count();

    }

        // Count solo parents per barangay
    $barangayCounts = SoloParentBeneficiary::select('barangay')
        ->selectRaw('COUNT(*) as total')
        ->groupBy('barangay')
        ->pluck('total', 'barangay');


        $applications = SoloParentApplication::all();

        $ageGroups = [
        '18-25' => 0,
        '26-35' => 0,
        '36-45' => 0,
        '46-60' => 0,
        '60+'   => 0,
    ];

    foreach ($applications as $app) {
        if (!$app->birthdate) continue;
        $age = Carbon::parse($app->birthdate)->age;

        if ($age >= 18 && $age <= 25) $ageGroups['18-25']++;
        else if ($age >= 26 && $age <= 35) $ageGroups['26-35']++;
        else if ($age >= 36 && $age <= 45) $ageGroups['36-45']++;
        else if ($age >= 46 && $age <= 60) $ageGroups['46-60']++;
        else if ($age > 60) $ageGroups['60+']++;
    }

    // ===========================
// RECENT SYSTEM ACTIVITY
// ===========================
$recentActivities = SoloParentApplication::orderBy('updated_at', 'desc')
    ->take(6)
    ->get()
    ->map(function ($app) {

        $name = trim($app->first_name . ' ' . $app->last_name);

        if ($app->application_stage === 'Verified Solo Parent') {
            return [
                'type' => 'approved',
                'text' => 'Application Approved',
                'name' => $name,
                'time' => $app->updated_at,
            ];
        }

        if ($app->status === 'Rejected') {
            return [
                'type' => 'rejected',
                'text' => 'Application Rejected',
                'name' => $name,
                'time' => $app->updated_at,
            ];
        }

        if ($app->application_stage === 'Home Visit') {
            return [
                'type' => 'review',
                'text' => 'Home Visit Ongoing',
                'name' => $name,
                'time' => $app->updated_at,
            ];
        }

        if ($app->application_stage === 'Scheduled for Submission') {
            return [
                'type' => 'created',
                'text' => 'Scheduled for Submission',
                'name' => $name,
                'time' => $app->updated_at,
            ];
        }

        return [
            'type' => 'created',
            'text' => 'New Application Submitted',
            'name' => $name,
            'time' => $app->created_at,
        ];
    });





    return view('admin.dashboard', compact(
        'totalUsers', 'adminUsers', 'activeUsers', 'inactiveUsers',
        'totalApplications', 'approvedApplications', 'pendingApplications', 'rejectedApplications',
        'applications', 'recentApplications', 'newApplicationsCount',
        'homeVisits', 'users', 'admin',
        'recentChats', 'newChatCount', 'totalNotifications',
        'readyToProcess', 'scheduledSubmissions',
        'beneficiaries',
        'barangays',
        'totalHomeVisits', 'pendingCount', 'scheduledCount', 'completedCount',
        'beneficiaryTotal',
        'highestDensityBarangay',
        'categoryCounts', 'categories', 'barangayCounts','ageGroups', 'recentActivities'

    ));
}

    // NEXT STEP
    private function logAction($action, $module = 'Applications', $status = 'Success'){
        AuditLog::create([
            'user'   => Auth::user()->first_name . ' ' . Auth::user()->last_name,
            'action' => $action,
            'module' => $module,
            'status' => $status,
        ]);
    }


    public function getApplicationFiles($id){
        $application = SoloParentApplication::find($id);
        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found.'
            ]);
        }

        $files = $application->files()->get()->map(function ($file) {
            return [
                'documents_id' => $file->documents_id,
                'application_id' => $file->application_id,
                'path' => $file->path,
                'url' => $file->url,  // using accessor in ApplicationFile
                'name' => $file->name, // using accessor if available
            ];
        });

        return response()->json(['success' => true, 'files' => $files]);
    }

    // NEXT STEP
    public function nextStep(Request $request){
    $request->validate([
        'application_id' => 'required|exists:solo_parent_applications,application_id',
    ]);

    $application = SoloParentApplication::findOrFail($request->application_id);

    // 🔒 Prevent moving rejected applications
    if (strtolower($application->status) === 'rejected') {
        return response()->json([
            'success' => false,
            'message' => 'Rejected applications cannot be moved to next stage.'
        ], 403);
    }

    $stage = $application->application_stage ?? '';

    switch ($stage) {
        case '':
        case 'Application':
        case 'Review Application':
            return $this->moveToScheduleInternal($application);

        case 'Scheduled for Submission':
            return $this->moveToHomeVisitInternal($application);

        case 'Home Visit':
            return $this->moveToReadyInternal($application);

        case 'Ready to Process':
            $application->application_stage = 'Verified Solo Parent';
            $application->status = 'Approved';
            $application->save();

            $this->logAction("Approved application #{$application->reference_no}");

            return response()->json([
                'success' => true,
                'message' => 'Application successfully verified and approved.',
                'application' => $application
            ]);

        default:
            return response()->json([
                'success' => false,
                'message' => 'Invalid application stage.'
            ], 400);
    }
}
 
    // REJECT APPLICATION
    public function rejectApplication(Request $request){
    $request->validate([
        'application_id' => 'required|exists:solo_parent_applications,application_id',
        'reason' => 'nullable|string|max:500',
    ]);

    $application = SoloParentApplication::where('application_id', $request->application_id)->first();

    if (!$application) {
        return response()->json([
            'success' => false,
            'message' => 'Application not found.'
        ]);
    }

    if (strtolower($application->status) === 'rejected') {
        return response()->json([
            'success' => false,
            'message' => 'Application already rejected.'
        ]);
    }

    $application->status = 'Rejected';
    $application->rejection_reason = $request->reason;
    $application->save();


    AuditLog::create([
        'user' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
        'action'=> "Rejected application #{$application->reference_no}" .
                   ($request->filled('reason') ? " (Reason: {$request->reason})" : ''),
        'module'=> 'Applications',
        'status'=> 'Success'
    ]);

    Notification::create([
        'user_id' => $application->user_id,
        'type'    => 'application_rejected',
        'message' => "Your Solo Parent application (#{$application->reference_no}) has been rejected." .
                     ($request->filled('reason') ? " Reason: {$request->reason}" : ''),
        'is_read' => false
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Application rejected successfully.',
        'status'  => 'rejected'
    ]);
}

    // MOVE TO SCHEDULE
    private function moveToScheduleInternal(SoloParentApplication $application){
    $application->application_stage = 'Scheduled for Submission';
    $application->status = 'Awaiting Documents';
    $application->save();

    $scheduled = ScheduledSubmission::updateOrCreate(
        ['application_id' => $application->application_id],
        [
            'reference_no' => $application->reference_no,
            'first_name' => $application->first_name,
            'last_name' => $application->last_name,
            'street' => $application->street,
            'barangay' => $application->barangay,
            'municipality' => $application->municipality,
            'status' => 'Pending',
            'scheduled_date' => null,
            'scheduled_time' => null,
            'category' => $application->category,
        ]
    );

    $this->logAction("Moved application #{$application->reference_no} to Scheduled for Submission");

    return response()->json([
        'success' => true,
        'message' => 'Moved to Scheduled for Submission',
        'scheduled' => $scheduled
    ]);
}


    public function saveSchedule(Request $request){
    // Validate input
    $request->validate([
        'schedule_req_id' => 'required|exists:scheduled_submissions,schedule_req_id',
        'scheduled_date' => 'required|date',
        'scheduled_time' => 'required',
    ]);

    // Find the record
    $schedule = ScheduledSubmission::findOrFail($request->schedule_req_id);

    // Update schedule
    $schedule->scheduled_date = $request->scheduled_date;
    $schedule->scheduled_time = $request->scheduled_time;
    $schedule->status = 'Scheduled';
    $schedule->save();

    return response()->json([
        'success' => true,
        'message' => 'Schedule saved successfully!',
        'scheduled' => $schedule
    ]);
}

    // MOVE TO HOME VISIT INTERNAL
    private function moveToHomeVisitInternal(SoloParentApplication $application){
        $application->application_stage = 'Home Visit';
        $application->status = 'Awaiting Home Visit';
        $application->save();
    
        $homeVisit = HomeVisit::updateOrCreate(
            ['application_id' => $application->application_id],
            [
                'reference_no' => $application->reference_no,
                'first_name' => $application->first_name,
                'last_name' => $application->last_name,
                'street' => $application->street,
                'barangay' => $application->barangay,
                'municipality' => $application->municipality,
                'visit_status' => 'Pending',
                'visit_date' => null,
                'visit_time' => null,
                'category' => $application->category,
            ]
        );
    
        $this->logAction("Moved application #{$application->reference_no} to Home Visit");
    
        return response()->json([
            'success' => true,
            'message' => 'Moved to Home Visit',
            'homeVisit' => $homeVisit
        ]);
    }
    
public function scheduleHomeVisit(Request $request)
{
    $visitId = $request->input('visit_id');
    $date = $request->input('visit_date');
    $time = $request->input('visit_time');

    $visit = HomeVisit::find($visitId);
    if (!$visit) {
        return response()->json(['success' => false, 'message' => 'Home visit not found'], 404);
    }

    $visit->visit_date = $date;
    $visit->visit_time = $time;
    $visit->visit_status = 'Scheduled';
    $visit->save();

    return response()->json(['success' => true, 'message' => 'Home visit scheduled successfully']);
}


    // MOVE TO READY INTERNAL
    private function moveToReadyInternal(SoloParentApplication $application){
    $application->application_stage = 'Ready to Process';
    $application->status = 'Completed';
    $application->save();

    ReadyToProcess::updateOrCreate(
        ['application_id' => $application->application_id],
        [
            'reference_no' => $application->reference_no,
            'first_name' => $application->first_name,
            'last_name' => $application->last_name,
            'street' => $application->street,
            'barangay' => $application->barangay,
            'municipality' => $application->municipality,
            'category' => $application->category,
            'status' => 'Ready',
        ]
    );

    $this->logAction("Moved application #{$application->reference_no} to Ready to Process");

    return response()->json([
        'success' => true,
        'message' => 'Application is now Ready to Process'
    ]);
}

public function moveToBeneficiary(Request $request)
{
    $request->validate([
        'ready_process_id' => 'required|exists:ready_to_process,ready_process_id',
    ]);

    // Wrap everything in a transaction to make it fast and safe
    $beneficiary = DB::transaction(function () use ($request) {

        // ✅ Load only what we need (no relationships unless necessary)
        $ready = ReadyToProcess::findOrFail($request->ready_process_id);

        $application = SoloParentApplication::find($ready->application_id);
        if (!$application || strtolower($application->status) === 'rejected') {
            abort(403, 'Rejected applications cannot be moved.');
        }

        // ✅ Update application directly
        $application->update([
            'application_stage' => 'Verified Solo Parent',
            'status'            => 'Approved',
            'is_beneficiary'    => true,
        ]);

$barangayName = $ready->barangay?->name; // get name from relation

if (!$barangayName && $ready->barangay_id) {
    // fallback: get name directly from barangay_id
    $barangay = Barangay::find($ready->barangay_id);
    $barangayName = $barangay->name ?? 'Unknown';
}

$beneficiary = SoloParentBeneficiary::create([
    'application_id'    => $ready->application_id,
    'first_name'        => $ready->first_name,
    'last_name'         => $ready->last_name,
    'barangay_id'       => $ready->barangay_id,
    'barangay'          => $barangayName ?? 'Unknown',
    'street'            => $ready->street ?? '-',
    'municipality'      => $ready->municipality ?? '-',
    'date_added'        => now(),
    'assistance_status' => 'Approved',
    'category'          => $ready->category ?? '-',
]);
        $beneficiary = SoloParentBeneficiary::create([
            'application_id'    => $ready->application_id,
            'first_name'        => $ready->first_name,
            'last_name'         => $ready->last_name,
            'barangay_id'       => $ready->barangay_id,
            'barangay'          => $ready->barangay?->name ?? 'Unknown',
            'street'            => $ready->street ?? '-',
            'municipality'      => $ready->municipality ?? '-',
            'date_added'        => now(),
            'assistance_status' => 'Approved',
            'category'          => $ready->category ?? '-',
        ]);

        // ✅ Delete ready record
        $ready->delete();

        return $beneficiary;
    });

    // Only return minimal JSON to frontend
    return response()->json([
        'success' => true,
        'message' => 'Successfully moved to beneficiaries.',
        'beneficiary' => [
            'beneficiary_id'    => $beneficiary->id,
            'first_name'        => $beneficiary->first_name,
            'last_name'         => $beneficiary->last_name,
            'barangay'          => $beneficiary->barangay,
            'barangay_id'       => $beneficiary->barangay_id,
            'street'            => $beneficiary->street,
            'municipality'      => $beneficiary->municipality,
            'date_added'        => $beneficiary->date_added,
            'assistance_status' => $beneficiary->assistance_status,
            'category'          => $beneficiary->category,
        ],
    ]);
}








    public function showBeneficiary($id){
    $beneficiary = SoloParentBeneficiary::with('benefits')->find($id);

    if (!$beneficiary) {
        return response()->json(['success' => false, 'message' => 'Beneficiary not found.']);
    }

    // Fallbacks for empty fields
    $firstName = $beneficiary->first_name ?? '-';
    $lastName = $beneficiary->last_name ?? '-';
    $barangay = $beneficiary->barangay ?? '-';
    $street = $beneficiary->street ?? '';
    $municipality = $beneficiary->municipality ?? '';
    $assistanceStatus = $beneficiary->assistance_status ?? '-';
    $category = $beneficiary->category ?? '-';
    $dateAdded = $beneficiary->date_added ? $beneficiary->date_added->format('Y-m-d H:i') : '-';

    // Combine street + municipality for address
    $address = ($street || $municipality) ? trim($street . ($street && $municipality ? ', ' : '') . $municipality) : '-';

    // Default benefits
    $defaultBenefits = [
        "1000 monthly cash subsidy",
        "PhilHealth Coverage",
        "10% + VAT exemption on baby needs",
        "Scholarships for children",
        "Priority in jobs, livelihood, and housing",
        "7 days parental leave + flexible work"
    ];

    // Category-specific recommended benefits
    $categoryBenefits = [
        "A1. Birth of a child as a consequences of Rape" => ["Counseling", "Medical Support", "Scholarship"],
        "A2. Widow/Widower" => ["Livelihood Program", "Housing Assistance"],
        "A3. Spouse of person deprived of Liberty (PDL)" => ["Livelihood Program", "Legal Aid"],
        "A4. Spouse of person with Disability (PWD)" => ["Disability Support", "Medical Support"],
        "A5. Due to de facto separation" => ["Counseling", "Livelihood Program"],
        "A6. Due to nullity of marriage" => ["Counseling", "Legal Aid"],
        "A7. Abandoned" => ["Housing Assistance", "Counseling"],
        "B. Spouse of the OFW/Relative of the OFW" => ["Livelihood Program", "Scholarship"],
        "C. Unmarried mother/father who keeps and rears his/her child/children" => ["Childcare Support", "Scholarship"],
        "D. Legal guardian, adoptive or foster parents" => ["Childcare Support", "School Supplies"],
        "E. Any relative within the fourth (4th) civil degree" => ["Counseling", "Childcare Support"],
        "F. Pregnant woman who provides sole parental care and support to her unborn child or children" => ["Prenatal Care", "Counseling"]
    ];

    $recommendedBenefits = $categoryBenefits[$category] ?? [];

    // Benefits from DB
    $savedBenefits = $beneficiary->benefits->pluck('benefit_name')->toArray();

    // Combine all benefits
    $allBenefits = array_unique(array_merge($defaultBenefits, $recommendedBenefits, $savedBenefits));

    return response()->json([
        'success' => true,
        'beneficiary' => [
            'beneficiary_id' => $beneficiary->beneficiary_id,
            'application_id' => $beneficiary->application_id,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'address' => $address,
            'barangay' => $barangay,
            'assistance_status' => $assistanceStatus,
            'category' => $category,
            'benefits' => $allBenefits,
            'date_added' => $dateAdded,
        ]
    ]);
}

public function getBeneficiaries(){
    $beneficiaries = SoloParentBeneficiary::with('benefits')
        ->where('barangay', 'Tejero')
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($b){
            return [
                'beneficiary_id' => $b->beneficiary_id,
                'application_id' => $b->application_id,
                'first_name' => $b->first_name,
                'last_name' => $b->last_name,
                'barangay' => $b->barangay,
                'street' => $b->street,
                'municipality' => $b->municipality,
                'assistance_status' => $b->assistance_status,
                'category' => $b->category,
                'selected_benefits' => is_string($b->selected_benefits)
                    ? json_decode($b->selected_benefits)
                    : ($b->selected_benefits ?? []),
                'date_added' => $b->date_added
                    ? $b->date_added->format('Y-m-d H:i')
                    : null,
            ];
        });

    return response()->json([
        'beneficiaries' => $beneficiaries,
        'barangays' => ['Tejero']
    ]);
}


    public function saveBenefits(Request $request){
    $request->validate([
        'beneficiary_id' => 'required|exists:solo_parent_beneficiaries,beneficiary_id',
        'selected' => 'nullable|array', // additional benefits selected manually
    ]);

    $beneficiaryId = $request->beneficiary_id;
    $selectedBenefits = $request->selected ?? [];

    $beneficiary = SoloParentBeneficiary::findOrFail($beneficiaryId);
    $category = $beneficiary->category;

    // Default benefits
    $defaultBenefits = [
        "1000 monthly cash subsidy",
        "PhilHealth Coverage",
        "10% + VAT exemption on baby needs",
        "Scholarships for children",
        "Priority in jobs, livelihood, and housing",
        "7 days parental leave + flexible work"
    ];

    // Category-specific recommended benefits
    $categoryBenefits = [
        "A1. Birth of a child as a consequences of Rape" => ["Counseling", "Medical Support", "Scholarship"],
        "A2. Widow/Widower" => ["Livelihood Program", "Housing Assistance"],
        "A3. Spouse of person deprived of Liberty (PDL)" => ["Livelihood Program", "Legal Aid"],
        "A4. Spouse of person with Disability (PWD)" => ["Disability Support", "Medical Support"],
        "A5. Due to de facto separation" => ["Counseling", "Livelihood Program"],
        "A6. Due to nullity of marriage" => ["Counseling", "Legal Aid"],
        "A7. Abandoned" => ["Housing Assistance", "Counseling"],
        "B. Spouse of the OFW/Relative of the OFW" => ["Livelihood Program", "Scholarship"],
        "C. Unmarried mother/father who keeps and rears his/her child/children" => ["Childcare Support", "Scholarship"],
        "D. Legal guardian, adoptive or foster parents" => ["Childcare Support", "School Supplies"],
        "E. Any relative within the fourth (4th) civil degree" => ["Counseling", "Childcare Support"],
        "F. Pregnant woman who provides sole parental care and support to her unborn child or children" => ["Prenatal Care", "Counseling"]
    ];

    $recommended = $categoryBenefits[$category] ?? [];

    // Combine all benefits: default + category recommended + manually selected
    $allBenefits = array_unique(array_merge($defaultBenefits, $recommended, $selectedBenefits));

    // Remove old benefits
    BeneficiaryBenefit::where('beneficiary_id', $beneficiaryId)->delete();

    // Save each benefit
    foreach ($allBenefits as $benefitName) {
        BeneficiaryBenefit::create([
            'beneficiary_id' => $beneficiaryId,
            'benefit_name' => $benefitName,
            'date_given' => now()
        ]);
    }

    // Save selected benefits JSON in the main table
    $beneficiary->selected_benefits = json_encode($allBenefits);
    $beneficiary->save();

    return response()->json([
        'success' => true,
        'message' => 'Benefits saved successfully!',
        'benefits' => $allBenefits
    ]);
}

    // SOLO PARENT GIS
public function getSoloParentGIS(){
    $data = SoloParentApplication::where('is_beneficiary', true)
        ->where('barangay', 'Tejero')
        ->latest()
        ->get();

    $data->each(fn($a) => $a->full_name =
        trim(($a->first_name ?? '') . ' ' . ($a->last_name ?? ''))
    );

    return response()->json([
        'success' => true,
        'data' => $data
    ]);
}


    // READY TO PROCESS PAGE
    public function readyToProcess(){
        $ready = ReadyToProcess::latest()->get();
        return response()->json(['success' => true, 'data' => $ready]);
    }

    // ATTENDANCE
    public function getAttendance(){
        $attendances = Attendance::orderBy('activity_date', 'desc')->get();
        return response()->json(['success' => true, 'data' => $attendances]);
    }

    public function storeAttendance(Request $request){
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'activity_date' => 'required|date',
            'status' => 'required|string'
        ]);

        $attendance = Attendance::create($request->only('user_id', 'activity_date', 'status'));
        return response()->json(['success' => true, 'data' => $attendance]);
    }

    // Fetch messages between admin and selected user
    public function fetchMessages($userId){
        $adminId = Auth::id();

        $messages = Message::where(function($q) use ($adminId, $userId){
            $q->where('sender_id', $adminId)
              ->where('receiver_id', $userId);
        })->orWhere(function($q) use ($adminId, $userId){
            $q->where('sender_id', $userId)
              ->where('receiver_id', $adminId);
        })->orderBy('created_at', 'asc')->get();

        return response()->json($messages);
    }

    // Send a message
    public function sendMessage(Request $request){
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string'
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return response()->json($message);
    }

    // LOGOUT
    public function logout(){
        Auth::logout();
        return redirect()->route('home');
    }

    // Get all attendances (for admin dashboard)
    public function createEvent(Request $request){
    $request->validate([
        'name' => 'required|string|max:255',
        'type' => 'required|in:Seminar,Event,Meeting,Home Visit',
        'date' => 'required|date',
        'time' => 'required',
        'location' => 'required|string|max:255',
        'max_participants' => 'nullable|integer',
    ]);

    // 1️⃣ Save Event
    $event = Event::create([
        'name' => $request->name,
        'type' => $request->type,
        'date' => $request->date,
        'time' => $request->time,
        'location' => $request->location,
        'max_participants' => $request->max_participants ?? 0,
        'status' => 'Pending'
    ]);

    // 2️⃣ Automatically create Announcement for users
    $announcement = Announcement::create([
        'title' => "New {$event->type}: {$event->name}",
        'content' => "You can now pre-register for this {$event->type} happening on {$event->date} at {$event->time}, located at {$event->location}.",
        'link' => route('user.attendance.register', ['eventId' => $event->id]), // this is your user registration page
        'type' => $event->type,
        'event_id' => $event->id
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Event created and announcement sent successfully!',
        'event' => $event,
        'announcement' => $announcement
    ]);
}

    // Fetch all events
    public function fetch(){
        $events = Event::all(); // you can order by date if needed
        return response()->json(['events' => $events]);
    }

    public function savePayoutSchedule(Request $request){
    try {

        $barangay = $request->barangay;

        // ================================
        // Count total solo parents in barangay
        // ================================
        $totalEligible = SoloParentBeneficiary::where('barangay', $barangay)->count();

        // ================================
        // Count who already received
        // adjust field name accordingly
        // e.g. status = 'received' or has_received = 1
        // ================================
        $totalReceived = SoloParentBeneficiary::where('barangay', $barangay)
            ->where('has_received', 1) // change if using status
            ->count();

        // ================================
        // Create schedule
        // ================================
        $payout = PayoutSchedule::create([
            'barangay'       => $request->barangay,
            'scheduled_date' => $request->scheduled_date,
            'scheduled_time' => $request->scheduled_time,
            'location'       => $request->location,
        ]);

        // ================================
        // Create announcement
        // ================================
        Announcement::create([
            'title'   => "Payout Scheduled - {$request->barangay}",
            'content' => "A payout is scheduled at {$request->location} on {$request->scheduled_date} at {$request->scheduled_time}.",
            'category'=> "payout",
            'status'  => "success",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payout schedule saved and announcement created!',
            'data' => [
                'payout' => $payout,
                'summary' => [
                    'received' => $totalReceived,
                    'total'    => $totalEligible,
                    'formatted' => "{$totalReceived}/{$totalEligible}"
                ]
            ]
        ]);

    } catch (\Throwable $e) {

        Log::error($e);

        return response()->json([
            'success' => false,
            'message' => 'Failed to save payout.'
        ]);
    }
}


    public function markBenefitReceived(Request $request){
    $request->validate([
        'beneficiaryId' => 'required|exists:solo_parent_beneficiaries,beneficiary_id',
        'benefitName' => 'required|string',
    ]);

    $beneficiaryId = $request->beneficiaryId;
    $benefitName = $request->benefitName;
    $timestamp = now();

    // Check if record already exists
    $existing = BeneficiaryBenefit::where('beneficiary_id', $beneficiaryId)
                ->where('benefit_name', $benefitName)
                ->first();

    if ($existing) {
        $existing->remarks = 'Received';
        $existing->date_given = $timestamp;
        $existing->save();
    } else {
        BeneficiaryBenefit::create([
            'beneficiary_id' => $beneficiaryId,
            'benefit_name' => $benefitName,
            'remarks' => 'Received',
            'date_given' => $timestamp,
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Benefit marked as received.',
        'date_given' => $timestamp->format('M d, Y H:i')
    ]);
}


    private function storePayoutAnnouncement($barangay, $date, $time, $location){
    return Announcement::create([
        'title' => "Payout Schedule for $barangay",
        'content' => "A payout will be held at $barangay on $date at $time. Location: $location. Please be present to claim your assistance.",
        'link' => null,
        'event_id' => null,
        'type' => 'payout',
        'status' => "success",
        'category' => $barangay,
    ]);
}

    public function destroy($id){
        $event = Event::find($id);

        if(!$event){
            return response()->json(['success' => false]);
        }

        $event->delete();

        return response()->json(['success' => true]);
    }


    public function soloParentDistribution(){
        // ===== SOLO PARENT CATEGORY TOTAL =====
        $categories = SoloParentBeneficiary::select(
                'category',
                DB::raw('COUNT(beneficiary_id) as total')
            )
            ->whereNotNull('category')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        // ===== PER BARANGAY TOTAL =====
        $barangays = SoloParentBeneficiary::select(
                'barangay',
                DB::raw('COUNT(beneficiary_id) as total')
            )
            ->whereNotNull('barangay')
            ->groupBy('barangay')
            ->orderByDesc('total')
            ->get();

        return response()->json([
            'categories' => [
                'labels' => $categories->pluck('category'),
                'data'   => $categories->pluck('total'),
            ],
            'barangays' => [
                'labels' => $barangays->pluck('barangay'),
                'data'   => $barangays->pluck('total'),
            ],
        ]);
    }

    public function monthlyPerformance(Request $request){
    // Get year from query, default to current year
    $year = $request->query('year', now()->year);

    // Initialize months array
    $months = [];
    $totals = [];

    // Build month labels Jan-Dec
    for ($i = 1; $i <= 12; $i++) {
        $months[] = Carbon::createFromDate($year, $i, 1)->format('M');
        $totals[] = 0;
    }

    // Get count of submitted applications per month
    $applications = SoloParentApplication::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
        ->whereYear('created_at', $year)
        ->groupByRaw('MONTH(created_at)')
        ->pluck('total', 'month'); // returns [month_number => total]

    // Fill totals array
    foreach ($applications as $monthNumber => $count) {
        $totals[$monthNumber - 1] = $count; // months array is 0-indexed
    }

    // Get approval status counts for the year
    $approval = SoloParentApplication::selectRaw("
        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
    ")->whereYear('created_at', $year)->first();

    return response()->json([
        'months' => $months,
        'totals' => $totals,
        'approval' => $approval,
    ]);
}

    public function getTejeroPayoutSchedule(){
    $barangay = 'Tejero';

    // Ensure payout row exists
    $schedule = PayoutSchedule::firstOrCreate(
        ['barangay' => $barangay],
        ['status' => 'pending']
    );

    $totalBeneficiaries = SoloParentBeneficiary::where('barangay', $barangay)->count();

    $received = SoloParentBeneficiary::where('barangay', $barangay)
        ->where('assistance_status', 'received')
        ->count();

    return response()->json([
        'id' => $schedule->id,
        'barangay' => $barangay,
        'total_beneficiaries' => $totalBeneficiaries,
        'scheduled_date' => $schedule->scheduled_date,
        'scheduled_time' => $schedule->scheduled_time,
        'location' => $schedule->location,
        'received' => $received,
        'total' => $totalBeneficiaries,
    ]);
}


    public function updatePayoutSchedule(Request $request, $id){
    $request->validate([
        'scheduled_date' => 'required|date',
        'scheduled_time' => 'required',
        'location' => 'required|string|max:255',
    ]);

    $schedule = PayoutSchedule::findOrFail($id);
    $schedule->scheduled_date = $request->scheduled_date;
    $schedule->scheduled_time = $request->scheduled_time;
    $schedule->location = $request->location;
    $schedule->save();

    return response()->json([
        'success' => true,
        'message' => 'Schedule updated successfully!',
        'schedule' => $schedule
    ]);
}

    public function tejeroOverview(){
    $barangay = 'Tejero';

    // Ensure schedule exists
    $schedule = PayoutSchedule::firstOrCreate(
        ['barangay' => $barangay],
        ['status' => 'pending']
    );

    $totalBeneficiaries = SoloParentBeneficiary::where('barangay', $barangay)->count();

    $receivedCount = SoloParentBeneficiary::where('barangay', $barangay)
        ->where('assistance_status', 'received')
        ->count();

    return response()->json([
        'barangay' => $barangay,
        'total_beneficiaries' => $totalBeneficiaries,
        'scheduled_date' => $schedule->scheduled_date,
        'scheduled_time' => $schedule->scheduled_time,
        'location' => $schedule->location,
        'received' => $receivedCount,
    ]);
}

    public function getTejeroTotal(){
    // Count all beneficiaries in Barangay Tejero
    $total = DB::table('solo_parent_beneficiaries')
                ->where('barangay', 'Tejero')
                ->count();

    return response()->json(['total' => $total]);
}


    public function getTejeroBeneficiaries(){
    $beneficiaries = SoloParentBeneficiary::with('benefits')
        ->where('barangay', 'Tejero')
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($b){
            return [
                'beneficiary_id' => $b->beneficiary_id,
                'application_id' => $b->application_id,
                'first_name' => $b->first_name,
                'last_name' => $b->last_name,
                'barangay' => $b->barangay,
                'street' => $b->street,
                'municipality' => $b->municipality,
                'assistance_status' => $b->assistance_status,
                'category' => $b->category,
                'selected_benefits' => is_string($b->selected_benefits) ? json_decode($b->selected_benefits) : ($b->selected_benefits ?? []),
                'date_added' => $b->date_added ? $b->date_added->format('Y-m-d H:i') : null,
            ];
        });

    return response()->json($beneficiaries);
}

    public function tejeroJson(){
        // Fetch all beneficiaries for Barangay Tejero
        $beneficiaries = SoloParentBeneficiary::where('barangay', 'Tejero')->get();

        // Return as JSON
        return response()->json($beneficiaries);
    }

}

