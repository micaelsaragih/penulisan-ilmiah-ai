<?php

namespace App\Services;

/**
 * TextAnalyzer Service
 *
 * Service ini bertanggung jawab untuk menganalisis teks artikel ilmiah
 * menggunakan pendekatan rule-based (tanpa AI).
 *
 * Fungsi utamanya meliputi:
 * - Menghitung statistik teks (kata, kalimat, paragraf, karakter)
 * - Menganalisis struktur artikel (Abstrak, Pendahuluan, Metode, Hasil, Kesimpulan)
 * - Mendeteksi penggunaan bahasa tidak baku
 * - Mendeteksi kalimat yang terlalu panjang
 * - Menghitung skor kualitas artikel secara keseluruhan
 *
 * Output utama berupa array dengan kunci:
 * - errors    → kesalahan serius yang harus diperbaiki
 * - warnings  → peringatan yang sebaiknya diperhatikan
 * - statistics → data statistik teks
 * - structure  → status kelengkapan bagian artikel
 */
class TextAnalyzer
{
    /**
     * Bagian-bagian wajib dalam artikel ilmiah.
     * Digunakan oleh checkStructure() untuk validasi kelengkapan.
     */
    private const REQUIRED_SECTIONS = [
        'Abstrak'      => '/\b(abstrak|abstract)\b/i',
        'Pendahuluan'  => '/\b(pendahuluan|latar\s+belakang|introduction)\b/i',
        'Metode'       => '/\b(metode|metodologi|method|methodology)\b/i',
        'Hasil'        => '/\b(hasil|result|findings|temuan)\b/i',
        'Kesimpulan'   => '/\b(kesimpulan|simpulan|conclusion)\b/i',
    ];

    /**
     * Daftar kata tidak baku beserta padanan bakunya.
     * Digunakan oleh checkLanguage() untuk deteksi bahasa informal.
     */
    private const INFORMAL_WORDS = [
        'gak'    => 'tidak',
        'gue'    => 'saya / penulis',
        'lu'     => 'Anda / kamu',
        'banget' => 'sangat / sekali',
        'udah'   => 'sudah',
        'emang'  => 'memang',
        'kayak'  => 'seperti',
        'nggak'  => 'tidak',
        'gimana' => 'bagaimana',
        'aja'    => 'saja',
        'bgt'    => 'sangat',
        'yg'     => 'yang',
        'dgn'    => 'dengan',
        'tdk'    => 'tidak',
        'blm'    => 'belum',
        'krn'    => 'karena',
        'utk'    => 'untuk',
        'dll'    => 'dan lain-lain',
        'dsb'    => 'dan sebagainya',
    ];

    /**
     * Batas maksimal kata per kalimat sebelum dianggap terlalu panjang.
     */
    private const MAX_WORDS_PER_SENTENCE = 25;

    /**
     * Analisis teks artikel secara keseluruhan.
     *
     * Method utama yang merangkum semua hasil analisis menjadi
     * satu array terstruktur. Ini adalah satu-satunya method
     * yang perlu dipanggil dari controller.
     *
     * @param  string  $text  Teks artikel yang akan dianalisis
     * @return array   Hasil analisis: errors, warnings, statistics, structure
     */
    public function analyze(string $text): array
    {
        $errors   = [];
        $warnings = [];

        // 1. Hitung statistik dasar
        $statistics = $this->getStatistics($text);

        // 2. Cek kelengkapan struktur artikel → menghasilkan errors
        $structure = $this->checkStructure($text, $errors);

        // 3. Cek penggunaan bahasa → menghasilkan errors (kata tidak baku)
        $this->checkLanguage($text, $errors);

        // 4. Cek kalimat terlalu panjang → menghasilkan warnings
        $this->checkSentenceLength($text, $warnings);

        // 5. Cek tambahan: kata ganti orang pertama informal
        $this->checkFirstPersonUsage($text, $warnings);

        // 6. Cek tambahan: paragraf terlalu pendek
        $this->checkParagraphLength($text, $warnings, $statistics);

        // Catatan: Skor sekarang dihitung oleh ScoreService
        
        return [
            'errors'     => $errors,
            'warnings'   => $warnings,
            'statistics' => $statistics,
            'structure'  => $structure,
        ];
    }

