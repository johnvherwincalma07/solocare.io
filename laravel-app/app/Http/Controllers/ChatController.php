<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // ================= USER METHODS =================
    public function index() {
        return view('chat.index'); // user chat view
    }

    public function sendMessage(Request $request) {
        $admin = User::where('role', 'admin')->first();
        if(!$admin) return response()->json(['error' => 'Admin not found'], 404);

        $request->validate(['message' => 'required|string']);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $admin->id,
            'message' => $request->message
        ]);

        return response()->json($message);
    }

    public function fetchMessages() {
        $admin = User::where('role', 'admin')->first();
        if(!$admin) return response()->json([]);

        $messages = Message::where(function($q) use ($admin) {
            $q->where('sender_id', Auth::id())->where('receiver_id', $admin->id);
        })->orWhere(function($q) use ($admin) {
            $q->where('sender_id', $admin->id)->where('receiver_id', Auth::id());
        })->orderBy('created_at')->get();

        return response()->json($messages);
    }

    // ================= ADMIN METHODS =================
    public function adminFetchMessages($userId) {
        $messages = Message::where(function($q) use ($userId) {
            $q->where('sender_id', $userId)->orWhere('receiver_id', $userId);
        })->orderBy('created_at')->get();

        return response()->json($messages);
    }

    public function adminSendMessage(Request $request) {
        $request->validate([
            'message' => 'required|string',
            'receiver_id' => 'required|integer|exists:users,id'
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(), // admin ID
            'receiver_id' => $request->receiver_id,
            'message' => $request->message
        ]);

        return response()->json($message);
    }
}
