<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Message;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

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
                
            // Find the user associated with this session (from any non-admin message)
            $userMsg = Message::with('user')
                ->where('session_id', $s->session_id)
                ->where('is_admin', false)
                ->whereNotNull('user_id')
                ->first();
            
            $unreadCount = Message::where('session_id', $s->session_id)
                ->where('is_admin', false)
                ->where('is_read', false)
                ->count();
                
            $activeChats[] = (object) [
                'session_id' => $s->session_id,
                'user' => $userMsg ? $userMsg->user : null,
                'latest_message' => $latestMsg->content,
                'last_activity' => $latestMsg->created_at,
                'unread_count' => $unreadCount
            ];
        }

        $products = Product::with('images')->where('is_active', true)->get();
        $botActive = Cache::get('bot_active', true);
        $aiActive = Cache::get('ai_active', true);

        return view('admin.chat', compact('activeChats', 'products', 'botActive', 'aiActive'));
    }

    public function toggleBot(Request $request, $type)
    {
        $active = $request->input('active', false);
        $key = $type === 'ai' ? 'ai_active' : 'bot_active';
        Cache::forever($key, filter_var($active, FILTER_VALIDATE_BOOLEAN));
        return response()->json(['success' => true, $key => Cache::get($key)]);
    }

    public function getMessages($sessionId)
    {
        // Mark as read when admin opens chat
        Message::where('session_id', $sessionId)
            ->where('is_admin', false)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::with(['user', 'replyTo', 'product.images'])
            ->where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get();
            
        return response()->json($messages);
    }

    public function reply(Request $request, $sessionId)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'reply_to_id' => 'nullable|exists:messages,id',
            'product_id' => 'nullable|exists:products,id',
        ]);

        $message = Message::create([
            'session_id' => $sessionId,
            'user_id' => auth()->id(), // Admin's user ID
            'is_admin' => true,
            'content' => $request->content,
            'is_read' => true, // Admin sent it, so it's read by admin
            'reply_to_id' => $request->reply_to_id,
            'product_id' => $request->product_id,
        ]);

        return response()->json($message);
    }

    public function destroy($id)
    {
        $message = Message::findOrFail($id);
        $message->delete();
        
        return response()->json(['success' => true]);
    }
}