    /**
     * Hitung statistik dasar dari teks.
     *
     * @param  string  $text  Teks yang akan dihitung statistiknya
     * @return array   Statistik: word_count, sentence_count, paragraph_count, char_count
     */
    public function getStatistics(string $text): array
    {
        // Menghitung jumlah kata
        $wordCount = str_word_count($text);

        // Menghitung jumlah kalimat berdasarkan tanda titik, tanya, dan seru
        $sentenceCount = preg_match_all('/[.!?]+/', $text);

        // Menghitung jumlah paragraf berdasarkan baris kosong
        $paragraphs = array_filter(
            preg_split('/\n\s*\n/', trim($text)),
            fn($p) => trim($p) !== ''
        );
        $paragraphCount = count($paragraphs);

        // Menghitung total karakter (tanpa spasi)
        $charCount = strlen(preg_replace('/\s+/', '', $text));

        return [
            'word_count'      => $wordCount,
            'sentence_count'  => $sentenceCount ?: 0,
            'paragraph_count' => max($paragraphCount, 1),
            'char_count'      => $charCount,
        ];
    }

    /**
     * Periksa kelengkapan struktur artikel ilmiah.
     *
     * Mendeteksi apakah artikel mengandung bagian-bagian wajib:
     * Abstrak, Pendahuluan, Metode, Hasil, Kesimpulan.
     * Bagian yang tidak ditemukan akan ditambahkan ke daftar errors.
     *
     * @param  string  $text    Teks artikel
     * @param  array   &$errors  Array errors yang akan ditambahi (by reference)
     * @return array   Status tiap bagian: ['Abstrak' => true/false, ...]
     */
    public function checkStructure(string $text, array &$errors): array
    {
        $structure = [];

        foreach (self::REQUIRED_SECTIONS as $sectionName => $pattern) {
            $found = (bool) preg_match($pattern, $text);
            $structure[$sectionName] = $found;

            // Jika bagian wajib tidak ditemukan, tambahkan error
            if (!$found) {
                $errors[] = [
                    'type'    => 'structure',
                    'message' => "Bagian \"{$sectionName}\" tidak ditemukan dalam artikel. "
                               . "Bagian ini wajib ada dalam artikel ilmiah.",
                ];
            }
        }

        return $structure;
    }

    /**
     * Deteksi penggunaan kata tidak baku dalam teks.
     *
     * Mencari kata-kata informal/slang dan menambahkannya
     * ke daftar errors beserta saran kata baku penggantinya.
     *
     * @param  string  $text    Teks yang akan diperiksa
     * @param  array   &$errors  Array errors yang akan ditambahi (by reference)
     * @return void
     */
    public function checkLanguage(string $text, array &$errors): void
    {
        foreach (self::INFORMAL_WORDS as $informal => $formal) {
            // Gunakan word boundary (\b) agar tidak mendeteksi substring
            // Contoh: "bagaimana" tidak match "gak"
            if (preg_match('/\b' . preg_quote($informal, '/') . '\b/i', $text)) {
                $errors[] = [
                    'type'    => 'language',
                    'message' => "Ditemukan kata tidak baku: \"{$informal}\". "
                               . "Gunakan \"{$formal}\" sebagai gantinya.",
                ];
            }
        }
    }

