<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\About;
use App\Models\Article;
use App\Models\Gallery;
use App\Models\Faq;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\Announcement;
use App\Models\SoloParentApplication;
use App\Models\SoloParentBeneficiary;
use App\Models\UserActivity;
use App\Models\Message;
use App\Models\SystemSetting;
use App\Models\Barangay;
use App\Models\PayoutSchedule;
use App\Mail\NewUserCredentials;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;




class DashboardController extends Controller
{
    // ================================
    // SUPER ADMIN DASHBOARD
    // ================================
    public function index()
    {
        $about = About::first();
        $articles = Article::all();
        $gallery = Gallery::all();

            // --- SOLO PARENT APPLICATIONS ---
        $applications = SoloParentApplication::latest()->get();

        $totalApplications = $applications->count();
        $pendingApplications = $applications->where('status', 'Pending')->count();
        $approvedApplications = $applications->where('status', 'Approved')->count();
        $rejectedApplications = $applications->where('status', 'Rejected')->count();


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

            // --- BARANGAYS ---
        $barangays = [
            "Alingaro","Arnaldo","Bacao I","Bacao II","Bagumbayan","Biclatan",
            "Buenavista I","Buenavista II","Buenavista III","Corregidor","Dulong Bayan",
            "Governor Ferrer","Javalera","Manggahan","Navarro","Panungyanan",
            "Pasong Camachile I","Pasong Camachile II","Pasong Kawayan I","Pasong Kawayan II",
            "Pinagtipunan","Prinza","Sampalucan","San Francisco","San Gabriel",
            "San Juan I","San Juan II","Santa Clara","Santiago","Tapia","Tejero","Vibora"
        ];

            // Fetch all beneficiaries
        $beneficiaries = SoloParentBeneficiary::with('benefits')->get();

        // Total count
        $beneficiaryTotal = $beneficiaries->count();

        // Unique barangays
        $barangays = $beneficiaries->pluck('barangay')->unique()->filter()->values();

        // Highest density barangay
        $highestDensityBarangay = $beneficiaries
            ->groupBy('barangay')
            ->map(fn($group) => $group->count())
            ->sortDesc()
            ->keys()
            ->first() ?? 'None';

        // --- Fetch users for chat ---
        $users = User::where('role', 'admin')->get();

            // --- USER MANAGEMENT STAT CARDS ---
        $userStats = [
            'total'      => User::count(),
            'active'     => User::where('status', 'Active')->count(),
            'inactive'   => User::where('status', 'Inactive')->count(),
            'admins'     => User::where('role', 'admin')->count(),
        ];

        $system = SystemSetting::first();



        return view('super-admin.dashboard', compact('about', 'articles', 'gallery', 'categories',
        'totalApplications', 'pendingApplications', 'approvedApplications', 'rejectedApplications', 'barangays', 'applications',
        'beneficiaries',
        'beneficiaryTotal', 'barangays', 'highestDensityBarangay', 'users', 'userStats', 'system'
    ));
    }


    public function getDashboardStats()
    {
        $totalUsers = User::count();

        // If using database sessions table
        $activeSessions = DB::table('sessions')->count();

        return response()->json([
            'totalUsers' => $totalUsers,
            'activeSessions' => $activeSessions
        ]);
    }

    public function superUserManagement()
    {
        AuditLog::create([
            'user'   => Auth::user()->first_name . ' ' . Auth::user()->last_name,
            'action' => 'Viewed User Management',
            'module' => 'Users',
            'status' => 'Viewed',
        ]);

        return view('super-admin.dashboard');
    }


