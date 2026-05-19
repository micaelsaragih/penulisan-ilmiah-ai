<?php

namespace App\Services;

/**
 * ScoreService
 *
 * Service ini menangani logika penilaian untuk artikel ilmiah.
 * Memisahkan tanggung jawab kalkulasi nilai agar kode lebih clean.
 */
class ScoreService
{
    /**
     * Hitung semua skor berdasarkan hasil analisis.
     *
     * @param array $structure Hasil analisis struktur (true/false)
     * @param array $errors    Daftar kesalahan yang ditemukan
     * @param array $warnings  Daftar peringatan yang ditemukan
     * @return array  Data nilai yang terdiri dari struktur, bahasa, final_score, dan kategori
     */
    public function calculate(array $structure, array $errors, array $warnings): array
    {
        $strukturScore = $this->calculateStructureScore($structure);
        $bahasaScore   = $this->calculateLanguageScore($errors, $warnings);
        
        // Final score: rata-rata dari struktur dan bahasa
        $finalScore = (int) round(($strukturScore + $bahasaScore) / 2);
        
        $kategori = $this->determineCategory($finalScore);

        return [
            'struktur'    => $strukturScore,
            'bahasa'      => $bahasaScore,
            'final_score' => $finalScore,
            'kategori'    => $kategori,
        ];
    }

    /**
     * Logika penilaian Struktur:
     * - Awal: 100
     * - Setiap bagian yang hilang: -20 poin
     */
    private function calculateStructureScore(array $structure): int
    {
        $score = 100;
        
        foreach ($structure as $found) {
            if (!$found) {
                $score -= 20;
            }
        }
        
        return max(0, $score);
    }

    /**
     * Logika penilaian Bahasa:
     * - Awal: 100
     * - Setiap kata tidak baku: -5 poin
     * - Setiap kalimat terlalu panjang: -3 poin
     */
    private function calculateLanguageScore(array $errors, array $warnings): int
    {
        $score = 100;

        // Kata tidak baku ada di $errors dengan type 'language'
        foreach ($errors as $error) {
            if ($error['type'] === 'language') {
                $score -= 5;
            }
        }

        // Kalimat terlalu panjang ada di $warnings dengan type 'readability'
        foreach ($warnings as $warning) {
            if ($warning['type'] === 'readability') {
                // Peringatan kalimat panjang individual diawali dengan "Kalimat ke-"
                if (str_starts_with($warning['message'], 'Kalimat ke-')) {
                    $score -= 3;
                }
            }
        }

        return max(0, $score);
    }

    /**
     * Tentukan kategori berdasarkan nilai akhir.
     * 80-100: Baik
     * 60-79: Cukup
     * <60: Perlu Perbaikan
     */
    private function determineCategory(int $score): string
    {
        if ($score >= 80) {
            return 'Baik';
        }
        
        if ($score >= 60) {
            return 'Cukup';
        }
        
        return 'Perlu Perbaikan';
    }
}
