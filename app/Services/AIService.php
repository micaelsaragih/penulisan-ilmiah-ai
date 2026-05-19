<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * AIService — Integrasi Google Gemini API
 *
 * Service ini menghubungkan aplikasi dengan Google Gemini API
 * untuk memperbaiki teks artikel menjadi bahasa ilmiah formal.
 *
 * Endpoint: generateContent (Gemini REST API)
 * Format request:  contents → parts → text
 * Format response: candidates[0].content.parts[0].text
 *
 * Konfigurasi diambil dari config/services.php → services.gemini.*
 * yang nilainya berasal dari file .env:
 * - GEMINI_API_KEY  → API key dari Google AI Studio
 * - GEMINI_MODEL    → model yang digunakan (default: gemini-2.0-flash)
 */
class AIService
{
    /** @var string API key untuk autentikasi ke Gemini */
    protected string $apiKey;

    /** @var string Model Gemini yang digunakan */
    protected string $model;

    /** @var int Timeout request dalam detik */
    protected int $timeout;

    /** @var string Base URL endpoint Gemini API */
    protected string $baseUrl;

    /**
     * Inisialisasi AIService.
     * Mengambil konfigurasi dari config/services.php (sumber: .env).
     */
    public function __construct()
    {
        $this->apiKey  = config('services.gemini.key', '');
        $this->model   = config('services.gemini.model', 'gemini-2.0-flash');
        $this->timeout = (int) config('services.gemini.timeout', 60);
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';
    }

    /**
     * Cek apakah API key Gemini sudah dikonfigurasi.
     *
     * @return bool True jika API key tidak kosong
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Perbaiki teks artikel menjadi bahasa ilmiah formal.
     *
     * Mengirim teks ke Gemini API dengan prompt bahasa Indonesia
     * untuk mengubah bahasa informal menjadi akademik.
     *
     * @param  string $text Teks artikel yang akan diperbaiki
     * @return array  ['status', 'result', 'message']
     */
    public function improveText(string $text): array
    {
        // Cek API key
        if (!$this->isConfigured()) {
            return [
                'status'  => 'error',
                'result'  => '',
                'message' => 'API key Gemini belum dikonfigurasi. Tambahkan GEMINI_API_KEY di file .env.',
            ];
        }

        // Batasi panjang teks (Gemini mendukung teks panjang, tapi kita batasi untuk kecepatan)
        $maxChars = 5000;
        if (mb_strlen($text) > $maxChars) {
            $text = mb_substr($text, 0, $maxChars);
        }

        // Bangun prompt dalam bahasa Indonesia
        $prompt = $this->buildPrompt($text);

        // Kirim ke Gemini API
        return $this->sendRequest($prompt);
    }

    /**
     * Parafrasa satu kalimat menjadi versi ilmiah formal.
     *
     * @param  string $sentence Kalimat yang akan diparafrasa
     * @return array  ['status', 'result', 'message']
     */
    public function paraphraseSentence(string $sentence): array
    {
        if (!$this->isConfigured()) {
            return [
                'status'  => 'error',
                'result'  => '',
                'message' => 'API key Gemini belum dikonfigurasi.',
            ];
        }

        $prompt = "Parafrasa kalimat berikut menjadi bahasa Indonesia ilmiah yang formal dan baku. "
                . "Pertahankan makna asli. Gunakan ejaan sesuai PUEBI. "
                . "Kembalikan HANYA kalimat hasil parafrasa tanpa penjelasan.\n\n"
                . "Kalimat: " . $sentence;

        return $this->sendRequest($prompt);
    }

    /**
     * Bangun prompt lengkap untuk perbaikan teks ilmiah.
     *
     * @param  string $text Teks yang akan diperbaiki
     * @return string Prompt lengkap dengan instruksi
     */
    private function buildPrompt(string $text): string
    {
        return <<<PROMPT
Kamu adalah asisten penulisan artikel ilmiah dalam bahasa Indonesia.

Tugas: Perbaiki teks berikut menjadi bahasa ilmiah formal.

Aturan:
1. Ganti semua kata tidak baku (gak, banget, emang, kayak, lu, gue, dll.) menjadi kata baku
2. Ubah kalimat informal menjadi formal dengan gaya penulisan ilmiah
3. Gunakan kalimat pasif jika memungkinkan (hindari "saya", "aku")
4. Perbaiki struktur kalimat agar jelas dan efektif
5. Pertahankan makna asli dari teks
6. Gunakan istilah akademik yang tepat
7. Pastikan ejaan sesuai PUEBI
8. JANGAN menambahkan informasi baru yang tidak ada di teks asli
9. Kembalikan HANYA teks yang sudah diperbaiki, tanpa penjelasan tambahan

Teks yang perlu diperbaiki:

{$text}
PROMPT;
    }