    public function superGetUsers()
    {
        AuditLog::create([
            'user'   => Auth::user()->first_name . ' ' . Auth::user()->last_name,
            'action' => 'Fetched users list',
            'module' => 'Users',
            'status' => 'Viewed',
        ]);

        // Fetch all users, sort Super Admins first
        $users = User::select(
    'id', 'first_name', 'middle_name', 'last_name', 'username', 'email', 
    'contact', 'avatar', 'street', 'barangay', 'municipality_city', 'province',
    'role', 'status', 'created_at'
)->orderByRaw("CASE WHEN role = 'super_admin' THEN 0 ELSE 1 END, last_name ASC")->get();

        // Add full_name for JS usage
        $users->each(fn($u) => $u->full_name = trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? '')));

        return response()->json($users);
    }

    public function superGetUserStats()
    {
        return response()->json([
            'total' => User::count(),
            'active' => User::where('status', 'Active')->count(),
            'inactive' => User::where('status', 'Inactive')->count(),
            'admins' => User::where('role', 'admin')->count()
        ]);
    }


    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|string|in:super_admin,admin,staff,user,solo_parent'
        ]);

        // Split fullname into last_name and first_name (assuming "Last First")
        $names = explode(' ', $request->fullname, 2);
        $user->last_name = $names[0] ?? '';
        $user->first_name = $names[1] ?? '';
        $user->email = $request->email;
        $user->role = $request->role;
        $user->save();

        AuditLog::create([
            'user'   => Auth::user()->first_name . ' ' . Auth::user()->last_name,
            'action' => "Updated user: {$user->first_name} {$user->last_name}",
            'module' => 'Users',
            'status' => 'Updated',
        ]);

        return response()->json(['message' => 'User updated successfully']);
    }


    public function deleteUser(User $user)
    {
        $name = $user->first_name . ' ' . $user->last_name;
        $user->delete();

        AuditLog::create([
            'user'   => Auth::user()->first_name . ' ' . Auth::user()->last_name,
            'action' => "Deleted user: {$name}",
            'module' => 'Users',
            'status' => 'Deleted',
        ]);

        return response()->json(['message' => 'User deleted successfully']);
    }

    public function deactivateUser(User $user)
    {
        // Prevent super admin from being deactivated
        if ($user->role === 'super_admin') {
            return response()->json(['message' => 'Cannot deactivate a super admin'], 403);
        }

        $user->status = 'Inactive';
        $user->save();

        AuditLog::create([
            'user'   => Auth::user()->first_name . ' ' . Auth::user()->last_name,
            'action' => "Deactivated user: {$user->first_name} {$user->last_name}",
            'module' => 'Users',
            'status' => 'Success',
        ]);

        return response()->json(['message' => 'User deactivated successfully']);
    }



    public function store(Request $request)
    {
        // 🔐 Ensure authenticated
        $currentUser = Auth::user();
        if (!$currentUser) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // ✅ VALIDATION FIRST
        $validated = $request->validate([
            'first_name'  => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name'   => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|confirmed|min:6',
            'role'        => 'required|in:user,admin,super_admin',
        ]);

        // 🔐 ROLE PERMISSION CHECK (AFTER VALIDATION)
        if (
            $currentUser->role === 'admin' &&
            in_array($validated['role'], ['admin', 'super_admin'])
        ) {
            throw ValidationException::withMessages([
                'role' => 'Admins can only create regular users.'
            ]);
        }

        // ✅ CLEAN USERNAME GENERATION
        $baseUsername =
            strtolower(
                substr(trim($validated['first_name']), 0, 1) .
                preg_replace('/\s+/', '', trim($validated['last_name']))
            );

        $username = $baseUsername;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter++;
        }

        // ✅ CREATE USER (PASSWORD AUTO-HASHED BY MODEL)
        $user = User::create([
            'first_name'  => $validated['first_name'],
            'middle_name' => $validated['middle_name'],
            'last_name'   => $validated['last_name'],
            'username'    => $username,
            'email'       => $validated['email'],
            'password'    => $validated['password'],
            'role'        => $validated['role'],
            'status'      => 'Active',
        ]);

        // 📧 SEND LOGIN CREDENTIALS EMAIL
        Mail::to($user->email)->send(
            new NewUserCredentials($user, $validated['password'])
        );

        // 📝 AUDIT LOG (MAKE SURE AuditLog::$fillable IS FIXED)
        AuditLog::create([
            'user'   => $currentUser->first_name . ' ' . $currentUser->last_name,
            'action' => "Added new user: {$user->username} ({$user->role})",
            'module' => 'Users',
            'status' => 'Success',
        ]);

        return response()->json([
            'success'  => true,
            'username' => $username
        ]);
    }


    public function updateSystem(Request $request)
    {
        $system = SystemSetting::first(); // assuming you have only one row

        $data = $request->only([
            'system_brand_name',
            'system_full_name',
            'system_description',
            'admin_email',
            'footer_text'
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $filename);
            $data['system_logo'] = $filename;
        }

        $system->update($data);

        return response()->json([
            'success' => true,
            'system' => [
                'brand_name' => $system->system_brand_name,
                'full_name' => $system->system_full_name,
                'footer_text' => $system->footer_text,
            ]
        ]);
    }




    public function updateAbout(Request $request)
    {
        $about = About::first() ?? new About();
        $about->content = $request->input('content');
        $about->save();

        return redirect()->back()->with('success', 'About section updated.');
    }

    public function updateQualified(Request $request)
    {
        $about = About::first() ?? new About();
        $about->content_qualified = $request->input('content_qualified');
        $about->save();

        return redirect()->back()->with('success', 'Card 2 (Qualified) updated.');
    }

    public function updateBenefits(Request $request)
    {
        $about = About::first() ?? new About();
        $about->content_benefits = $request->input('content_benefits');
        $about->save();

        return redirect()->back()->with('success', 'Card 3 (Benefits) updated.');
    }

    public function storeArticle(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string|max:500',
            'image' => 'nullable|image|max:2048',
        ]);

        $article = new Article();
        $article->title = $request->title;
        $article->excerpt = $request->excerpt;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('articles', 'public');
            $article->image = $path;
        }

        $article->save();

        return redirect()->back()->with('success', 'Article added successfully.');
    }

    public function updateArticle(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        $article->title = $request->title;
        $article->excerpt = $request->excerpt;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('articles', 'public');
            $article->image = $path;
        }

        $article->save();

        return redirect()->back()->with('success', 'Article updated successfully.');
    }

    public function destroyArticle($id)
    {
        $article = Article::findOrFail($id);
        $article->delete();

        return redirect()->back()->with('success', 'Article deleted successfully.');
    }

    public function storeGallery(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'required|image|max:2048',
        ]);

        $path = $request->file('image')->store('gallery', 'public');

        $gallery = new Gallery();
        $gallery->title = $request->title;
        $gallery->image_path = $path;
        $gallery->save();

        return redirect()->back()->with('success', 'Gallery image added.');
    }

    public function destroyGallery($id)
    {
        $gallery = Gallery::findOrFail($id);
        $gallery->delete();

        return redirect()->back()->with('success', 'Gallery image deleted.');
    }

    public function superGetAnnouncements()
    {
        $announcements = Announcement::orderBy('created_at', 'desc')->get([
            'id','title','content','link','event_id','type','status','category','created_at','updated_at'
        ]);

        return response()->json($announcements);
    }


    public function superGetMessages($userId)
    {
        $adminId = Auth::id();

        $messages = Message::where(function($q) use ($adminId, $userId){
            $q->where('sender_id', $adminId)->where('receiver_id', $userId);
        })->orWhere(function($q) use ($adminId, $userId){
            $q->where('sender_id', $userId)->where('receiver_id', $adminId);
        })->orderBy('created_at', 'asc')->get();

        return response()->json($messages);
    }

    public function superSendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'receiver_id' => 'required|exists:users,id',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return response()->json(['success' => true, 'message' => $message]);
    }

    public function getReportsData()
    {
        // Total applications grouped by month
        $applicationsByMonth = SoloParentApplication::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        // Approval statistics
        $approved = SoloParentApplication::where('status', 'approved')->count();
        $rejected = SoloParentApplication::where('status', 'rejected')->count();
        $pending = SoloParentApplication::where('status', 'pending')->count();

        // Recent system activities (example)
        $activities = UserActivity::latest()->take(10)->get(); // create a table for activities or adjust as needed

        return response()->json([
            'applicationsByMonth' => $applicationsByMonth,
            'approvalStats' => [
                'approved' => $approved,
                'rejected' => $rejected,
                'pending' => $pending
            ],
            'recentActivities' => $activities
        ]);
    }

    // ================================
    // FAQ MANAGEMENT (SUPER ADMIN)
    // ================================

    public function superGetFaqs()
    {
        return response()->json(
            Faq::orderBy('id', 'desc')->get()
        );
    }

    public function superStoreFaq(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
            'answer'   => 'required|string',
        ]);

        $faq = Faq::create([
            'question'  => $request->question,
            'answer'    => $request->answer,
            'is_active' => true,
        ]);

        // Add audit log
        AuditLog::create([
            'user' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
            'action' => "Added new FAQ: '{$faq->question}'",
            'module' => 'FAQs',
            'status' => 'Success'
        ]);

        return response()->json([
            'success' => true,
            'faq' => $faq
        ]);
    }


    public function superUpdateFaq(Request $request, $id)
    {
        $request->validate([
            'question' => 'required|string',
            'answer'   => 'required|string',
        ]);

        $faq = Faq::findOrFail($id);

        $faq->update([
            'question' => $request->question,
            'answer'   => $request->answer,
        ]);

        AuditLog::create([
            'user' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
            'action' => "Updated FAQ: '{$faq->question}'",
            'module' => 'FAQs',
            'status' => 'Updated'
        ]);

        return response()->json(['success' => true]);
    }

    public function superDeleteFaq($id)
    {
        $faq = Faq::findOrFail($id);
        $faqQuestion = $faq->question;
        $faq->delete();

        AuditLog::create([
            'user' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
            'action' => "Deleted FAQ: '{$faqQuestion}'",
            'module' => 'FAQs',
            'status' => 'Deleted'
        ]);

        return response()->json(['success' => true]);
    }

    public function superGetAuditLogs()
    {
        $logs = AuditLog::latest()->take(50)->get(); // latest 50 logs
        return response()->json($logs);
    }

    public function categoryLocationReport()
    {
        // CATEGORY (ALL BARANGAYS)
        $categories = DB::table('solo_parent_applications')
            ->select('category', DB::raw('COUNT(*) as total'))
            ->groupBy('category')
            ->get();
    
        // BARANGAY (ALL BARANGAYS)
        $barangays = DB::table('solo_parent_applications')
            ->select('barangay', DB::raw('COUNT(*) as total'))
            ->groupBy('barangay')
            ->orderBy('total','DESC')
            ->get();
    
        return response()->json([
            'categories' => $categories,
            'barangays'  => $barangays
        ]);
    }
    
    
