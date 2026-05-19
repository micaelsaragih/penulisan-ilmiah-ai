@extends('layouts.app')

@section('title', 'Beranda — Sistem Pembimbing Artikel Ilmiah')

@section('content')
<style>
    .input-toggle {
        display: flex;
        background: var(--gray-100);
        border-radius: var(--radius);
        padding: 0.375rem;
        margin-bottom: 1.5rem;
    }
    .toggle-btn {
        flex: 1;
        padding: 0.75rem;
        border: none;
        background: transparent;
        font-weight: 600;
        color: var(--gray-500);
        border-radius: 8px;
        cursor: pointer;
        transition: all var(--transition);
        font-size: 0.9375rem;
    }
    .toggle-btn.active {
        background: #fff;
        color: var(--unimed-green-dark);
        box-shadow: var(--shadow-sm);
    }
    
    .file-upload-wrapper {
        position: relative;
        width: 100%;
    }
    .file-input {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 2;
    }
    .file-upload-box {
        border: 2px dashed var(--gray-300);
        border-radius: var(--radius);
        padding: 3rem 1.5rem;
        text-align: center;
        background: var(--gray-50);
        transition: all var(--transition);
    }
    .file-input:hover + .file-upload-box {
        border-color: #34d399; /* primary-light fallback */
        background: var(--primary-bg);
    }
    .file-upload-box.has-file {
        border-color: var(--unimed-green);
        background: var(--primary-bg);
        border-style: solid;
    }
    
    .main-form-card {
        max-width: 700px;
        margin: 2rem auto;
        border: none;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        border-radius: 20px;
    }
    
    .form-submit-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-top: 1.5rem;
    }

    /* ===== Progress Bar ===== */
    .progress-bar-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: var(--gray-200);
        overflow: hidden;
        display: none;
        z-index: 10;
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
    }
    .progress-bar {
        width: 50%;
        height: 100%;
        background: var(--unimed-green);
        animation: indeterminate 1.5s infinite ease-in-out;
        transform-origin: 0% 50%;
    }
    @keyframes indeterminate {
        0% { transform: translateX(-100%) scaleX(0.2); }
        50% { transform: translateX(0%) scaleX(0.5); }
        100% { transform: translateX(200%) scaleX(0.2); }
    }

    @keyframes spinner-border {
        to { transform: rotate(360deg); }
    }
    @keyframes fadeSlideIn {
        from { opacity: 0; transform: translateY(-5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .alert {
        animation: fadeSlideIn 0.4s ease-out;
    }
</style>

<div class="container" style="display: flex; align-items: center; justify-content: center; min-height: calc(100vh - 200px);">
    <div class="card animate-in main-form-card" style="width: 100%; position: relative;">
        
        <!-- Loading Progress Bar -->
        <div class="progress-bar-container" id="progress-bar-container">
            <div class="progress-bar"></div>
        </div>

        <div class="card-header" style="flex-direction: column; text-align: center; border-bottom: none; padding: 2.5rem 1.5rem 1rem;">
            <div style="width: 56px; height: 56px; background: var(--primary-bg); color: var(--unimed-green); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin: 0 auto 1rem;">
                <i class="bi bi-file-earmark-text-fill"></i>
            </div>
            <h2 style="font-size: 1.75rem; color: var(--gray-900); font-weight: 800;">Analisis Artikel Ilmiah</h2>
            <p style="color: var(--gray-500); font-size: 0.9375rem; margin-top: 0.5rem; max-width: 500px; margin-left: auto; margin-right: auto;">
                Unggah dokumen atau ketik langsung artikel Anda untuk dianalisis secara otomatis sesuai standar PUEBI dan format penulisan ilmiah.
            </p>
        </div>
        
        <div class="card-body" style="padding: 0 2rem 2.5rem;">
            @if($errors->any())
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        @foreach($errors->all() as $error)
                            window.showToast("{{ $error }}", "error");
                        @endforeach
                    });
                </script>
            @endif

            <form action="{{ route('analyze') }}" method="POST" id="analyze-form" enctype="multipart/form-data">
                @csrf
                
                <!-- Toggle Switch -->
                <div class="input-toggle">
                    <button type="button" class="toggle-btn active" id="btn-manual">
                        <i class="bi bi-keyboard"></i> Tulis Manual
                    </button>
                    <button type="button" class="toggle-btn" id="btn-upload">
                        <i class="bi bi-cloud-upload"></i> Upload Dokumen
                    </button>
                </div>
                
                <!-- Manual Input Section -->
                <div id="section-manual" style="display: block;">
                    <textarea name="text" id="text" placeholder="Ketik atau tempel artikel ilmiah Anda di sini..." style="min-height: 250px; font-size: 0.9375rem;">{{ old('text') }}</textarea>
                    <div class="input-hint" style="margin-top: 0.5rem; justify-content: space-between;">
                        <span><i class="bi bi-info-circle"></i> Minimal 10 karakter.</span>
                        <span id="char-count">0 karakter</span>
                    </div>
                </div>
                
                <!-- File Upload Section -->
                <div id="section-upload" style="display: none;">
                    @if(class_exists('ZipArchive') && extension_loaded('zip'))
                        <div class="file-upload-wrapper">
                            <input type="file" name="document" id="document" accept=".docx" class="file-input">
                            <div class="file-upload-box" id="upload-box">
                                <i class="bi bi-file-earmark-word" id="upload-icon" style="font-size: 3rem; color: var(--unimed-green);"></i>
                                <p id="upload-title" style="margin-top: 1rem; font-weight: 600; color: var(--gray-800); font-size: 1.0625rem;">Drag & Drop atau Klik untuk Upload</p>
                                <p id="upload-desc" style="color: var(--gray-500); font-size: 0.875rem; margin-top: 0.25rem;">Hanya format dokumen .docx (Max. 2MB)</p>
                                
                                <div id="file-info" style="display: none; margin-top: 1rem; padding: 0.75rem; background: #fff; border-radius: 8px; border: 1px solid var(--unimed-green); display: flex; align-items: center; justify-content: space-between; gap: 1rem; max-width: 400px; margin-left: auto; margin-right: auto; box-shadow: var(--shadow-sm); z-index: 10; position: relative;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; overflow: hidden;">
                                        <i class="bi bi-file-word-fill" style="color: #2b579a; font-size: 1.25rem;"></i>
                                        <span id="file-name-display" style="font-weight: 600; color: var(--unimed-green-dark); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 0.875rem;"></span>
                                    </div>
                                    <button type="button" id="btn-remove-file" style="background: none; border: none; color: var(--danger); cursor: pointer; padding: 4px; display: flex; align-items: center;">
                                        <i class="bi bi-x-circle-fill" style="font-size: 1.125rem;"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning" style="border-radius: 12px;">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <div>Fitur upload dinonaktifkan sementara karena ekstensi <strong>zip</strong> belum aktif di web server Anda. Silakan gunakan input teks manual.</div>
                        </div>
                    @endif
                </div>

                <div class="form-submit-container">
                    <button type="submit" class="btn btn-primary" id="btn-analyze" style="width: 100%; max-width: 300px; padding: 1rem; font-size: 1rem; border-radius: 12px; justify-content: center;">
                        <i class="bi bi-magic"></i> Analisis Artikel
                    </button>
                    <div id="loading-info" style="display: none; width: 100%; text-align: center; font-size: 0.8125rem; color: var(--gray-500); margin-top: 0.75rem; animation: fadeSlideIn 0.5s ease-out;">
                        <i class="bi bi-info-circle"></i> Proses AI sedang berjalan (±10-30 detik). Harap tunggu...
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnManual = document.getElementById('btn-manual');
        const btnUpload = document.getElementById('btn-upload');
        const sectionManual = document.getElementById('section-manual');
        const sectionUpload = document.getElementById('section-upload');
        const textInput = document.getElementById('text');
        const fileInput = document.getElementById('document');
        const fileInfo = document.getElementById('file-info');
        const fileNameDisplay = document.getElementById('file-name-display');
        const uploadBox = document.getElementById('upload-box');
        const btnRemoveFile = document.getElementById('btn-remove-file');
        const form = document.getElementById('analyze-form');
        const charCount = document.getElementById('char-count');
        const uploadIcon = document.getElementById('upload-icon');
        const uploadTitle = document.getElementById('upload-title');
        const uploadDesc = document.getElementById('upload-desc');

        // Mode Tracker
        let currentMode = 'manual';

        // Toggle Logic
        btnManual.addEventListener('click', () => {
            currentMode = 'manual';
            btnManual.classList.add('active');
            btnUpload.classList.remove('active');
            sectionManual.style.display = 'block';
            sectionUpload.style.display = 'none';
            // Bersihkan file jika kembali ke manual
            if (fileInput) fileInput.value = '';
            resetUploadUI();
        });

        btnUpload.addEventListener('click', () => {
            currentMode = 'upload';
            btnUpload.classList.add('active');
            btnManual.classList.remove('active');
            sectionUpload.style.display = 'block';
            sectionManual.style.display = 'none';
            // Bersihkan teks jika berpindah ke upload
            textInput.value = '';
            updateCharCount();
        });

        // Live Character Count
        function updateCharCount() {
            const length = textInput.value.length;
            charCount.textContent = length + ' karakter';
        }
        textInput.addEventListener('input', updateCharCount);
        updateCharCount(); // inisialisasi

        // File Upload UX
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                if (this.files && this.files[0]) {
                    const fileName = this.files[0].name;
                    fileNameDisplay.textContent = fileName;
                    fileInfo.style.display = 'flex';
                    uploadBox.classList.add('has-file');
                    
                    // Sembunyikan instruksi
                    uploadIcon.style.display = 'none';
                    uploadTitle.style.display = 'none';
                    uploadDesc.style.display = 'none';
                } else {
                    resetUploadUI();
                }
            });

            btnRemoveFile.addEventListener('click', function(e) {
                e.preventDefault(); 
                e.stopPropagation(); // Mencegah klik file input
                fileInput.value = '';
                resetUploadUI();
            });
        }

        function resetUploadUI() {
            if (!fileInput) return;
            fileInfo.style.display = 'none';
            uploadBox.classList.remove('has-file');
            uploadIcon.style.display = 'inline-block';
            uploadTitle.style.display = 'block';
            uploadDesc.style.display = 'block';
        }

        // Form Validation sebelum Submit
        form.addEventListener('submit', function(e) {
            let isValid = true;
            let errorMessage = '';

            if (currentMode === 'manual') {
                if (textInput.value.trim().length < 10) {
                    isValid = false;
                    errorMessage = 'Silakan ketik artikel minimal 10 karakter.';
                    textInput.focus();
                }
            } else if (currentMode === 'upload') {
                if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                    isValid = false;
                    errorMessage = 'Silakan unggah dokumen .docx terlebih dahulu.';
                }
            }

            if (!isValid) {
                e.preventDefault();
                window.showToast(errorMessage, 'error');
                if (typeof window.playClickSound === 'function') window.playClickSound(); // Error sound if needed, but click works
                return;
            }

            // Mainkan Suara Klik Halus
            if (typeof window.playClickSound === 'function') window.playClickSound();

            // Mencegah multiple submit & tampilkan efek loading
            const btnAnalyze = document.getElementById('btn-analyze');
            const loadingInfo = document.getElementById('loading-info');
            const progressBar = document.getElementById('progress-bar-container');
            
            btnAnalyze.disabled = true;
            btnAnalyze.style.cursor = 'not-allowed';
            btnAnalyze.style.opacity = '0.9';
            btnAnalyze.innerHTML = '<span style="width: 1.25rem; height: 1.25rem; border: 0.2em solid currentColor; border-right-color: transparent; border-radius: 50%; animation: spinner-border .75s linear infinite; display: inline-block;"></span> Memproses...';
            
            loadingInfo.style.display = 'block';
            if (progressBar) progressBar.style.display = 'block';
        });
        
        // Cek jika error validasi dari server terkait dokumen, kembalikan ke tab upload
        @if(old('document') || $errors->has('document'))
            btnUpload.click();
        @endif
    });
</script>
@endsection