    /**
     * Kirim request ke Gemini generateContent endpoint.
     *
     * Format request body:
     * {
     *   "contents": [{
     *     "parts": [{"text": "...prompt..."}]
     *   }],
     *   "generationConfig": { "temperature": 0.3 }
     * }
     *
     * Format response yang di-parse:
     * candidates[0].content.parts[0].text
     *
     * @param  string $prompt Teks prompt yang akan dikirim
     * @return array  ['status', 'result', 'message']
     */
    private function sendRequest(string $prompt): array
    {
        // Bangun URL endpoint: /models/{model}:generateContent?key={apiKey}
        $url = "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}";
        
        $maxRetries = 3;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                if ($attempt > 1) {
                    sleep(2); // Jeda 2 detik sebelum retry
                }

                // Kirim POST request dengan format Gemini API
                $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                    ])
                    ->withoutVerifying()   // Bypass SSL cert untuk Laragon development
                    ->connectTimeout(10)   // Timeout koneksi: 10 detik
                    ->timeout($this->timeout) // Timeout response: dari config
                    ->post($url, [
                        // Format Gemini: contents → parts → text
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $prompt],
                                ],
                            ],
                        ],
                        // Konfigurasi generasi teks
                        'generationConfig' => [
                            'temperature'     => 0.3,   // Rendah → output konsisten & formal
                            'maxOutputTokens' => 2048,  // Batas panjang output
                            'topP'            => 0.8,   // Nucleus sampling
                            'topK'            => 40,    // Top-K sampling
                        ],
                    ]);

                // --- Cek HTTP error ---
                if ($response->failed()) {
                    $errorBody = $response->json();
                    $errorMsg  = $errorBody['error']['message']
                              ?? 'Terjadi kesalahan saat menghubungi Gemini API.';

                    Log::warning("Gemini API Error (Attempt $attempt/$maxRetries)", [
                        'status' => $response->status(),
                        'error'  => $errorMsg,
                    ]);

                    continue; // Coba lagi
                }

                // --- Parse response ---
                $data = $response->json();

                // Ambil teks dari: candidates[0].content.parts[0].text
                $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

                // Cek jika response kosong atau tidak sesuai format
                if (empty($content)) {
                    // Cek apakah ada block reason (konten diblokir safety filter)
                    $blockReason = $data['candidates'][0]['finishReason'] ?? null;
                    if ($blockReason === 'SAFETY') {
                        return [
                            'status'  => 'error',
                            'result'  => '',
                            'message' => 'Konten diblokir oleh filter keamanan Gemini.',
                        ];
                    }

                    Log::warning("Gemini Empty Response (Attempt $attempt/$maxRetries)", ['data' => $data]);
                    continue; // Coba lagi
                }

                // --- Sukses ---
                return [
                    'status'  => 'success',
                    'result'  => trim($content),
                    'message' => null,
                ];

            } catch (\Exception $e) {
                Log::warning("AIService Exception (Attempt $attempt/$maxRetries)", ['msg' => $e->getMessage()]);
                continue; // Coba lagi
            }
        }

        // --- Fallback (Jika semua retry gagal) ---
        Log::error('Gemini API Failed after all retries.');

        $fallbackText = "AI sedang sibuk, berikut versi perbaikan sederhana:\n\n" .
                        "Pastikan artikel Anda menggunakan kata baku dan kalimat efektif sesuai PUEBI. " .
                        "Hindari penggunaan kata ganti orang pertama seperti 'saya' atau 'aku', dan " .
                        "gunakan bentuk pasif untuk memberikan kesan objektif pada penulisan ilmiah Anda.";

        return [
            'status'  => 'fallback',
            'result'  => $fallbackText,
            'message' => 'AI sedang sibuk, sistem menggunakan mode alternatif.',
        ];
    }
}
