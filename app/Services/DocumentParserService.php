<?php

namespace App\Services;

use Exception;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Log;

/**
 * DocumentParserService
 *
 * Service ini menangani proses parsing dan ekstraksi teks dari dokumen.
 * Dibuat khusus untuk menghindari penumpukan logika di Controller
 * dan mempermudah pemeliharaan atau penambahan format dokumen di masa depan.
 */
class DocumentParserService
{
    /**
     * Ekstrak teks lengkap dari file dokumen Word (.docx).
     *
     * @param string $filePath Path lengkap menuju file sementara yang diupload
     * @return string
     * @throws Exception Jika terjadi kesalahan saat membaca atau parsing dokumen
     */
    public function extractTextFromDocx(string $filePath): string
    {
        // Pengecekan krusial: PHPWord butuh ZipArchive untuk membaca .docx
        if (!extension_loaded('zip') || !class_exists('ZipArchive')) {
            throw new Exception(
                'Ekstensi PHP "zip" (ZipArchive) belum aktif di web server Anda. ' .
                'Harap aktifkan ekstensi ini di file php.ini agar fitur upload dokumen Word dapat digunakan.'
            );
        }

        try {
            // Memuat file docx menggunakan library PhpWord
            $phpWord = IOFactory::load($filePath, 'Word2007');
            $text = '';

            // Iterasi semua bagian dokumen
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    
                    // Jika elemen adalah elemen TextRun (biasanya paragraf)
                    if (method_exists($element, 'getElements')) {
                        $paragraphText = '';
                        foreach ($element->getElements() as $subElement) {
                            if (method_exists($subElement, 'getText')) {
                                $paragraphText .= $subElement->getText();
                            }
                        }
                        if (!empty(trim($paragraphText))) {
                            $text .= $paragraphText . "\n\n";
                        }
                    } 
                    // Jika elemen langsung berupa teks biasa
                    elseif (method_exists($element, 'getText')) {
                        $plainText = $element->getText();
                        if (!empty(trim($plainText))) {
                            $text .= $plainText . "\n\n";
                        }
                    }
                }
            }

            return trim($text);

        } catch (Exception $e) {
            Log::error('Gagal mengekstrak teks DOCX', ['error' => $e->getMessage()]);
            throw new Exception('Gagal membaca isi dokumen. Pastikan file bukan format corrupt dan dapat dibuka normal.');
        }
    }
}
