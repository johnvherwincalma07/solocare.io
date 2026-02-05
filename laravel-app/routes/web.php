<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SMSController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\UserController;
use App\Models\About;
use App\Models\Article;
use App\Models\Gallery;
use App\Models\Barangay;
use App\Http\Controllers\SoloParentExportController;
use App\Models\Faq;
use App\Http\Controllers\SoloParentBeneficiaryController;
use App\Http\Controllers\SuperAdminSoloParentExportController;
use App\Http\Controllers\SuperAdminBeneficiaryExportController;


// ==========================
// 🌐 PUBLIC ROUTES
// ==========================

Route::get('/', function () {
    $aboutContent = About::first()->content ?? '';
    $articles = Article::all();
    $gallery = Gallery::all();
    $faqs = Faq::all();

     // Hardcoded requirements for left and right columns

    $requirementsLeft = [
        ['title' => '1. As a Consequence of Rape', 'content' => '• Birth Certificate/s of the child<br>• Complaint Affidavit<br>• Sworn affidavit declaring not cohabiting<br>• Medical Record<br>• Barangay Affidavit<br>• 2 pcs 1x1 ID picture'],
        ['title' => '2. Death of the Spouse', 'content' => '• Death Certificate<br>• Barangay certification stating solo parenting responsibility'],
        ['title' => '3. Detained Spouse', 'content' => '• Certificate of Detention<br>• Court or Police record<br>• Barangay certification of solo parenting'],
        ['title' => '4. Physical or Mental Incapacity of Spouse', 'content' => '• Medical Certificate from government hospital<br>• Barangay certification<br>• Valid ID'],
        ['title' => '5. Legal Separation / Annulment', 'content' => '• Court Decision<br>• Barangay certification stating custody of children<br>• 2 pcs ID picture'],
        ['title' => '6. Abandonment by Spouse', 'content' => '• Barangay or Police blotter<br>• Sworn affidavit<br>• Certification of non-cohabitation'],
    ];

    $requirementsRight = [
        ['title' => '7. Unmarried Mother/Father', 'content' => '• Birth Certificate of child<br>• CENOMAR<br>• Affidavit of Solo Parenting'],
        ['title' => '8. Legal Guardian', 'content' => '• Court Appointment as Guardian<br>• Barangay certification of custody'],
        ['title' => '9. Foster or Adoptive Parent', 'content' => '• DSWD Certification<br>• Adoption papers<br>• Barangay certification'],
        ['title' => '10. Spouse Working Abroad (6+ Months)', 'content' => '• POEA/Company Certification<br>• Passport/Travel records<br>• Barangay certification of solo responsibility'],
        ['title' => '11. Abandoned by Partner (Unmarried)', 'content' => '• Barangay blotter<br>• Sworn affidavit<br>• Certification of non-cohabitation for at least 1 year'],
        ['title' => '12. Other Circumstances (Court Declaration)', 'content' => '• Court order or certification<br>• Barangay certification<br>• Valid ID & affidavit'],
    ];


    return view('home', compact('aboutContent', 'articles', 'gallery', 'requirementsLeft', 'requirementsRight', 'faqs'));
})->name('home');

// ==========================
// AUTH ROUTES
// ==========================
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/register', [RegisterController::class, 'register'])->name('register');
Route::post('/check-username', [RegisterController::class, 'checkUsername'])->name('check.username');
Route::post('/check-email', [RegisterController::class, 'checkEmail'])->name('check.email');

// Password Reset / OTP
Route::get('/forgot-password', [ForgotPasswordController::class, 'showRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendOtp'])->name('password.email');
Route::get('/otp-verify', [ForgotPasswordController::class, 'showOtpForm'])->name('otp.verify');
Route::post('/otp-verify', [ForgotPasswordController::class, 'verifyOtp'])->name('password.verifyOtp');
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

Route::get('/faqs', [HomeController::class, 'getPublicFaqs'])->name('faqs.public');