    /**
     * Deteksi kalimat yang terlalu panjang (lebih dari 25 kata).
     *
     * Kalimat yang terlalu panjang menurunkan keterbacaan dan
     * membuat pembaca sulit memahami isi artikel.
     *
     * @param  string  $text      Teks yang akan diperiksa
     * @param  array   &$warnings  Array warnings yang akan ditambahi (by reference)
     * @return void
     */
    public function checkSentenceLength(string $text, array &$warnings): void
    {
        // Pecah teks menjadi kalimat-kalimat berdasarkan tanda baca akhir
        $sentences = preg_split('/(?<=[.!?])\s+/', trim($text));
        $longCount = 0;

        foreach ($sentences as $index => $sentence) {
            $sentence = trim($sentence);
            if (empty($sentence)) continue;

            $wordCount = str_word_count($sentence);

            if ($wordCount > self::MAX_WORDS_PER_SENTENCE) {
                $longCount++;
                // Tampilkan preview kalimat (50 karakter pertama)
                $preview = mb_strlen($sentence) > 50
                    ? mb_substr($sentence, 0, 50) . '...'
                    : $sentence;

                $warnings[] = [
                    'type'    => 'readability',
                    'message' => "Kalimat ke-" . ($index + 1) . " terlalu panjang ({$wordCount} kata). "
                               . "Maksimal " . self::MAX_WORDS_PER_SENTENCE . " kata per kalimat. "
                               . "\"" . $preview . "\"",
                ];
            }
        }

        // Ringkasan jika banyak kalimat panjang
        if ($longCount > 3) {
            $warnings[] = [
                'type'    => 'readability',
                'message' => "Ditemukan {$longCount} kalimat yang terlalu panjang. "
                           . "Pertimbangkan untuk menyederhanakan struktur kalimat secara keseluruhan.",
            ];
        }
    }

    /**
     * Deteksi penggunaan kata ganti orang pertama informal.
     *
     * Dalam penulisan ilmiah, sebaiknya menggunakan bentuk pasif
     * atau kata "penulis" daripada "saya", "aku", dll.
     *
     * @param  string  $text      Teks yang akan diperiksa
     * @param  array   &$warnings  Array warnings yang akan ditambahi (by reference)
     * @return void
     */
    private function checkFirstPersonUsage(string $text, array &$warnings): void
    {
        // Kata ganti orang pertama yang sebaiknya dihindari
        $firstPersonWords = ['saya', 'aku', 'kami'];
        $found = [];

        foreach ($firstPersonWords as $word) {
            if (preg_match('/\b' . $word . '\b/i', $text)) {
                $found[] = $word;
            }
        }

        if (!empty($found)) {
            $wordList = '"' . implode('", "', $found) . '"';
            $warnings[] = [
                'type'    => 'style',
                'message' => "Ditemukan penggunaan kata ganti orang pertama: {$wordList}. "
                           . "Dalam penulisan ilmiah, sebaiknya gunakan bentuk pasif atau kata \"penulis\".",
            ];
        }
    }

    /**
     * Deteksi paragraf yang terlalu pendek.
     *
     * Paragraf dengan kurang dari 2 kalimat dianggap terlalu pendek
     * untuk artikel ilmiah.
     *
     * @param  string  $text        Teks yang akan diperiksa
     * @param  array   &$warnings   Array warnings yang akan ditambahi (by reference)
     * @param  array   $statistics  Statistik teks untuk konteks
     * @return void
     */
    private function checkParagraphLength(string $text, array &$warnings, array $statistics): void
    {
        $paragraphs = array_filter(
            preg_split('/\n\s*\n/', trim($text)),
            fn($p) => trim($p) !== ''
        );

        $shortCount = 0;
        foreach ($paragraphs as $index => $paragraph) {
            $wordCount = str_word_count(trim($paragraph));
            // Paragraf dengan kurang dari 20 kata dianggap terlalu pendek
            if ($wordCount < 20 && $wordCount > 0) {
                $shortCount++;
            }
        }

        if ($shortCount > 0) {
            $warnings[] = [
                'type'    => 'style',
                'message' => "Ditemukan {$shortCount} paragraf yang terlalu pendek (kurang dari 20 kata). "
                           . "Paragraf yang baik minimal terdiri dari 2–3 kalimat.",
            ];
        }

        // Peringatan jika artikel terlalu pendek secara keseluruhan
        if ($statistics['word_count'] < 500) {
            $warnings[] = [
                'type'    => 'length',
                'message' => "Artikel hanya memiliki {$statistics['word_count']} kata. "
                           . "Artikel ilmiah umumnya minimal 2.000 kata.",
            ];
        }
    }

    // Metode calculateScore telah dipindahkan ke ScoreService.php
}
