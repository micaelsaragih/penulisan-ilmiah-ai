<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TextAnalyzer;
use App\Services\AIService;
use App\Services\ScoreService;
use App\Services\DocumentParserService;

/**
 * ArticleController
 *
 * Controller ini menangani semua request terkait analisis artikel ilmiah.
 * Menggunakan prinsip Single Responsibility — controller hanya bertanggung
 * jawab untuk menerima request dan mengembalikan response.
 *
 * Logika bisnis didelegasikan ke service layer:
 * - TextAnalyzer: untuk analisis teks rule-based (struktur, bahasa, skor)
 * - AIService: untuk perbaikan teks berbasis Google Gemini API
 *
 * Routes yang ditangani:
 * - GET  /         → Menampilkan halaman utama (form input artikel)
 * - POST /analyze  → Memproses dan menganalisis teks artikel
 */
class ArticleController extends Controller
{
    /**
     * Instance TextAnalyzer service.
     *
     * @var TextAnalyzer
     */
    protected TextAnalyzer $textAnalyzer;

    /**
     * Instance AIService.
     *
     * @var AIService
     */
    protected AIService $aiService;

    /**
     * Instance ScoreService.
     *
     * @var ScoreService
     */
    protected ScoreService $scoreService;

    /**
     * Instance DocumentParserService.
     *
     * @var DocumentParserService
     */
    protected DocumentParserService $documentParserService;

    /**
     * Inisialisasi controller dengan dependency injection.
     *
     * Laravel secara otomatis me-resolve dependency melalui
     * Service Container, sehingga kita tidak perlu membuat
     * instance secara manual.
     *
     * @param  TextAnalyzer          $textAnalyzer          Service untuk analisis teks
     * @param  AIService             $aiService             Service untuk integrasi AI
     * @param  ScoreService          $scoreService          Service untuk perhitungan skor
     * @param  DocumentParserService $documentParserService Service parsing dokumen docx
     */
    public function __construct(
        TextAnalyzer $textAnalyzer, 
        AIService $aiService, 
        ScoreService $scoreService,
        DocumentParserService $documentParserService
    ) {
        $this->textAnalyzer = $textAnalyzer;
        $this->aiService = $aiService;
        $this->scoreService = $scoreService;
        $this->documentParserService = $documentParserService;
    }

    /**
     * Tampilkan halaman utama aplikasi.
     *
     * Menampilkan form input di mana pengguna dapat memasukkan
     * atau menempelkan teks artikel ilmiah mereka untuk dianalisis.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mengembalikan view halaman utama
        return view('home');
    }

    /**
     * Proses analisis teks artikel.
     *
     * Menerima teks dari form, menjalankan:
     * 1. Analisis rule-based melalui TextAnalyzer
     *    → errors, warnings, score, statistics, structure
     * 2. Perbaikan teks melalui AIService (jika API key dikonfigurasi)
     *    → versi perbaikan AI dalam bahasa ilmiah formal
     *
     * @param  Request  $request  HTTP request yang berisi teks artikel
     * @return \Illuminate\View\View
     */
    public function analyze(Request $request)
    {
        // Validasi input — teks opsional karena pengguna bisa upload file
        $validated = $request->validate([
            'document' => 'nullable|file|mimes:docx|max:2048',
            'text'     => 'nullable|string',
        ], [
            'document.mimes' => 'File harus berupa dokumen Word (.docx).',
            'document.max'   => 'Ukuran file dokumen maksimal 2MB.',
        ]);

        $text = '';

        // Prioritas 1: Baca file dokumen jika di-upload
        if ($request->hasFile('document') && $request->file('document')->isValid()) {
            try {
                $filePath = $request->file('document')->getPathname();
                $text = $this->documentParserService->extractTextFromDocx($filePath);
                
                if (empty(trim($text))) {
                    return back()->withInput()->withErrors([
                        'document' => 'Dokumen yang Anda upload kosong atau teksnya tidak dapat dibaca.'
                    ]);
                }
            } catch (\Exception $e) {
                return back()->withInput()->withErrors([
                    'document' => $e->getMessage()
                ]);
            }
        } else {
            // Prioritas 2: Gunakan input teks manual jika tidak ada file
            $text = $validated['text'] ?? '';
            
            if (empty(trim($text))) {
                return back()->withInput()->withErrors([
                    'text' => 'Silakan upload dokumen Word (.docx) ATAU tempelkan teks artikel Anda.'
                ]);
            }
            if (mb_strlen(trim($text)) < 10) {
                return back()->withInput()->withErrors([
                    'text' => 'Teks artikel minimal 10 karakter.'
                ]);
            }
        }

        // 1. Jalankan analisis teks melalui TextAnalyzer service
        //    Mengembalikan: errors, warnings, statistics, structure
        $analysis = $this->textAnalyzer->analyze($text);

        // 2. Hitung skor menggunakan ScoreService
        //    Mengembalikan: struktur, bahasa, final_score, kategori
        $scoring = $this->scoreService->calculate(
            $analysis['structure'], 
            $analysis['errors'], 
            $analysis['warnings']
        );

        // 3. Jalankan perbaikan AI melalui AIService (jika dikonfigurasi)
        //    Mengembalikan: success, improved_text, error
        $aiResult = null;
        if ($this->aiService->isConfigured()) {
            $aiResult = $this->aiService->improveText($text);
        }

        // Gabungkan semua hasil untuk ditampilkan di view
        $results = array_merge(
            ['text' => $text],
            $analysis,
            ['score_data' => $scoring], // Menyimpan data skor dari ScoreService
            [
                'ai_configured' => $this->aiService->isConfigured(),
                'ai_result'     => $aiResult,
            ]
        );

        // Kembalikan view hasil dengan data analisis
        return view('results', compact('results'));
    }
}
