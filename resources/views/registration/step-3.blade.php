@extends('layouts.app')

@section('title', 'Tahap 3/4 - SMK TI BALI GLOBAL BADUNG')
@section('html_class', 'h-full bg-slate-50')
@section('body_class', 'min-h-full flex flex-col text-slate-800 antialiased selection:bg-blue-600 selection:text-white')

@section('content')
    <div
      id="upload-doc-modal"
      class="fixed inset-0 z-[70] hidden bg-slate-900/60 items-center justify-center p-4"
      role="dialog"
      aria-modal="true"
      aria-labelledby="upload-doc-modal-title"
      onclick="if (event.target === this) closeUploadPreview();"
    >
      <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50 shrink-0">
          <h2 id="upload-doc-modal-title" class="text-sm font-bold text-slate-900">Pratinjau Dokumen</h2>
          <button
            type="button"
            onclick="closeUploadPreview()"
            title="Tutup"
            class="text-slate-400 hover:text-slate-700 p-2 rounded-xl hover:bg-slate-200 transition"
          >
            <img src="{{ asset('assets/img/icon/close.svg') }}" alt="Tutup" class="w-5 h-5 object-contain" />
          </button>
        </div>
        <div id="upload-doc-modal-body" class="p-4 sm:p-6 overflow-y-auto flex-1 text-center"></div>
      </div>
    </div>

    <!-- Main Content Container -->
    <main
      class="flex-1 py-4 sm:py-5 px-3.5 sm:px-6 max-w-3xl mx-auto w-full no-print"
    >
      <!-- CARD PENDAFTARAN (HEADER, STEPPER & FORMULIR) -->
      <div
        class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 p-4 sm:py-6 sm:px-7 shadow-xl shadow-slate-200/50"
      >
        @include('registration.partials.stepper', [
          'currentStep' => 3,
          'description' => 'Unggah berkas dokumen persyaratan pendaftaran wajib dalam format file yang jelas dan terbaca.',
        ])

        <!-- ============================================================ -->
        <!-- STEP 3: UNGGAH BERKAS PERSYARATAN (INTERAKTIF & WAJIB)        -->
        <!-- ============================================================ -->
        <form
          id="form-step-3"
          action="{{ route('pendaftaran.step3.post') }}"
          method="POST"
          enctype="multipart/form-data"
          class="mt-2.5 sm:mt-3 pt-2.5 sm:pt-3 border-t border-slate-100 space-y-5"
        >
          @if ($errors->any())
            <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
              <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          @php
            $mandatoryDocs = [
                'pasfoto' => [
                    'field' => 'berkas_foto',
                    'icon' => 'photo.svg',
                    'alt' => 'Pasfoto',
                    'title' => 'Pasfoto Siswa (Formal)',
                    'desc' => 'Ukuran 3x4 latar polos (merah/biru), format JPG/PNG/WEBP. Maks. 10 MB.',
                    'accept' => 'image/jpeg,image/png,image/webp,image/jpg',
                    'always_image' => true,
                ],
                'kk' => [
                    'field' => 'berkas_kk',
                    'icon' => 'document-text.svg',
                    'alt' => 'KK',
                    'title' => 'Kartu Keluarga (KK)',
                    'desc' => 'Scan atau foto jelas KK asli seluruh anggota keluarga. Format JPG/PNG/WEBP/PDF. Maks. 10 MB.',
                    'accept' => 'image/jpeg,image/png,image/webp,image/jpg,.pdf,application/pdf',
                    'always_image' => false,
                ],
                'akta' => [
                    'field' => 'berkas_akta',
                    'icon' => 'check-circle.svg',
                    'alt' => 'Akta',
                    'title' => 'Akta Kelahiran Asli',
                    'desc' => 'Scan atau foto Akta Kelahiran resmi dari Dukcapil / SKL. Format JPG/PNG/WEBP/PDF. Maks. 10 MB.',
                    'accept' => 'image/jpeg,image/png,image/webp,image/jpg,.pdf,application/pdf',
                    'always_image' => false,
                ],
            ];
          @endphp

          <!-- 3 Slot Unggah Berkas Wajib -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach ($mandatoryDocs as $type => $doc)
              @php
                $draftItem = $berkasDraft[$doc['field']] ?? null;
                $hasDraft = $draftItem !== null;
                $isImageFlag = $doc['always_image']
                    ? '1'
                    : ($hasDraft && !str_ends_with(strtolower($draftItem['path_file']), '.pdf') ? '1' : '');
              @endphp
              <div
                id="card-upload-{{ $type }}"
                class="p-4 bg-slate-50/70 hover:bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 hover:border-blue-400 transition flex flex-col justify-between"
              >
                <div>
                  <div class="flex items-center justify-between mb-2">
                    <div
                      class="w-8 h-8 rounded-lg bg-blue-600 border-2 border-white flex items-center justify-center shrink-0"
                    >
                      <img
                        src="{{ asset('assets/img/icon/' . $doc['icon']) }}"
                        alt="{{ $doc['alt'] }}"
                        class="w-4 h-4 object-contain shrink-0 brightness-0 invert"
                      />
                    </div>
                    @if ($hasDraft)
                      <span
                        id="badge-status-{{ $type }}"
                        class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200"
                      >
                        <img src="{{ asset('assets/img/icon/check-green.svg') }}" alt="" class="w-3 h-3 object-contain" />
                        <span>Terunggah</span>
                      </span>
                    @else
                      <span
                        id="badge-status-{{ $type }}"
                        class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200"
                      >
                        Wajib diunggah
                      </span>
                    @endif
                  </div>
                  <h4 class="text-sm font-bold text-slate-900 leading-snug">
                    {{ $doc['title'] }} <span class="text-red-500">*</span>
                  </h4>
                  <p class="text-xs sm:text-[12.5px] text-slate-500 mt-0.5 leading-snug">
                    {{ $doc['desc'] }}
                  </p>

                  <div
                    id="info-{{ $type }}"
                    class="mt-2 text-[10.5px] text-slate-500 min-h-[20px] truncate"
                  >
                    @if ($hasDraft)
                      <span class="inline-flex items-center gap-1 text-emerald-700 font-medium max-w-full">
                        <img src="{{ asset('assets/img/icon/check-green.svg') }}" alt="" class="w-3 h-3 object-contain shrink-0" />
                        <span class="truncate">{{ $draftItem['nama_file'] }}</span>
                      </span>
                    @else
                      <span class="text-slate-400 italic">Belum ada berkas dipilih</span>
                    @endif
                  </div>
                </div>

                <!-- Buttons Container -->
                <div
                  class="mt-3 pt-2.5 border-t border-slate-200/60 flex items-center gap-1.5"
                >
                  <label
                    class="flex-1 text-center cursor-pointer py-1.5 px-2.5 bg-white border border-slate-200 hover:border-slate-300 rounded-lg text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-50 transition flex items-center justify-center gap-1.5"
                  >
                    <img
                      src="{{ asset('assets/img/icon/upload.svg') }}"
                      alt=""
                      class="w-3.5 h-3.5 object-contain shrink-0"
                    />
                    <span id="btn-text-{{ $type }}">{{ $hasDraft ? 'Ganti File' : 'Upload' }}</span>
                    <input
                      type="file"
                      id="input-file-{{ $type }}"
                      name="{{ $doc['field'] }}"
                      accept="{{ $doc['accept'] }}"
                      class="sr-only"
                      @required(!$hasDraft)
                      onchange="handleFileChange(event, '{{ $type }}')"
                    />
                  </label>
                  <button
                    type="button"
                    onclick="previewUpload('{{ $type }}')"
                    data-saved-url="{{ $hasDraft ? asset($draftItem['path_file']) : '' }}"
                    data-fallback-url="{{ $hasDraft ? route('storage.berkas', ['filename' => basename($draftItem['path_file'])]) : '' }}"
                    data-is-image="{{ $isImageFlag }}"
                    data-file-name="{{ $draftItem['nama_file'] ?? '' }}"
                    id="btn-check-{{ $type }}"
                    class="{{ $hasDraft ? 'flex' : 'hidden' }} py-1.5 px-2.5 bg-blue-50 border border-blue-200 hover:bg-blue-100 text-blue-700 rounded-lg text-xs sm:text-sm font-semibold transition items-center gap-1"
                  >
                    <img
                      src="{{ asset('assets/img/icon/eye.svg') }}"
                      alt=""
                      class="w-3.5 h-3.5 object-contain shrink-0"
                    />
                    <span>Lihat</span>
                  </button>
                </div>
              </div>
            @endforeach
          </div>

          @php
            $additionalDocs = array_filter($requiredDocs ?? [], function($k) {
              return !in_array($k, ['berkas_foto', 'berkas_kk', 'berkas_akta']);
            }, ARRAY_FILTER_USE_KEY);
          @endphp

          @if(!empty($additionalDocs))
            <!-- Berkas Pendukung Sesuai Jalur / Keringanan Khusus SPMB -->
            <div class="pt-4 border-t border-slate-200/80">
              <div class="mb-3">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                  <img src="{{ asset('assets/img/icon/document-text.svg') }}" alt="" class="w-4 h-4 object-contain" />
                  <span>Berkas Pendukung Jalur / Keringanan Khusus</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                  Unggah berkas bukti sesuai jalur atau voucher pendaftaran yang Anda pilih (format gambar/PDF maks. 10 MB).
                </p>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($additionalDocs as $docKey => $doc)
                  <div class="p-4 bg-slate-50/70 hover:bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 hover:border-blue-400 transition flex flex-col justify-between">
                    <div>
                      <div class="flex items-center justify-between mb-2">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 border-2 border-white flex items-center justify-center shrink-0">
                          <img src="{{ asset($doc['icon'] ?? 'assets/img/icon/document-text.svg') }}" alt="" class="w-4 h-4 object-contain shrink-0 brightness-0 invert" />
                        </div>
                        @if (isset($berkasDraft[$docKey]))
                          <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <img src="{{ asset('assets/img/icon/check-green.svg') }}" alt="" class="w-3 h-3 object-contain" />
                            <span>Terunggah</span>
                          </span>
                        @else
                          <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                            Persyaratan
                          </span>
                        @endif
                      </div>

                      <h4 class="text-sm font-bold text-slate-900 leading-snug">{{ $doc['title'] }}</h4>
                      <p class="text-xs text-slate-500 mt-0.5 leading-snug">{{ $doc['desc'] }}</p>

                      <div class="mt-2 text-[10.5px] text-slate-500 min-h-[20px] truncate">
                        @if (isset($berkasDraft[$docKey]))
                          <span class="inline-flex items-center gap-1 text-emerald-700 font-medium max-w-full">
                            <img src="{{ asset('assets/img/icon/check-green.svg') }}" alt="" class="w-3 h-3 object-contain shrink-0" />
                            <span class="truncate">{{ $berkasDraft[$docKey]['nama_file'] }}</span>
                          </span>
                        @else
                          <span class="text-slate-400 italic">Belum ada berkas dipilih</span>
                        @endif
                      </div>
                    </div>

                    <div class="mt-3 pt-2.5 border-t border-slate-200/60 flex items-center gap-1.5">
                      <label class="flex-1 text-center cursor-pointer py-1.5 px-2.5 bg-white border border-slate-200 hover:border-slate-300 rounded-lg text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-50 transition flex items-center justify-center gap-1.5">
                        <img src="{{ asset('assets/img/icon/upload.svg') }}" alt="" class="w-3.5 h-3.5 object-contain shrink-0" />
                        <span>{{ isset($berkasDraft[$docKey]) ? 'Ganti File' : 'Upload' }}</span>
                        <input
                          type="file"
                          name="{{ $docKey }}"
                          accept="{{ $doc['accept'] ?? 'image/*,.pdf' }}"
                          class="sr-only"
                          onchange="handleDynamicFileChange(event, this)"
                        />
                      </label>
                      @if (isset($berkasDraft[$docKey]))
                        <button
                          type="button"
                          onclick="previewUploadUrl('{{ asset($berkasDraft[$docKey]['path_file']) }}', '{{ $doc['title'] }}')"
                          class="py-1.5 px-2.5 bg-blue-50 border border-blue-200 hover:bg-blue-100 text-blue-700 rounded-lg text-xs sm:text-sm font-semibold transition flex items-center gap-1"
                        >
                          <img src="{{ asset('assets/img/icon/eye.svg') }}" alt="" class="w-3.5 h-3.5 object-contain shrink-0" />
                          <span>Lihat</span>
                        </button>
                      @endif
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          @endif

          <!-- Petunjuk Validasi Berkas -->
          <div
            class="p-3 sm:p-3.5 bg-blue-50/60 border border-blue-200 rounded-xl flex items-start gap-2.5 text-sm text-blue-900 leading-relaxed"
          >
            <img
              src="{{ asset('assets/img/icon/info-circle.svg') }}"
              alt="Info"
              class="w-4 h-4 object-contain shrink-0 mt-0.5"
            />
            <div>
              <span class="font-bold">Ketentuan Unggah Berkas:</span>
              <p class="text-xs sm:text-sm text-blue-800 mt-0.5">
                Pastikan dokumen terbaca dengan jelas dan tidak buram. Berkas yang sudah diunggah akan otomatis tersimpan dalam pendaftaran Anda.
              </p>
            </div>
          </div>

          <!-- Navigation Buttons -->
          <div class="pt-3 sm:pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
            <a
              href="{{ route('pendaftaran.step2') }}"
              class="px-4 py-2.5 sm:px-5 sm:py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm sm:text-base rounded-xl transition flex items-center gap-1.5 cursor-pointer"
            >
              <img
                src="{{ asset('assets/img/icon/arrow-left.svg') }}"
                alt=""
                class="w-4 h-4 object-contain shrink-0"
              />
              <span>Kembali</span>
            </a>
            <button
              type="submit"
              class="px-5 py-2.5 sm:px-6 sm:py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm sm:text-base rounded-xl shadow-md shadow-blue-600/20 hover:shadow-lg hover:shadow-blue-600/25 transition flex items-center gap-2 cursor-pointer"
            >
              <span>Lanjut ke Verifikasi</span>
              <img
                src="{{ asset('assets/img/icon/arrow-right-white.svg') }}"
                alt=""
                class="w-4 h-4 object-contain shrink-0"
              />
            </button>
          </div>

          @csrf
        </form>
      </div>
    </main>

    @include('partials.footer-panitia')
