<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Notification;
use App\Models\SoloParentApplication;
use App\Models\ScheduledSubmission;
use App\Models\HomeVisit;
use App\Models\ReadyToProcess;
use App\Models\SoloParentBeneficiary;
use App\Models\Announcement;
use App\Models\Event;
use App\Models\User;
use App\Models\BeneficiaryBenefit;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // --- Latest 5 announcements ---
        $announcements = Announcement::latest()->take(5)->get();

        // --- Latest submitted application ---
        $application = SoloParentApplication::where('user_id', $user->id)
            ->where('is_submitted', true)
            ->latest()
            ->first();

        $schedule = $homeVisit = $ready = $beneficiary = null;

        if ($application) {
            $schedule = ScheduledSubmission::where('application_id', $application->application_id)->latest()->first();
            $homeVisit = HomeVisit::where('application_id', $application->application_id)->latest()->first();
            $ready = ReadyToProcess::where('application_id', $application->application_id)->latest()->first();
            $beneficiary = SoloParentBeneficiary::where('application_id', $application->application_id)->latest()->first();
        }

        // 🔔 USER NOTIFICATIONS
        $scheduledNotifications = [];

        // Database notifications
        $dbNotifications = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->latest()
            ->take(5)
            ->get();

        foreach ($dbNotifications as $notif) {
            $scheduledNotifications[] = [
                'message' => $notif->message,
                'type' => $notif->type,
            ];
        }

        // Stage-based notifications
        if ($application && $application->status !== 'Rejected') {
            // Document submission
            if ($application->application_stage === 'Scheduled for Submission') {
                if ($application->status === 'Awaiting Documents') {
                    $scheduledNotifications[] = ['message' => 'Your application is awaiting document submission.'];
                } elseif ($application->status === 'Documents Scheduled' && $schedule) {
                    $scheduledNotifications[] = [
                        'message' => 'Your document submission is scheduled on '
                            . \Carbon\Carbon::parse($schedule->scheduled_date)->format('F j, Y')
                            . ' at '
                            . \Carbon\Carbon::parse($schedule->scheduled_time)->format('h:i A'),
                    ];
                }
            }

            // Home Visit
            if ($application->application_stage === 'Home Visit') {
                if ($application->status === 'Awaiting Home Visit') {
                    $scheduledNotifications[] = ['message' => 'Your application is awaiting home visit scheduling.'];
                } elseif ($application->status === 'Home Visit Scheduled' && $homeVisit) {
                    $scheduledNotifications[] = [
                        'message' => 'Your home visit is scheduled on '
                            . \Carbon\Carbon::parse($homeVisit->visit_date)->format('F j, Y')
                            . ' at '
                            . \Carbon\Carbon::parse($homeVisit->visit_time)->format('h:i A'),
                    ];
                }
            }

            // Ready to process
            if ($application->application_stage === 'Ready to Process') {
                $scheduledNotifications[] = ['message' => 'Your application is now ready for final processing.'];
            }

            // Approved
            if ($application->application_stage === 'Verified Solo Parent') {
                $scheduledNotifications[] = ['message' => 'Congratulations! Your Solo Parent application has been approved.'];
            }
        }

        return view('user.dashboard', compact(
            'user',
            'application',
            'schedule',
            'homeVisit',
            'ready',
            'beneficiary',
            'announcements',
            'scheduledNotifications'
        ));
    }

    public function showAttendanceForm(Request $request)
    {
        $eventId = $request->query('eventId');
        $event = Event::findOrFail($eventId);

        return view('user.attendance_form', compact('event'));
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = $request->new_password;
        $user->save();

        Auth::logout();

        return redirect('/')->with('status', 'Password updated successfully. Please login again.');
    }

    public function markBenefitReceived(Request $request)
    {
        $request->validate([
            'beneficiaryId' => 'required|exists:solo_parent_beneficiaries,beneficiary_id',
            'benefitName' => 'required|string',
            'timestamp' => 'required|date',
        ]);

        $beneficiaryId = $request->beneficiaryId;
        $benefitName = $request->benefitName;
        $timestamp = $request->timestamp;

        $benefit = BeneficiaryBenefit::where('beneficiary_id', $beneficiaryId)
            ->where('benefit_name', $benefitName)
            ->first();

        if ($benefit) {
            $benefit->status = 'received';
            $benefit->date_given = $timestamp;
            $benefit->save();
        } else {
            BeneficiaryBenefit::create([
                'beneficiary_id' => $beneficiaryId,
                'benefit_name' => $benefitName,
                'status' => 'received',
                'date_given' => $timestamp,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Benefit marked as received']);
    }

public function updateAvatar(Request $request)
{
    $request->validate([
        'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,jfif|max:5120',
    ]);

    $user = auth()->user();

    // Delete old avatar if exists
    $oldAvatarPath = $_SERVER['DOCUMENT_ROOT'] . '/storage/avatars/' . $user->avatar;
    if ($user->avatar && file_exists($oldAvatarPath)) {
        unlink($oldAvatarPath);
    }

    $file = $request->file('avatar');

    // Sanitize filename
    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $originalName);
    $extension = $file->getClientOriginalExtension();
    $filename = time() . '_' . $safeName . '.' . $extension;

    // Store in public_html/storage/avatars
    $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/storage/avatars';
    if (!file_exists($destinationPath)) {
        mkdir($destinationPath, 0755, true);
    }

    $file->move($destinationPath, $filename);

    // Save filename in DB
    $user->avatar = $filename;
    $user->save();

    return redirect()->back()->with('success', 'Profile photo updated successfully!');
}




}