// SUPER ADMIN ROUTES
Route::prefix('super-admin')->middleware(['auth', 'role:super_admin'])->group(function () {

    Route::get('/dashboard/stats', [DashboardController::class, 'getDashboardStats'])->name('super.dashboard.stats');

    Route::get('/announcements', [DashboardController::class, 'superGetAnnouncements'])->name('super-admin.announcements');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('super.admin.dashboard');
    Route::get('/user-management', [DashboardController::class, 'superUserManagement'])->name('super.user.management');
    Route::get('/users', [DashboardController::class, 'superGetUsers'])->name('super.users');
    Route::put('/users/{user}', [DashboardController::class, 'updateUser'])->name('super.users.update');
    Route::delete('/users/{user}', [DashboardController::class, 'deleteUser'])->name('super.users.delete');

    Route::patch('/users/{user}/deactivate', [DashboardController::class, 'deactivateUser'])->name('super.users.deactivate');

    Route::get('/settings', [DashboardController::class, 'settings'])->name('superadmin.settings');
    Route::post('/settings/about', [DashboardController::class, 'updateAbout'])->name('superadmin.updateAbout');
    Route::post('/settings/qualified', [DashboardController::class, 'updateQualified'])->name('superadmin.updateQualified');
    Route::post('/settings/benefits', [DashboardController::class, 'updateBenefits'])->name('superadmin.updateBenefits');
    Route::post('/settings/articles', [DashboardController::class, 'storeArticle'])->name('superadmin.storeArticle');
    Route::post('/settings/articles/{id}', [DashboardController::class, 'updateArticle'])->name('superadmin.updateArticle');
    Route::delete('/settings/articles/{id}', [DashboardController::class, 'destroyArticle'])->name('superadmin.destroyArticle');
    Route::post('/settings/gallery', [DashboardController::class, 'storeGallery'])->name('superadmin.storeGallery');
    Route::delete('/settings/gallery/{id}', [DashboardController::class, 'destroyGallery'])->name('superadmin.destroyGallery');


    Route::post('/settings/system/update', [DashboardController::class, 'updateSystem'])->name('superadmin.system.update');


    Route::get('/faqs', [DashboardController::class, 'superGetFaqs'])->name('superadmin.faqs.fetch');
    Route::post('/faqs', [DashboardController::class, 'superStoreFaq'])->name('superadmin.faqs.store');
    Route::put('/faqs/{id}', [DashboardController::class, 'superUpdateFaq'])->name('superadmin.faqs.update');
    Route::delete('/faqs/{id}', [DashboardController::class, 'superDeleteFaq'])->name('superadmin.faqs.delete');
    Route::post('/super-admin/users', [DashboardController::class, 'store'])->name('super.users.store');

    Route::get('/audit-logs', [DashboardController::class, 'superGetAuditLogs'])->name('super.audit.logs');

    Route::get('/chat/messages/{id}', [DashboardController::class, 'superGetMessages'])->name('super.chat.messages');
    Route::post('/chat/send', [DashboardController::class, 'superSendMessage'])->name('super.chat.send');

    Route::get('/users/deactivated', [DashboardController::class, 'showDeactivatedUsers'])->name('admin.deactivatedUsers');
    Route::get('/reports/data', [DashboardController::class, 'getReportsData'])->name('super.reports.data');
    Route::get('/reports/category-location', [DashboardController::class, 'categoryLocationReport'])->name('superadmin.reports.category-location');
    Route::get('/reports/monthly-performance', [DashboardController::class, 'monthlyPerformance'])->name('superadmin.reports.monthly-performance');
    Route::get('/home-visits/weekly', [DashboardController::class, 'weeklyHomeVisits'])->name('super-admin.home-visits.weekly');
    
    
    Route::get('/solo-parent/export/excel', [SuperAdminSoloParentExportController::class, 'exportExcel'])->name('super-admin.solo-parent.export.excel');
    Route::get('/solo-parent/export/pdf', [SuperAdminSoloParentExportController::class, 'exportPdf'])->name('super-admin.solo-parent.export.pdf');
    Route::get('/super-admin/solo-parent/export', [SuperAdminSoloParentExportController::class, 'export'])->name('super-admin.solo-parent.export');
    
    Route::get('/beneficiaries/export/pdf', [SuperAdminBeneficiaryExportController::class, 'exportPdf'])->name('beneficiaries.export.pdf');
    Route::get('/beneficiaries/export/excel', [SuperAdminBeneficiaryExportController::class, 'exportExcel'])->name('beneficiaries.export.excel');
    
    
  
    
    Route::get('/benefits/stats', [DashboardController::class, 'stats'])->name('superadmin.benefits.stats');
    Route::get('/beneficiaries-by-barangay/{barangay}', [DashboardController::class, 'beneficiariesByBarangay'])->name('super-admin.beneficiaries.by.barangay');
    // List of barangays
Route::get('/barangays', [DashboardController::class, 'barangayList']);

// Solo parent beneficiaries by barangay
Route::get('/beneficiaries-by-barangay/{id}', [DashboardController::class, 'beneficiariesByBarangay']);
Route::get('/scheduled-barangays', [DashboardController::class, 'getScheduledBarangays']);

// routes/web.php
Route::get('/solo-parent-beneficiaries-by-barangay/{barangay}', [DashboardController::class, 'getBeneficiariesByBarangay']);
Route::get('/payout-schedule-by-barangay/{id}', [DashboardController::class, 'getPayoutSchedule']);




});


