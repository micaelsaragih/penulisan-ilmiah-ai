@extends('layouts.app')

@section('title', 'Petunjuk Penggunaan — Sistem Pembimbing Artikel Ilmiah')

@section('content')
<div class="container" style="min-height: calc(100vh - 200px);">
    <section class="hero" style="padding-bottom: 1rem;">
        <h1>Petunjuk <span style="color: var(--unimed-green);">Penggunaan</span></h1>
        <p style="margin-top: 0.5rem;">Cara menggunakan Sistem Pembimbing Penulisan Artikel Ilmiah.</p>
    </section>

    <div class="card animate-in" style="max-width: 800px; margin: 0 auto; border-radius: var(--radius-lg);">
        <div class="card-header">
            <span class="card-header-icon" style="background: #e0f2fe; color: #0284c7;">
                <i class="bi bi-journal-text"></i>
            </span>
            <h2>Langkah-Langkah Analisis</h2>
        </div>
        <div class="card-body">
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                
                <div style="display: flex; gap: 1rem;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary-bg); color: var(--unimed-green); display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0;">1</div>
                    <div>
                        <h3 style="font-size: 1.0625rem; color: var(--gray-800); margin-bottom: 0.25rem;">Siapkan Artikel Anda</h3>
                        <p style="color: var(--gray-600); font-size: 0.9375rem; line-height: 1.6;">Anda bisa menggunakan dokumen Microsoft Word (format .docx) atau langsung menyiapkan teks yang sudah Anda ketik untuk disalin-tempel.</p>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary-bg); color: var(--unimed-green); display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0;">2</div>
                    <div>
                        <h3 style="font-size: 1.0625rem; color: var(--gray-800); margin-bottom: 0.25rem;">Pilih Mode Input</h3>
                        <p style="color: var(--gray-600); font-size: 0.9375rem; line-height: 1.6;">Di halaman Beranda, pilih mode <strong>Tulis Manual</strong> jika ingin mem-paste teks langsung, atau pilih <strong>Upload Dokumen</strong> jika ingin mengunggah file .docx.</p>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary-bg); color: var(--unimed-green); display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0;">3</div>
                    <div>
                        <h3 style="font-size: 1.0625rem; color: var(--gray-800); margin-bottom: 0.25rem;">Mulai Analisis</h3>
                        <p style="color: var(--gray-600); font-size: 0.9375rem; line-height: 1.6;">Tekan tombol hijau <strong>"Analisis Artikel"</strong>. Sistem akan mengevaluasi kelengkapan struktur (IMRAD), mendeteksi kesalahan tata bahasa, serta menghubungi AI untuk memberikan saran penulisan ulang sesuai standar PUEBI.</p>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary-bg); color: var(--unimed-green); display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0;">4</div>
                    <div>
                        <h3 style="font-size: 1.0625rem; color: var(--gray-800); margin-bottom: 0.25rem;">Tinjau Hasil</h3>
                        <p style="color: var(--gray-600); font-size: 0.9375rem; line-height: 1.6;">Setelah proses selesai (kurang lebih 10-30 detik), Anda akan dialihkan ke halaman laporan. Tinjau skor, peringatan bahasa, dan salin hasil perbaikan AI jika diperlukan.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
