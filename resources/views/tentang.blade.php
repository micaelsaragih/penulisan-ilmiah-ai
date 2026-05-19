@extends('layouts.app')

@section('title', 'Tentang Aplikasi — Sistem Pembimbing Artikel Ilmiah')

@section('content')
<div class="container" style="min-height: calc(100vh - 200px);">
    <section class="hero" style="padding-bottom: 1rem;">
        <h1>Tentang <span style="color: var(--unimed-green);">Aplikasi</span></h1>
        <p style="margin-top: 0.5rem;">Informasi tentang Sistem Pembimbing Penulisan Artikel Ilmiah UNIMED.</p>
    </section>

    <div class="card animate-in" style="max-width: 800px; margin: 0 auto; border-radius: var(--radius-lg);">
        <div class="card-header">
            <span class="card-header-icon" style="background: #fef3c7; color: #d97706;">
                <i class="bi bi-info-circle-fill"></i>
            </span>
            <h2>Deskripsi & Tujuan</h2>
        </div>
        <div class="card-body">
            <div style="font-size: 0.9375rem; color: var(--gray-700); line-height: 1.8;">
                <p style="margin-bottom: 1rem;">
                    <strong>Sistem Pembimbing Penulisan Artikel Ilmiah</strong> adalah aplikasi berbasis web yang dirancang khusus untuk sivitas akademika Universitas Negeri Medan (UNIMED).
                </p>
                <p style="margin-bottom: 1rem;">
                    Tujuan utama dari aplikasi ini adalah untuk membantu mahasiswa, dosen, maupun peneliti dalam mengkoreksi dan menyempurnakan draf artikel ilmiah mereka sebelum disubmit ke jurnal nasional maupun internasional.
                </p>
                <p style="margin-bottom: 1rem;">
                    Aplikasi ini memadukan <strong>Pendeteksi Aturan Berbasis Teks (Rule-based NLP)</strong> untuk memastikan kelengkapan komponen artikel (Abstrak, Pendahuluan, Metode, Hasil, Pembahasan, Kesimpulan) dengan <strong>Kecerdasan Buatan (Generative AI)</strong> dari Google Gemini untuk merekomendasikan perbaikan tata bahasa yang kaku menjadi gaya bahasa ilmiah yang luwes dan sesuai dengan Pedoman Umum Ejaan Bahasa Indonesia (PUEBI).
                </p>
                
                <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--gray-200); text-align: center;">
                    <p style="font-weight: 600; color: var(--gray-900);">&copy; {{ date('Y') }} Universitas Negeri Medan</p>
                    <p style="font-size: 0.8125rem; color: var(--gray-500); margin-top: 0.25rem;">Dikembangkan untuk kemajuan riset dan publikasi ilmiah.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