// ADMIN ROUTES
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');

    Route::get('/users', [AdminController::class, 'getUsers'])->name('admin.users');
    Route::get('/user-stats', [AdminController::class, 'getUserStats'])->name('admin.userStats');
    Route::get('/solo-parent-gis', [AdminController::class, 'getSoloParentGIS'])->name('admin.getSoloParentGIS');

    // AdminController
    Route::post('/events/create', [AdminController::class, 'createEvent'])->name('admin.events.create');
    Route::get('/events/fetch', [AdminController::class, 'fetch'])->name('admin.events.fetch');

    // Application workflow
    Route::prefix('application')->group(function () {
        Route::get('/{id}/files', [AdminController::class, 'getApplicationFiles'])->name('admin.application.files');
        Route::post('/next-step', [AdminController::class, 'nextStep'])->name('application.nextStep');
        Route::post('/move-to-schedule', [AdminController::class, 'nextStep'])->name('application.moveToSchedule');
        Route::post('/reject', [AdminController::class, 'rejectApplication'])->name('application.reject');

    });


    // QR Code (Admin)
    Route::get('/generate-qr/{barangay}', [AdminController::class, 'generateQr'])->name('admin.generateQr');
    Route::get('/generate-qr-beneficiary/{id}', [AdminController::class, 'generateBeneficiaryQr'])->name('admin.generateBeneficiaryQr');


    // Scheduled submissions
    Route::prefix('scheduled-submissions')->group(function () {
        Route::post('/save', [AdminController::class, 'saveSchedule'])->name('scheduled-submissions.store');
        Route::post('/move-to-homevisit', [AdminController::class, 'moveToHomeVisit'])->name('scheduled.moveToHomeVisit');
        Route::post('/complete', [AdminController::class, 'markCompleted'])->name('scheduled-submissions.complete');

    });
    

    
    

    // Home Visits
    Route::prefix('homevisit')->group(function () {
        Route::post('/store', [AdminController::class, 'storeHomeVisit'])->name('homevisit.store'); // optional
        Route::post('/save-schedule', [AdminController::class, 'scheduleHomeVisit'])->name('homevisit.saveSchedule');
        Route::post('/move-to-ready', [AdminController::class, 'moveToReady'])->name('homevisit.moveToReady');
        Route::post('/reject', [AdminController::class, 'rejectHomeVisit'])->name('homevisit.reject'); // optional
    });

    Route::get('/beneficiaries/ready', [AdminController::class, 'getReadyBeneficiaries'])->name('beneficiaries.ready');


    // MOVE TO BENEFICIARY
    Route::post('/move-to-beneficiary', [AdminController::class, 'moveToBeneficiary'])
    ->name('ready.moveToBeneficiary');

    Route::get('beneficiary-benefits/{beneficiary}', [AdminController::class, 'getBeneficiaryBenefits'])->name('admin.getBeneficiaryBenefits');
    Route::get('beneficiaries/{id}', [AdminController::class, 'showBeneficiary'])->name('beneficiaries.show');



    // Beneficiaries
    Route::get('/beneficiaries', [AdminController::class, 'getBeneficiaries'])->name('beneficiaries.index');
    Route::post('/get-beneficiaries', [AdminController::class, 'getBeneficiaries'])
    ->name('admin.getBeneficiaries');

    Route::get('/beneficiaries/{id}/benefits', [AdminController::class, 'getBeneficiaryBenefits']);
    Route::post('payout/save', [AdminController::class, 'savePayoutSchedule'])->name('admin.savePayoutSchedule');

   // Save selected benefits for a beneficiary
    Route::post('/save-benefits', [AdminController::class, 'saveBenefits'])->name('admin.beneficiaries.save-benefits');
    Route::post('/add-beneficiary', [AdminController::class, 'moveToBeneficiary'])->name('beneficiary.add');


    // Beneficiary Benefits
    Route::prefix('beneficiary-benefits')->group(function () {
        Route::post('/assign', [AdminController::class, 'assignBenefit'])->name('admin.beneficiaryBenefits.assign');
        Route::delete('/remove/{id}', [AdminController::class, 'removeBenefit'])->name('admin.beneficiaryBenefits.remove');
    });

    // Beneficiary category benefits
    Route::get('/category-benefits/{category}', [AdminController::class, 'categoryBenefits'])->name('admin.categoryBenefits');
    Route::get('category-benefits/{cat}', [AdminController::class, 'getCategoryBenefits']);

    Route::post('/benefits/mark-received', [AdminController::class, 'markBenefitReceived'])->name('admin.benefits.markReceived');

    // Chat
    Route::get('/chat/messages/{userId}', [AdminController::class, 'fetchMessages'])->name('admin.chat.messages');
    Route::post('/chat/send', [AdminController::class, 'sendMessage'])->name('admin.chat.send');

    // Attendance
    Route::get('/attendance', [AdminController::class, 'getAttendance'])->name('admin.attendance.get');
    Route::post('/attendance', [AdminController::class, 'storeAttendance'])->name('admin.attendance.store');

    // SMS
    Route::post('/send-sms', [SMSController::class, 'send'])->name('sms.send');

    Route::get('/reports/solo-parent-distribution',[AdminController::class, 'soloParentDistribution'])->name('admin.reports.solo-parent-distribution');
    Route::get('/reports/monthly-performance', [AdminController::class, 'monthlyPerformance'])->name('admin.reports.monthly-performance');

    // Reports
    Route::prefix('admin/reports')->name('admin.reports.')->group(function() {
        Route::get('/monthly-performance', [App\Http\Controllers\AdminController::class, 'monthlyPerformance'])->name('monthly-performance');
        Route::get('/solo-parent-distribution', [App\Http\Controllers\AdminController::class, 'soloParentDistribution'])->name('solo-parent-distribution');
    });


    Route::get('/solo-parent/export/pdf', [SoloParentExportController::class, 'exportPdf'])->name('solo-parent.export.pdf');
    Route::get('/solo-parent/export/excel', [SoloParentExportController::class, 'exportExcel'])->name('solo-parent.export.excel');

     Route::get(
        '/solo-parent-beneficiaries/export/pdf',
        [SoloParentBeneficiaryController::class, 'exportPdf']
    )->name('solo-parent.beneficiary.export.pdf');

    Route::get(
        '/solo-parent-beneficiaries/export/excel',
        [SoloParentBeneficiaryController::class, 'exportExcel']
    )->name('solo-parent.beneficiary.export.excel');
    // Export
    Route::prefix('export')->group(function () {
        Route::get('/beneficiaries', [AdminController::class, 'exportBeneficiaries'])->name('export.beneficiaries');
        Route::get('/barangays', [AdminController::class, 'exportBarangays'])->name('export.barangays');
        Route::get('/benefits', [AdminController::class, 'exportBenefits'])->name('export.benefits');
        Route::get('/application-trends', [AdminController::class, 'exportApplicationTrends'])->name('export.application-trends');
    });
    
   
  
    Route::get('/tejero-schedule', [AdminController::class, 'getTejeroPayoutSchedule'])->name('admin.tejero.schedule');
    Route::post('/update-payout-schedule/{id}', [AdminController::class, 'updatePayoutSchedule'])->name('admin.update.payout.schedule');
    

    Route::get('/tejero-total', [AdminController::class, 'getTejeroTotal']);

