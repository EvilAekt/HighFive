<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Message;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function index()
    {
        // Get all unique sessions with their latest message
        $sessions = Message::select('session_id', DB::raw('MAX(created_at) as last_activity'))
            ->groupBy('session_id')
            ->orderBy('last_activity', 'desc')
            ->get();
            
        // We will fetch the actual latest message details for the sidebar in the view
        $activeChats = [];
        foreach ($sessions as $s) {
            $latestMsg = Message::where('session_id', $s->session_id)
                ->orderBy('created_at', 'desc')
                ->first();
            
            $unreadCount = Message::where('session_id', $s->session_id)
                ->where('is_admin', false)
                ->where('is_read', false)
                ->count();
                
            $activeChats[] = (object) [
                'session_id' => $s->session_id,
                'user' => $latestMsg->user, // Might be null
                'latest_message' => $latestMsg->content,
                'last_activity' => $latestMsg->created_at,
                'unread_count' => $unreadCount
            ];
        }

        return view('admin.chat', compact('activeChats'));
    }

    public function getMessages($sessionId)
    {
        // Mark as read when admin opens chat
        Message::where('session_id', $sessionId)
            ->where('is_admin', false)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::with('user')
            ->where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get();
            
        return response()->json($messages);
    }

    public function reply(Request $request, $sessionId)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $message = Message::create([
            'session_id' => $sessionId,
            'user_id' => auth()->id(), // Admin's user ID
            'is_admin' => true,
            'content' => $request->content,
            'is_read' => true, // Admin sent it, so it's read by admin
        ]);

        return response()->json($message);
    }
}
