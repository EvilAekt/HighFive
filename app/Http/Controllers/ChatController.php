<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    private function getSessionId(Request $request)
    {
        if (!session()->has('chat_session_id')) {
            session(['chat_session_id' => Str::uuid()->toString()]);
        }
        return session('chat_session_id');
    }

    public function index(Request $request)
    {
        $sessionId = $this->getSessionId($request);
        
        $messages = Message::where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get();
            
        // If the user is logged in but the session ID was created before they logged in,
        // we might want to attach user_id. For now, session_id is enough.
        
        return response()->json($messages);
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $sessionId = $this->getSessionId($request);

        $message = Message::create([
            'session_id' => $sessionId,
            'user_id' => auth()->id(),
            'is_admin' => false,
            'content' => $request->content,
            'is_read' => false,
        ]);

        // Auto-reply Bot Logic (Using Gemini AI)
        $botReply = null;
        $geminiKey = env('GEMINI_API_KEY');

        if ($geminiKey) {
            try {
                $prompt = "Kamu adalah karyawan dan Admin Customer Service kebanggaan dari toko baju lokal bernama HIGH FIVE. 
Tugasmu adalah melayani pelanggan dengan ramah, santai, gaul (selalu panggil pelanggan dengan 'kak'), dan sangat profesional. 
Tanamkan identitas bahwa kamu sangat bangga bekerja di HIGH FIVE. Jika ada yang bertanya identitasmu, jawablah bahwa kamu adalah admin/karyawan HIGH FIVE. Jawablah setiap pertanyaan dalam 1-2 kalimat pendek saja yang terasa natural layaknya manusia.
Pertanyaan Pelanggan: '{$request->content}'
WAJIB keluarkan HANYA valid JSON tanpa format markdown, dengan struktur persis seperti ini:
{\"reply\": \"tulis balasan akhir kamu di sini\"}";

                $response = \Illuminate\Support\Facades\Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemma-4-26b-a4b-it:generateContent?key={$geminiKey}", [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json'
                    ]
                ]);

                if ($response->successful()) {
                    $rawText = $response->json('candidates.0.content.parts.0.text');
                    
                    // Ekstrak JSON menggunakan Regex dari output yang berisik
                    if (preg_match('/\{.*\}/s', $rawText, $matches)) {
                        $jsonText = $matches[0];
                        $decoded = json_decode($jsonText, true);
                        if ($decoded && isset($decoded['reply'])) {
                            $botReply = trim($decoded['reply']);
                        }
                    }
                    
                    // Fallback aman jika parsing JSON benar-benar gagal
                    if (!$botReply) {
                        $botReply = "Halo kak! Ada yang bisa dibantu?"; 
                    }
                }
            } catch (\Exception $e) {
                // Silently fail to fallback
            }
        }
        
        // Fallback if API fails or no key
        if (!$botReply) {
            $lowercaseContent = strtolower($request->content);
            if (str_contains($lowercaseContent, 'harga') || str_contains($lowercaseContent, 'price')) {
                $botReply = "Halo kak! Untuk detail harga masing-masing produk sudah tertera di halaman produk ya. Ada koleksi spesifik yang ingin ditanyakan?";
            } elseif (str_contains($lowercaseContent, 'ongkir') || str_contains($lowercaseContent, 'pengiriman')) {
                $botReply = "Pengiriman kami mencakup seluruh Indonesia. Dapatkan GRATIS ONGKIR untuk pembelian di atas Rp 500.000! 🚚";
            } elseif (str_contains($lowercaseContent, 'ready') || str_contains($lowercaseContent, 'stok') || str_contains($lowercaseContent, 'halo') || str_contains($lowercaseContent, 'p')) {
                $botReply = "Halo kak! Semua produk yang bisa dipilih ukurannya di website artinya READY ya. Ada yang bisa dibantu? 💖";
            } else {
                $messageCount = Message::where('session_id', $sessionId)->count();
                if ($messageCount <= 1) {
                    $botReply = "Terima kasih telah menghubungi HIGH FIVE! Admin kami akan segera membalas pesan kakak. Mohon ditunggu sebentar ya! ✨";
                }
            }
        }

        if ($botReply) {
            // Small artificial delay so it feels like a bot typing (executed quickly but timestamps differ slightly)
            sleep(1); 
            Message::create([
                'session_id' => $sessionId,
                'user_id' => null,
                'is_admin' => true,
                'content' => $botReply,
                'is_read' => false, // User hasn't read it yet
            ]);
        }

        return response()->json($message);
    }
}