public function monthlyPerformance(Request $request)
{
    $year = $request->year ?? now()->year;

    // ===== MONTHLY APPLICATIONS =====
    $raw = DB::table('solo_parent_applications')
        ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
        ->whereYear('created_at', $year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

    $months = [];
    $applications = [];

    for ($m = 1; $m <= 12; $m++) {
        $months[] = \DateTime::createFromFormat('!m', $m)->format('M');

        $found = $raw->firstWhere('month', $m);
        $applications[] = $found ? $found->total : 0;
    }

    // ===== APPROVAL STATUS (CASE FIXED) =====
    $approved = DB::table('solo_parent_applications')
        ->whereYear('created_at', $year)
        ->where('status', 'Approved')
        ->count();

    $pending = DB::table('solo_parent_applications')
        ->whereYear('created_at', $year)
        ->where('status', 'Pending')
        ->count();

    $rejected = DB::table('solo_parent_applications')
        ->whereYear('created_at', $year)
        ->where('status', 'Rejected')
        ->count();

    return response()->json([
        'months' => $months,
        'applications' => $applications,
        'approved' => $approved,
        'pending' => $pending,
        'rejected' => $rejected
    ]);
}


public function weeklyHomeVisits(Request $request)
{
    $year = $request->year ?? now()->year;

    // Get all visits for the year with week number
    $raw = DB::table('home_visits')
        ->selectRaw('WEEK(visit_date, 1) as week, COUNT(*) as total')
        ->whereYear('visit_date', $year)
        ->groupBy('week')
        ->orderBy('week')
        ->get();

    $weeks = [];
    $visits = [];

    // Weeks 1 to 52
    for ($w = 1; $w <= 52; $w++) {
        $weeks[] = 'Week ' . $w;
        $found = $raw->firstWhere('week', $w);
        $visits[] = $found ? $found->total : 0;
    }

    return response()->json([
        'weeks' => $weeks,
        'visits' => $visits,
    ]);
}


public function barangayList()
{
    $barangays = Barangay::leftJoin(
            'solo_parent_beneficiaries',
            'barangays.id',
            '=',
            'solo_parent_beneficiaries.barangay_id'
        )
        ->select(
            'barangays.id',
            'barangays.barangay',
            DB::raw('COUNT(solo_parent_beneficiaries.beneficiary_id) as total_beneficiaries')
        )
        ->groupBy('barangays.id', 'barangays.barangay')
        ->orderBy('barangays.barangay')
        ->get();

    return response()->json($barangays);
}

public function getScheduledBarangays()
{
    // Get all barangays
    $allBarangays = Barangay::orderBy('barangay')->get(); // assumes you have a Barangay model

    $data = $allBarangays->map(function($b) {
        // Get the next schedule for this barangay (if exists)
        $schedule = PayoutSchedule::where('barangay', $b->name)
                    ->whereDate('scheduled_date', '>=', now())
                    ->orderBy('scheduled_date', 'asc')
                    ->first();

        // Count total beneficiaries in this barangay
        $totalBeneficiaries = SoloParentBeneficiary::where('barangay', $b->barangay)->count();
        $receivedCount = SoloParentBeneficiary::where('barangay', $b->barangay)
                                ->where('assistance_status', 'Received')
                                ->count();

        return [
            'barangay' => $b->barangay,
            'scheduled_date' => $schedule?->scheduled_date ? Carbon::parse($schedule->scheduled_date)->format('M d, Y') : '-',
            'scheduled_time' => $schedule?->scheduled_time ?? '-',
            'location' => $schedule?->location ?? '-',
            'total_beneficiaries' => $totalBeneficiaries,
            'received_count' => $receivedCount,
            'status' => $schedule?->status ?? 'Pending',
            'schedule_id' => $schedule?->payout_scheduled_id ?? null,
        ];
    });

    return response()->json($data);
}


public function getBeneficiariesByBarangay($barangay)
{
    $barangay = urldecode($barangay);

    $beneficiaries = SoloParentBeneficiary::whereRaw(
        'LOWER(TRIM(barangay)) = ?',
        [strtolower(trim($barangay))]
    )
    ->select(
        'beneficiary_id',
        'first_name',
        'last_name',
        'street',
        'barangay',
        'municipality',
        'category',
        'assistance_status',
        'selected_benefits'
    )
    ->orderBy('last_name')
    ->get();

    return response()->json($beneficiaries);
}



    public function getPayoutSchedule($barangayId)
    {
        $schedule = PayoutSchedule::with('barangayRelation')
            ->where('barangay_id', $barangayId)
            ->first();

        if (!$schedule) {
            return response()->json([
                'error' => 'Barangay not found'
            ], 404);
        }

        return response()->json($schedule);
    }



}


