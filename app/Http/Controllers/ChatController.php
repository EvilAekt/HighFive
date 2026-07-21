<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ChatController extends Controller
{
    private function getSessionId(Request $request)
    {
        if (auth()->check()) {
            return (string) auth()->id();
        }

        if (!session()->has('chat_session_id')) {
            session(['chat_session_id' => \Illuminate\Support\Str::uuid()->toString()]);
        }
        return session('chat_session_id');
    }

    public function index(Request $request)
    {
        $sessionId = $this->getSessionId($request);
        
        $messages = Message::with(['replyTo', 'product.images'])->where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get();
            
        // If the user is logged in but the session ID was created before they logged in,
        // we might want to attach user_id. For now, session_id is enough.
        
        return response()->json($messages);
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'reply_to_id' => 'nullable|exists:messages,id',
            'product_id' => 'nullable|exists:products,id',
        ]);

        $sessionId = $this->getSessionId($request);

        $message = Message::create([
            'session_id' => $sessionId,
            'user_id' => auth()->id(),
            'is_admin' => false,
            'content' => $request->content,
            'is_read' => false,
            'reply_to_id' => $request->reply_to_id,
            'product_id' => $request->product_id,
        ]);

        // Auto-reply Bot Logic (Using Gemini AI)
        $botReply = null;
        $botProductId = null;
        $geminiKey = env('GEMINI_API_KEY');
        $isAiActive = Cache::get('ai_active', true);
        $isBotActive = Cache::get('bot_active', true);

        if ($geminiKey && $isAiActive) {
            try {
                // Fetch Product Context
                $products = \App\Models\Product::with('variants')->where('is_active', true)->get();
                
                $soldItems = \Illuminate\Support\Facades\DB::table('order_items')
                    ->join('product_variants', 'product_variants.id', '=', 'order_items.product_variant_id')
                    ->join('orders', 'orders.id', '=', 'order_items.order_id')
                    ->where('orders.status', 'paid')
                    ->select('product_variants.product_id', \Illuminate\Support\Facades\DB::raw('SUM(order_items.quantity) as total_sold'))
                    ->groupBy('product_variants.product_id')
                    ->pluck('total_sold', 'product_id');

                $productContext = "DATA STOK, HARGA, DESKRIPSI, DAN PENJUALAN PRODUK:\n";
                foreach ($products as $p) {
                    $terjual = $soldItems->get($p->id, 0);
                    $rilis = $p->created_at->format('d M Y');
                    $productContext .= "- ID: {$p->id} | {$p->name} (Harga: Rp " . number_format($p->price, 0, ',', '.') . " | Terjual: {$terjual} pcs | Rilis: {$rilis}). Deskripsi: {$p->description}. Varian: ";
                    
                    if ($p->variants->isEmpty()) {
                        $productContext .= "Tidak ada varian/Stok Kosong.\n";
                    } else {
                        $variants = [];
                        foreach ($p->variants as $v) {
                            $colorStr = $v->color ? "Warna {$v->color} " : "";
                            $variants[] = "{$colorStr}Ukuran {$v->size} ({$v->stock} pcs)";
                        }
                        $productContext .= implode(', ', $variants) . ".\n";
                    }
                }

                $prompt = "Kamu adalah bot admin Customer Service dari toko baju HIGH FIVE. 
Tugasmu:
1. Panggil pengguna dengan 'kak'.
2. Jawab SANGAT SINGKAT (1 atau 2 kalimat saja).
3. Jika pertanyaannya halo/hai pertama kali, balas sapaan. Jika pertanyaan di luar konteks toko, keluhan rumit, atau tidak masuk akal, KAMU HARUS DIAM (jawab dengan string kosong \"\"). Jangan terus-menerus bilang 'Halo kak ada yang bisa dibantu'. Hanya jawab yang masuk akal terkait pakaian/toko.
4. Gunakan DATA STOK, HARGA, DAN PENJUALAN di bawah ini untuk menjawab pertanyaan soal stok, warna, ketersediaan ukuran, atau harga. Jika stok 0 atau ukuran/warna tidak ada, sampaikan habis/tidak ada.
5. Jika pelanggan meminta rekomendasi, rekomendasikan produk dengan jumlah 'Terjual' paling banyak (atau 'Rilis' terbaru jika diminta yang baru).
6. PENTING: Jika kamu menyebutkan atau merekomendasikan produk spesifik, tulislah ID produk tersebut di kolom 'product_id' pada output JSON agar sistem memunculkan foto produk! JANGAN tulis link HTML di kolom reply.

{$productContext}

Pertanyaan Pelanggan: '{$request->content}'
Keluarkan HANYA valid JSON seperti ini:
{\"reply\": \"jawabanmu di sini\", \"product_id\": \"isi dengan ID produk yang direkomendasikan jika ada, selain itu kosongkan string\"}";

                $response = \Illuminate\Support\Facades\Http::timeout(60)->post("https://generativelanguage.googleapis.com/v1beta/models/gemma-4-26b-a4b-it:generateContent?key={$geminiKey}", [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json'
                    ]
                ]);

                \Illuminate\Support\Facades\Log::info('Gemini Raw Response: ' . $response->body());

                if ($response->successful()) {
                    $parts = $response->json('candidates.0.content.parts');
                    $rawText = '';
                    if (is_array($parts)) {
                        foreach ($parts as $part) {
                            if (empty($part['thought'])) {
                                $rawText .= $part['text'] ?? '';
                            }
                        }
                    } else {
                        $rawText = $response->json('candidates.0.content.parts.0.text', '');
                    }
                    
                    if (preg_match('/\{.*\}/s', $rawText, $matches)) {
                        $jsonText = $matches[0];
                        $decoded = json_decode($jsonText, true);
                        if ($decoded && isset($decoded['reply']) && trim($decoded['reply']) !== '') {
                            $botReply = trim($decoded['reply']);
                            if (isset($decoded['product_id']) && trim($decoded['product_id']) !== '') {
                                $botProductId = trim($decoded['product_id']);
                            }
                        }
                    }
                } else {
                    \Illuminate\Support\Facades\Log::error('Gemini API Error Status: ' . $response->status());
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Gemini API Exception: ' . $e->getMessage());
            }
        }

        // Rule-based Bot Fallback
        if (!$botReply && $isBotActive) {
            $lowercaseContent = strtolower($request->content);
            if (str_contains($lowercaseContent, 'harga') || str_contains($lowercaseContent, 'price')) {
                $botReply = "Halo kak! Untuk detail harga masing-masing produk sudah tertera di halaman produk ya. Ada koleksi spesifik yang ingin ditanyakan?";
            } elseif (str_contains($lowercaseContent, 'ongkir') || str_contains($lowercaseContent, 'pengiriman')) {
                $botReply = "Pengiriman kami mencakup seluruh Indonesia. Dapatkan GRATIS ONGKIR untuk pembelian di atas Rp 500.000! 🚚";
            } elseif (str_contains($lowercaseContent, 'ready') || str_contains($lowercaseContent, 'stok') || str_contains($lowercaseContent, 'halo') || str_contains($lowercaseContent, 'p')) {
                $botReply = "Halo kak! Semua produk yang bisa dipilih ukurannya di website artinya READY ya. Ada yang bisa dibantu? 💖";
            } elseif (str_contains($lowercaseContent, 'test') || str_contains($lowercaseContent, 'tes')) {
                $botReply = "Bot otomatis HIGH FIVE sedang aktif kak! Ada yang bisa kami bantu? 🤖";
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
            $botMessage = Message::create([
                'session_id' => $sessionId,
                'user_id' => null,
                'is_admin' => true,
                'content' => $botReply,
                'is_read' => false, // User hasn't read it yet
                'reply_to_id' => $message->id, // Bot always replies to the incoming message
                'product_id' => $botProductId, // Attach the recommended product
            ]);
        }

        return response()->json($message);
    }

    public function destroy(Request $request, $id)
    {
        $sessionId = $this->getSessionId($request);
        $message = Message::where('session_id', $sessionId)->where('id', $id)->firstOrFail();
        
        // Allow user to delete their own message or hide it
        $message->delete();
        
        return response()->json(['success' => true]);
    }

    public function report(Request $request, $id)
    {
        $sessionId = $this->getSessionId($request);
        $message = Message::where('session_id', $sessionId)->where('id', $id)->firstOrFail();
        
        // Mocking the report functionality
        // In a real application, you might save this to a 'reports' table
        
        return response()->json(['success' => true, 'message' => 'Pesan telah dilaporkan.']);
    }
}
