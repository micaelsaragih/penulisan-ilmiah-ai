{{--
    Halaman Hasil Analisis
    Menampilkan hasil analisis rule-based dari TextAnalyzer.
    Data diterima melalui variabel $results dari ArticleController@analyze.

    Struktur $results:
    - text       → teks asli yang dianalisis
    - errors     → array kesalahan (type, message)
    - warnings   → array peringatan (type, message)
    - score      → skor kualitas 0–100
    - statistics → word_count, sentence_count, paragraph_count, char_count
    - structure  → status tiap bagian artikel (true/false)
--}}
@extends('layouts.app')

@section('title', 'Hasil Analisis — Sistem Pembimbing Artikel Ilmiah')

@section('content')
<style>
    .professional-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        margin-top: 2rem;
    }
    @media (min-width: 768px) {
        .professional-grid {
            grid-template-columns: 1fr 1fr;
        }
        .full-width {
            grid-column: 1 / -1;
        }
    }
    
    .summary-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--gray-100);
    }
    .stat-item {
        flex: 1;
        min-width: 100px;
        text-align: center;
        background: var(--gray-50);
        padding: 1rem;
        border-radius: var(--radius);
        border: 1px solid var(--gray-200);
    }
    .stat-item .value {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--unimed-green-dark);
    }
    .stat-item .label {
        font-size: 0.75rem;
        color: var(--gray-500);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-top: 0.25rem;
    }
    
    .score-circle.good { background: linear-gradient(135deg, var(--unimed-green), var(--unimed-green-dark)); }
    .score-circle.fair { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .score-circle.poor { background: linear-gradient(135deg, var(--unimed-red), #991b1b); }
    
    .score-bar.good { background: linear-gradient(90deg, var(--unimed-green), var(--unimed-green-dark)); }
    .score-bar.fair { background: linear-gradient(90deg, #f59e0b, #d97706); }
    .score-bar.poor { background: linear-gradient(90deg, var(--unimed-red), #991b1b); }
    
    .issue-tabs {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
        border-bottom: 1px solid var(--gray-200);
        padding-bottom: 0.5rem;
    }
    .issue-tab {
        background: none;
        border: none;
        font-weight: 600;
        color: var(--gray-500);
        cursor: pointer;
        padding: 0.5rem 0;
        position: relative;
    }
    .issue-tab.active {
        color: var(--unimed-green-dark);
    }
    .issue-tab.active::after {
        content: '';
        position: absolute;
        bottom: -0.6rem;
        left: 0;
        width: 100%;
        height: 3px;
        background: var(--unimed-green);
        border-radius: 3px 3px 0 0;
    }
    
    .custom-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.1rem 0.5rem;
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: 700;
        margin-left: 0.5rem;
    }
    .custom-badge.red { background: #fee2e2; color: #b91c1c; }
    .custom-badge.yellow { background: #fef3c7; color: #b45309; }
</style>

<div class="container">
    <section class="hero" style="padding-bottom: 0;">
        <h1>Laporan Hasil <span style="color: var(--unimed-green);">Analisis</span></h1>
        <p style="margin-top: 0.5rem;">Evaluasi komprehensif terhadap kualitas penulisan artikel ilmiah Anda.</p>
    </section>

    <div class="professional-grid">

        {{-- ==========================================
             CARD 1: RINGKASAN (FULL WIDTH)
             ========================================== --}}
        @php
            $scoring = $results['score_data'];
            $finalScore = $scoring['final_score'];
            $scoreStatus = $finalScore >= 80 ? 'good' : ($finalScore >= 60 ? 'fair' : 'poor');
        @endphp
        <div class="card animate-in full-width">
            <div class="card-header">
                <span class="card-header-icon" style="background: var(--primary-bg); color: var(--unimed-green);">
                    <i class="bi bi-pie-chart-fill"></i>
                </span>
                <h2>Ringkasan Eksekutif</h2>
            </div>
            <div class="card-body">
                <div class="score-display">
                    <div class="score-circle {{ $scoreStatus }}">
                        <span class="score-number">{{ $finalScore }}</span>
                        <span class="score-max">/ 100</span>
                    </div>
                    <div class="score-info">
                        <div class="score-label" style="font-size: 1.5rem;">{{ $scoring['kategori'] }}</div>
                        <div class="score-detail" style="display: flex; gap: 1.5rem; margin-top: 0.5rem; margin-bottom: 0.75rem;">
                            <div>
                                <span style="font-size: 0.875rem; color: var(--gray-500);">Skor Struktur:</span>
                                <strong style="color: var(--gray-800); font-size: 1.125rem;">{{ $scoring['struktur'] }}</strong>
                            </div>
                            <div>
                                <span style="font-size: 0.875rem; color: var(--gray-500);">Skor Bahasa:</span>
                                <strong style="color: var(--gray-800); font-size: 1.125rem;">{{ $scoring['bahasa'] }}</strong>
                            </div>
                        </div>
                        <div class="score-bar-container" style="height: 10px;">
                            <div class="score-bar {{ $scoreStatus }}" style="width: {{ $finalScore }}%"></div>
                        </div>
                    </div>
                </div>
                
                {{-- Statistik dimasukkan ke Ringkasan --}}
                <div class="summary-stats">
                    <div class="stat-item">
                        <div class="value">{{ number_format($results['statistics']['word_count']) }}</div>
                        <div class="label">Kata</div>
                    </div>
                    <div class="stat-item">
                        <div class="value">{{ number_format($results['statistics']['sentence_count']) }}</div>
                        <div class="label">Kalimat</div>
                    </div>
                    <div class="stat-item">
                        <div class="value">{{ number_format($results['statistics']['paragraph_count']) }}</div>
                        <div class="label">Paragraf</div>
                    </div>
                    <div class="stat-item">
                        <div class="value">{{ number_format($results['statistics']['char_count']) }}</div>
                        <div class="label">Karakter</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==========================================
             CARD 2: STRUKTUR (SETENGAH WIDTH)
             ========================================== --}}
        <div class="card animate-in">
            <div class="card-header">
                <span class="card-header-icon" style="background: #e0f2fe; color: #0284c7;">
                    <i class="bi bi-layout-text-window-reverse"></i>
                </span>
                <h2>Kelengkapan Struktur</h2>
            </div>
            <div class="card-body">
                <p style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 1rem;">
                    Pengecekan komponen wajib dalam artikel ilmiah standar (IMRAD).
                </p>
                <ul class="check-list" style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach($results['structure'] as $sectionName => $found)
                        <li class="check-item {{ $found ? 'found' : 'missing' }}" style="background: var(--gray-50); border: 1px solid var(--gray-200); padding: 0.75rem 1rem;">
                            <i class="bi {{ $found ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}" style="font-size: 1.125rem;"></i>
                            <span style="flex: 1; font-weight: 600;">{{ $sectionName }}</span>
                            @if($found)
                                <span class="badge" style="background: #dcfce7; color: #166534;">Ada</span>
                            @else
                                <span class="badge" style="background: #fee2e2; color: #991b1b;">Tidak Ditemukan</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- ==========================================
             CARD 3: BAHASA & EJAAN (SETENGAH WIDTH)
             ========================================== --}}
        <div class="card animate-in">
            <div class="card-header">
                <span class="card-header-icon" style="background: #fef3c7; color: #d97706;">
                    <i class="bi bi-spellcheck"></i>
                </span>
                <h2>Analisis Bahasa</h2>
            </div>
            <div class="card-body">
                <div class="issue-tabs">
                    <button class="issue-tab active" id="tab-errors">
                        Kesalahan Fatal <span class="custom-badge red">{{ count($results['errors']) }}</span>
                    </button>
                    <button class="issue-tab" id="tab-warnings">
                        Saran Perbaikan <span class="custom-badge yellow">{{ count($results['warnings']) }}</span>
                    </button>
                </div>
                
                {{-- Panel Kesalahan --}}
                <div id="panel-errors">
                    @if(count($results['errors']) > 0)
                        <div class="suggestion-list" style="max-height: 350px; overflow-y: auto; padding-right: 0.5rem;">
                            @foreach($results['errors'] as $error)
                                <div class="suggestion-item error" style="background: #fff; border-left: 4px solid var(--unimed-red);">
                                    <i class="bi bi-exclamation-octagon-fill" style="color: var(--unimed-red);"></i>
                                    <span>{{ $error['message'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="bi bi-shield-check" style="color: var(--unimed-green); font-size: 2rem;"></i>
                            <p style="margin-top: 0.5rem; font-weight: 600; color: var(--gray-800);">Bagus Sekali!</p>
                            <p style="color: var(--gray-500); font-size: 0.875rem;">Tidak ada kesalahan bahasa fatal ditemukan.</p>
                        </div>
                    @endif
                </div>

                {{-- Panel Saran (Sembunyi by default) --}}
                <div id="panel-warnings" style="display: none;">
                    @if(count($results['warnings']) > 0)
                        <div class="suggestion-list" style="max-height: 350px; overflow-y: auto; padding-right: 0.5rem;">
                            @foreach($results['warnings'] as $warning)
                                <div class="suggestion-item warning" style="background: #fff; border-left: 4px solid #f59e0b;">
                                    <i class="bi bi-exclamation-triangle-fill" style="color: #f59e0b;"></i>
                                    <span>{{ $warning['message'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="bi bi-hand-thumbs-up-fill" style="color: var(--unimed-green); font-size: 2rem;"></i>
                            <p style="margin-top: 0.5rem; font-weight: 600; color: var(--gray-800);">Sempurna!</p>
                            <p style="color: var(--gray-500); font-size: 0.875rem;">Tidak ada saran perbaikan gaya bahasa.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ==========================================
             CARD 4: VERSI PERBAIKAN AI (FULL WIDTH)
             ========================================== --}}
        <div class="card animate-in full-width">
            <div class="card-header" style="justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <span class="card-header-icon" style="background: #f3e8ff; color: #7e22ce;">
                        <i class="bi bi-robot"></i>
                    </span>
                    <h2>Versi Perbaikan AI (Sesuai PUEBI)</h2>
                </div>
                @if($results['ai_configured'] && $results['ai_result'])
                    @if($results['ai_result']['status'] === 'success')
                        <span class="badge badge-green" style="font-size: 0.875rem; padding: 0.25rem 0.75rem;"><i class="bi bi-check2-circle"></i> Berhasil Diproses</span>
                    @elseif($results['ai_result']['status'] === 'fallback')
                        <span class="badge badge-amber" style="font-size: 0.875rem; padding: 0.25rem 0.75rem;"><i class="bi bi-info-circle"></i> Mode Fallback</span>
                    @endif
                @endif
            </div>
            <div class="card-body">
                @if(!$results['ai_configured'])
                    <div class="ai-notice">
                        <div class="ai-notice-icon"><i class="bi bi-key-fill"></i></div>
                        <div class="ai-notice-content">
                            <div class="ai-notice-title">Integrasi AI Belum Aktif</div>
                            <div class="ai-notice-text">Tambahkan API key Gemini di file <code>.env</code> untuk otomatis memperbaiki tata bahasa artikel Anda.</div>
                        </div>
                    </div>
                @elseif($results['ai_result'] && in_array($results['ai_result']['status'], ['success', 'fallback']))
                    @if($results['ai_result']['status'] === 'fallback')
                        <div class="alert alert-warning" style="border-radius: 8px;">
                            <i class="bi bi-info-circle-fill"></i>
                            <span>{{ $results['ai_result']['message'] }}</span>
                        </div>
                    @endif
                    
                    <div class="ai-improved-text" style="font-size: 1rem; padding: 1.5rem; background: var(--gray-50); border: 1px solid var(--gray-200); border-radius: var(--radius); border-left: 5px solid #7e22ce;">
                        {!! nl2br(e($results['ai_result']['result'])) !!}
                    </div>
                    
                    <div class="ai-actions" style="margin-top: 1.5rem; display: flex; justify-content: flex-end;">
                        <button class="btn btn-primary" id="btn-copy-ai" onclick="copyAIText()" style="background: linear-gradient(135deg, #7e22ce, #6b21a8); box-shadow: 0 4px 12px rgba(126, 34, 206, 0.2);">
                            <i class="bi bi-clipboard-check"></i> Salin Hasil AI
                        </button>
                    </div>
                @elseif($results['ai_result'] && $results['ai_result']['status'] === 'error')
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span>{{ $results['ai_result']['message'] }}</span>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- Tombol kembali --}}
    <div style="text-align: center; margin-top: 3rem;">
        <a href="{{ route('home') }}" class="btn btn-secondary" style="padding: 0.875rem 2rem; border-radius: 100px;">
            <i class="bi bi-arrow-left"></i> Analisis Artikel Lain
        </a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabErrors = document.getElementById('tab-errors');
        const tabWarnings = document.getElementById('tab-warnings');
        const panelErrors = document.getElementById('panel-errors');
        const panelWarnings = document.getElementById('panel-warnings');

        if (tabErrors && tabWarnings) {
            tabErrors.addEventListener('click', () => {
                tabErrors.classList.add('active');
                tabWarnings.classList.remove('active');
                panelErrors.style.display = 'block';
                panelWarnings.style.display = 'none';
            });

            tabWarnings.addEventListener('click', () => {
                tabWarnings.classList.add('active');
                tabErrors.classList.remove('active');
                panelWarnings.style.display = 'block';
                panelErrors.style.display = 'none';
            });
        }
    });

    function copyAIText() {
        const text = document.querySelector('.ai-improved-text')?.innerText;
        if (!text) return;

        navigator.clipboard.writeText(text).then(() => {
            const btn = document.getElementById('btn-copy-ai');
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-check2-all"></i> Tersalin!';
            btn.style.background = 'var(--unimed-green)';
            setTimeout(() => {
                btn.innerHTML = originalHTML;
                btn.style.background = 'linear-gradient(135deg, #7e22ce, #6b21a8)';
            }, 2500);
        });
    }
</script>
@endsection
