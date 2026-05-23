<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KonselingController extends Controller
{
    public function index()
    {
        return view('konseling.index');
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'history' => 'nullable|array',
        ]);

        $apiKey = config('services.groq.key');

        if (!$apiKey || $apiKey === 'YOUR_GROQ_API_KEY_HERE') {
            return response()->json([
                'reply' => 'Konfigurasi API Key belum diset. Periksa file .env kamu. 🙏'
            ]);
        }

        // ── Susun riwayat percakapan ──
        $messages = [];

        // System prompt: persona Kak Sari
        $messages[] = [
            'role'    => 'system',
            'content' => "Kamu adalah Kak Sari, konselor mahasiswa dari BSI Campus Hub. "
                . "Tugasmu membantu mahasiswa mengatasi tekanan akademik, deadline menumpuk, "
                . "motivasi belajar, dan masalah kehidupan kampus. "
                . "Jawab dengan hangat, empatik, dan gunakan bahasa Indonesia yang sopan namun santai seperti kakak. "
                . "Gunakan emoji sesekali agar terasa ramah. "
                . "Batasi jawaban maksimal 4 paragraf agar mudah dibaca. "
                . "JANGAN memberikan diagnosis medis. "
                . "Jika ada indikasi darurat (menyakiti diri sendiri dll), sarankan hubungi hotline 119 ext 8.",
        ];

        // Masukkan history percakapan sebelumnya (konteks)
        if ($request->has('history') && is_array($request->history)) {
            foreach ($request->history as $chat) {
                if (isset($chat['role']) && isset($chat['text'])) {
                    $messages[] = [
                        'role'    => $chat['role'] === 'user' ? 'user' : 'assistant',
                        'content' => $chat['text'],
                    ];
                }
            }
        }

        // Pesan user terbaru
        $messages[] = [
            'role'    => 'user',
            'content' => $request->input('message'),
        ];

        try {
            $response = Http::withoutVerifying()
                ->timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type'  => 'application/json',
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'       => 'llama-3.3-70b-versatile',
                    'messages'    => $messages,
                    'temperature' => 0.75,
                    'max_tokens'  => 600,
                ]);

            if ($response->failed()) {
                $errorData = $response->json();
                Log::error('Groq API Error:', ['status' => $response->status(), 'body' => $errorData]);

                if ($response->status() === 429) {
                    return response()->json([
                        'reply' => 'Kak Sari sedang menerima banyak pesan. Coba lagi dalam 1 menit ya! 🙏'
                    ]);
                }

                return response()->json([
                    'reply' => 'Maaf, koneksi ke Kak Sari terputus. Silakan coba beberapa saat lagi. 😊'
                ]);
            }

            $data  = $response->json();
            $reply = $data['choices'][0]['message']['content']
                     ?? 'Maaf, Kak Sari agak bingung menjawabnya. Bisa diulangi?';

            return response()->json(['reply' => $reply]);

        } catch (\Exception $e) {
            Log::error('Groq Exception: ' . $e->getMessage());
            return response()->json([
                'reply' => 'Terjadi kendala teknis. Mohon tunggu sebentar ya. 🙏'
            ], 500);
        }
    }
}