Route::get('/tejero-beneficiaries-json', [AdminController::class, 'tejeroJson'])->name('admin.tejero.json');

    


    
    
});

// USER ROUTES
Route::middleware(['auth', 'role:user'])->prefix('user')->group(function () {
    Route::get('/dashboard', [ApplicationController::class, 'userDashboard'])->name('user.dashboard');
    Route::post('/application', [ApplicationController::class, 'store'])->name('applications.store');
    Route::get('/application', [ApplicationController::class, 'showApplication'])->name('applications.index');
    Route::post('/change-password', [UserController::class, 'changePassword'])->name('user.change.password');


    Route::get('/application/track/{reference_no}', [ApplicationController::class, 'trackApplication'])->name('user.application.track');
    Route::get('/user-dashboard/attendance', [UserController::class, 'showAttendanceForm'])->name('user.attendance.register');

    Route::get('/application/{id}/download', [ApplicationController::class, 'downloadPdf'])->name('applications.download');
    Route::get('/application/view/{id}', [ApplicationController::class, 'viewPdf'])->name('solo-parent.view');
    Route::post('/benefits/mark-received', [UserController::class, 'markBenefitReceived'])->name('user.benefits.markReceived');

    // QR Scan & Mark Received (User)
    Route::get('/benefit/scan/{barangay}', [UserController::class, 'scanQr'])->name('user.scanQr');
    Route::post('/benefit/scan/receive', [UserController::class, 'markReceived'])->name('user.markReceived');

    Route::post('/profile/avatar/update', [UserController::class, 'updateAvatar'])->name('user.profile.avatar.update');

});

// ==========================
// APPLICATION TRACKING
// ==========================
Route::get('/track-application', [ApplicationController::class, 'track'])->name('applications.track');
Route::get('/application/track/result', [ApplicationController::class, 'track'])->name('application.track.result');

// ==========================
// CHAT (GENERAL)
// ==========================
Route::middleware('auth')->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/messages', [ChatController::class, 'fetchMessages'])->name('chat.messages');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
});

// ==========================
// ANNOUNCEMENTS
// ==========================
Route::get('/api/announcements', [AnnouncementController::class, 'fetchAnnouncements'])
    ->middleware(['auth', 'role:admin,super_admin,user']);