@endsection

@push('scripts')
    <script>
      function handleFileChange(event, type) {
        const file = event.target.files[0];
        if (!file) return;

        const maxSize = 10 * 1024 * 1024; // 10MB
        if (file.size > maxSize) {
          if (typeof window.showToast === 'function') {
            window.showToast('Ukuran file maksimal adalah 10 MB.', 'warning');
          } else {
            alert('Ukuran file maksimal adalah 10 MB.');
          }
          event.target.value = '';
          return;
        }

        const info = document.getElementById('info-' + type);
        const btnText = document.getElementById('btn-text-' + type);
        const btnCheck = document.getElementById('btn-check-' + type);
        const card = document.getElementById('card-upload-' + type);
        const badge = document.getElementById('badge-status-' + type);

        if (info) {
          info.innerHTML = '<span class="text-blue-600 font-semibold truncate block">Siap diunggah: ' + file.name + '</span>';
        }
        if (btnText) btnText.innerText = 'Ganti File';

        if (btnCheck) {
          btnCheck.classList.remove('hidden');
          btnCheck.classList.add('flex');
          btnCheck.setAttribute('data-file-name', file.name);
          btnCheck.setAttribute('data-is-image', file.type.startsWith('image/') ? '1' : '');

          const reader = new FileReader();
          reader.onload = function(e) {
            btnCheck.setAttribute('data-saved-url', e.target.result);
          };
          reader.readAsDataURL(file);
        }

        if (badge) {
          badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200';
          badge.innerText = 'Dipilih';
        }
        if (card) {
          card.classList.remove('border-slate-200');
          card.classList.add('border-blue-400');
        }
      }

      function previewUpload(type) {
        const btn = document.getElementById('btn-check-' + type);
        if (!btn) return;

        const url = btn.getAttribute('data-saved-url');
        const fallbackUrl = btn.getAttribute('data-fallback-url');
        const isImage = btn.getAttribute('data-is-image') === '1';
        const fileName = btn.getAttribute('data-file-name') || 'Dokumen Persyaratan';

        const modal = document.getElementById('upload-doc-modal');
        const modalTitle = document.getElementById('upload-doc-modal-title');
        const modalBody = document.getElementById('upload-doc-modal-body');

        if (!modal || !modalTitle || !modalBody) return;

        modalTitle.innerText = fileName;
        modalBody.innerHTML = '';

        if (!url) {
          modalBody.innerHTML = '<p class="text-sm text-slate-500 py-12">Belum ada file yang diunggah.</p>';
        } else if (isImage) {
          const img = document.createElement('img');
          img.src = url;
          img.alt = fileName;
          img.className = 'max-w-full max-h-[70vh] rounded-2xl mx-auto shadow-md border border-slate-100 object-contain';
          if (fallbackUrl) {
            img.onerror = function() {
              if (this.src !== fallbackUrl) {
                this.src = fallbackUrl;
              }
            };
          }
          modalBody.appendChild(img);
        } else {
          modalBody.innerHTML = `
            <div class="flex flex-col items-center justify-center p-8 text-center space-y-4">
              <div class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <img src="{{ asset('assets/img/icon/pdf.svg') }}" alt="PDF" class="w-8 h-8 object-contain" />
              </div>
              <div>
                <h4 class="font-bold text-slate-800">${fileName}</h4>
                <p class="text-xs text-slate-500 mt-1">Dokumen format PDF</p>
              </div>
              <div class="flex items-center gap-2">
                <a href="${url}" target="_blank" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs transition flex items-center gap-1.5">
                  <img src="{{ asset('assets/img/icon/external-link.svg') }}" alt="" class="w-4 h-4 object-contain brightness-0 invert" />
                  <span>Buka Tab Baru</span>
                </a>
                <a href="${url}" download="${fileName}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition flex items-center gap-1.5">
                  <img src="{{ asset('assets/img/icon/download.svg') }}" alt="" class="w-4 h-4 object-contain" />
                  <span>Unduh File</span>
                </a>
              </div>
            </div>
          `;
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
      }

      function previewUploadUrl(url, title) {
        const modal = document.getElementById('upload-doc-modal');
        const modalTitle = document.getElementById('upload-doc-modal-title');
        const modalBody = document.getElementById('upload-doc-modal-body');

        if (!modal || !modalTitle || !modalBody) return;

        modalTitle.innerText = title;
        modalBody.innerHTML = '';

        const isPdf = url.toLowerCase().endsWith('.pdf');
        if (isPdf) {
          modalBody.innerHTML = `
            <div class="flex flex-col items-center justify-center p-8 text-center space-y-4">
              <div class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <img src="{{ asset('assets/img/icon/pdf.svg') }}" alt="PDF" class="w-8 h-8 object-contain" />
              </div>
              <div>
                <h4 class="font-bold text-slate-800">${title}</h4>
                <p class="text-xs text-slate-500 mt-1">Dokumen format PDF</p>
              </div>
              <div class="flex items-center gap-2">
                <a href="${url}" target="_blank" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs transition flex items-center gap-1.5">
                  <img src="{{ asset('assets/img/icon/external-link.svg') }}" alt="" class="w-4 h-4 object-contain brightness-0 invert" />
                  <span>Buka Tab Baru</span>
                </a>
                <a href="${url}" download class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition flex items-center gap-1.5">
                  <img src="{{ asset('assets/img/icon/download.svg') }}" alt="" class="w-4 h-4 object-contain" />
                  <span>Unduh File</span>
                </a>
              </div>
            </div>
          `;
        } else {
          const img = document.createElement('img');
          img.src = url;
          img.alt = title;
          img.className = 'max-w-full max-h-[70vh] rounded-2xl mx-auto shadow-md border border-slate-100 object-contain';
          modalBody.appendChild(img);
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
      }

      function closeUploadPreview() {
        const modal = document.getElementById('upload-doc-modal');
        if (modal) {
          modal.classList.add('hidden');
          modal.classList.remove('flex');
        }
      }

      function handleDynamicFileChange(event, input) {
        const file = event.target.files[0];
        if (!file) return;

        const maxSize = 10 * 1024 * 1024;
        if (file.size > maxSize) {
          if (typeof window.showToast === 'function') {
            window.showToast('Ukuran file maksimal adalah 10 MB.', 'warning');
          } else {
            alert('Ukuran file maksimal adalah 10 MB.');
          }
          event.target.value = '';
          return;
        }

        const card = input.closest('.rounded-2xl');
        if (!card) return;

        const infoEl = card.querySelector('.truncate');
        if (infoEl) {
          infoEl.innerHTML = '<span class="text-blue-600 font-semibold truncate block">Siap diunggah: ' + file.name + '</span>';
        }

        const badgeEl = card.querySelector('.rounded-full');
        if (badgeEl) {
          badgeEl.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200';
          badgeEl.innerText = 'Dipilih';
        }

        const btnLabel = card.querySelector('label span');
        if (btnLabel) {
          btnLabel.innerText = 'Ubah File';
        }
      }

      document.addEventListener('keydown', event => {
        if (event.key === 'Escape') closeUploadPreview();
      });

      // Validasi form & visual loading saat submit
      document.getElementById('form-step-3')?.addEventListener('submit', function(e) {
        const requiredDocuments = [
          { input: document.getElementById('input-file-pasfoto'), card: 'card-upload-pasfoto', label: 'Pasfoto Formal Siswa', hasDraft: "{{ isset($berkasDraft['berkas_foto']) ? '1' : '0' }}" === '1' },
          { input: document.getElementById('input-file-kk'), card: 'card-upload-kk', label: 'Kartu Keluarga (KK)', hasDraft: "{{ isset($berkasDraft['berkas_kk']) ? '1' : '0' }}" === '1' },
          { input: document.getElementById('input-file-akta'), card: 'card-upload-akta', label: 'Akta Kelahiran Asli', hasDraft: "{{ isset($berkasDraft['berkas_akta']) ? '1' : '0' }}" === '1' },
        ];
        const missingDocument = requiredDocuments.find(({ input, hasDraft }) => !hasDraft && (!input || input.files.length === 0));

        if (missingDocument) {
          e.preventDefault();
          const message = 'Mohon unggah ' + missingDocument.label + ' terlebih dahulu.';
          if (typeof window.showToast === 'function') { window.showToast(message, 'error'); } else { alert(message); }
          document.getElementById(missingDocument.card)?.scrollIntoView({ behavior: 'smooth', block: 'center' });
          return false;
        }

        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
          submitBtn.innerHTML = '<span>Mengunggah Berkas...</span>';
        }
      });
    </script>
@endpush
