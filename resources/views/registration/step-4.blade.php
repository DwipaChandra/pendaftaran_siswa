@extends('layouts.app')

@section('title', 'Tahap 4/4 - SMK TI BALI GLOBAL BADUNG')
@section('html_class', 'h-full bg-slate-50')
@section('body_class', 'min-h-full flex flex-col text-slate-800 antialiased selection:bg-blue-600 selection:text-white')

@section('content')
    <div
      id="upload-doc-modal"
      class="fixed inset-0 z-[70] hidden bg-slate-900/60 items-center justify-center p-4 no-print"
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
          'currentStep' => 4,
          'description' => 'Periksa kembali seluruh data dan berkas sebelum mengirim pendaftaran resmi Anda.',
        ])

        <!-- ============================================================ -->
        <!-- STEP 4: VERIFIKASI DATA & BERKAS                            -->
        <!-- ============================================================ -->
        <form
          id="form-step-4"
          action="{{ route('pendaftaran.submit') }}"
          method="POST"
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

          <!-- Ringkasan Profil Singkat Siswa -->
          <div class="p-3 sm:p-4 rounded-2xl bg-blue-50/60 border border-blue-100 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-blue-600 text-white font-extrabold flex items-center justify-center text-sm shadow-md shadow-blue-600/25 shrink-0">
                {{ strtoupper(substr($calonSiswa->nama_lengkap ?? 'S', 0, 1)) }}
              </div>
              <div class="min-w-0">
                <h3 class="text-sm sm:text-base font-extrabold text-slate-900 truncate">
                  {{ $calonSiswa->nama_lengkap ?? 'Calon Siswa' }}
                </h3>
                <p class="text-xs text-slate-500 font-mono mt-0.5">
                  NISN: <span class="font-bold text-slate-700">{{ $calonSiswa->nisn ?? '-' }}</span>
                </p>
              </div>
            </div>
            <span class="shrink-0 px-2.5 py-1 rounded-full text-xs font-semibold bg-white text-blue-700 border border-blue-200">
              Final Review
            </span>
          </div>

          <!-- Section 1: Identitas Pribadi Siswa -->
          <div class="p-4 rounded-2xl border border-slate-200/90 bg-white space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
              <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                <h4 class="text-sm font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                  <img
                    src="{{ asset('assets/img/icon/user.svg') }}"
                    alt=""
                    class="w-3.5 h-3.5 object-contain shrink-0"
                  />
                  <span>1. Identitas Calon Siswa</span>
                </h4>
              </div>
              <a
                href="{{ route('pendaftaran.step1') }}"
                class="px-2 py-0.5 bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-700 text-xs sm:text-sm font-semibold rounded-lg transition flex items-center gap-1"
              >
                <img
                  src="{{ asset('assets/img/icon/pencil.svg') }}"
                  alt=""
                  class="w-3 h-3 object-contain shrink-0"
                />
                <span>Ubah</span>
              </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-3 text-sm">
              <div>
                <span class="text-xs text-slate-400 block">Tempat, Tanggal Lahir</span>
                <p class="font-semibold text-slate-800">{{ $calonSiswa->tempat_lahir ?? '-' }}, {{ $calonSiswa->tanggal_lahir ? date('d M Y', strtotime($calonSiswa->tanggal_lahir)) : '-' }}</p>
              </div>
              <div>
                <span class="text-xs text-slate-400 block">Jenis Kelamin / Agama</span>
                <p class="font-semibold text-slate-800">{{ $calonSiswa->jenis_kelamin ?? '-' }} / {{ $calonSiswa->agama ?? '-' }}</p>
              </div>
              <div>
                <span class="text-xs text-slate-400 block">Asal Sekolah</span>
                <p class="font-semibold text-slate-800">{{ $calonSiswa->asal_sekolah ?? '-' }}</p>
              </div>
              <div>
                <span class="text-xs text-slate-400 block">No. WhatsApp Siswa</span>
                <p class="font-semibold text-slate-800 tabular-nums">{{ $calonSiswa->no_whatsapp ?? '-' }}</p>
              </div>
              <div class="sm:col-span-2">
                <span class="text-xs text-slate-400 block">Alamat Lengkap Domisili</span>
                <p class="font-semibold text-slate-800 leading-relaxed">{{ $calonSiswa->alamat_domisili ?? '-' }}</p>
              </div>
            </div>
          </div>

          <!-- Section 2: Data Orang Tua / Wali & Pilihan Jurusan -->
          <div class="p-4 rounded-2xl border border-slate-200/90 bg-white space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
              <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                <h4 class="text-sm font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                  <img
                    src="{{ asset('assets/img/icon/users.svg') }}"
                    alt=""
                    class="w-3.5 h-3.5 object-contain shrink-0"
                  />
                  <span>2. Orang Tua &amp; Jurusan</span>
                </h4>
              </div>
              <a
                href="{{ route('pendaftaran.step2') }}"
                class="px-2 py-0.5 bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-700 text-xs sm:text-sm font-semibold rounded-lg transition flex items-center gap-1"
              >
                <img
                  src="{{ asset('assets/img/icon/pencil.svg') }}"
                  alt=""
                  class="w-3 h-3 object-contain shrink-0"
                />
                <span>Ubah</span>
              </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-3 text-sm">
              <div>
                <span class="text-xs text-slate-400 block">Nama &amp; Pekerjaan Ayah</span>
                <p class="font-semibold text-slate-800">{{ $calonSiswa->nama_bapak ?? ($draft['nama_bapak'] ?? '-') }} ({{ $calonSiswa->pekerjaan_bapak ?? ($draft['pekerjaan_bapak'] ?? '-') }})</p>
              </div>
              <div>
                <span class="text-xs text-slate-400 block">Nama &amp; Pekerjaan Ibu</span>
                <p class="font-semibold text-slate-800">{{ $calonSiswa->nama_ibu ?? ($draft['nama_ibu'] ?? '-') }} ({{ $calonSiswa->pekerjaan_ibu ?? ($draft['pekerjaan_ibu'] ?? '-') }})</p>
              </div>
              <div>
                <span class="text-xs text-slate-400 block">No. WhatsApp Ayah / Ibu</span>
                <p class="font-semibold text-slate-800 tabular-nums">
                  {{ $calonSiswa->no_wa_ayah ?? ($draft['no_wa_ayah'] ?? '-') }} / {{ $calonSiswa->no_wa_ibu ?? ($draft['no_wa_ibu'] ?? '-') }}
                </p>
              </div>
              <div>
                <span class="text-xs text-slate-400 block">Alamat Orang Tua / Wali</span>
                <p class="font-semibold text-slate-800 leading-relaxed">{{ $calonSiswa->alamat_ortu ?? ($draft['alamat_ortu'] ?? '-') }}</p>
              </div>
              <div>
                <span class="text-xs text-slate-400 block">Pilihan Jalur Pendaftaran</span>
                <p class="font-semibold text-slate-800">{{ $jalur->nama_jalur ?? '-' }}</p>
                @if(!empty($draft['nama_sub_jalur']))
                  <p class="text-xs text-blue-700 font-semibold mt-0.5">Kategori: {{ $draft['nama_sub_jalur'] }}</p>
                @endif
              </div>
              <div>
                <span class="text-xs text-slate-400 block">Pilihan Kompetensi Keahlian (Jurusan)</span>
                <p class="font-extrabold text-blue-700 text-base">{{ $jurusan->nama_jurusan ?? '-' }}</p>
              </div>
            </div>
          </div>

          <!-- Section 3: Berkas Persyaratan Terunggah -->
          <div class="p-4 rounded-2xl border border-slate-200/90 bg-white space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
              <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                <h4 class="text-sm font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                  <img
                    src="{{ asset('assets/img/icon/upload.svg') }}"
                    alt=""
                    class="w-3.5 h-3.5 object-contain shrink-0"
                  />
                  <span>3. Berkas Persyaratan Terunggah</span>
                </h4>
              </div>
              <a
                href="{{ route('pendaftaran.step3') }}"
                class="px-2 py-0.5 bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-700 text-xs sm:text-sm font-semibold rounded-lg transition flex items-center gap-1"
              >
                <img
                  src="{{ asset('assets/img/icon/pencil.svg') }}"
                  alt=""
                  class="w-3 h-3 object-contain shrink-0"
                />
                <span>Ubah</span>
              </a>
            </div>

            @php
              $allUploadedDocs = [
                'Pasfoto Formal Siswa' => $berkasDraft['berkas_foto'] ?? null,
                'Kartu Keluarga (KK)' => $berkasDraft['berkas_kk'] ?? null,
                'Akta Kelahiran Asli' => $berkasDraft['berkas_akta'] ?? null,
              ];
              // Tambahkan berkas pendukung jika ada
              foreach($berkasDraft as $key => $doc) {
                if(!in_array($key, ['berkas_foto', 'berkas_kk', 'berkas_akta']) && is_array($doc)) {
                  $allUploadedDocs[$doc['jenis_berkas'] ?? $key] = $doc;
                }
              }
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
              @foreach($allUploadedDocs as $title => $doc)
                @if($doc)
                  <div class="min-w-0 p-2.5 rounded-xl border border-slate-200 bg-white flex items-center gap-2">
                    <div class="w-7 h-7 shrink-0 rounded-lg bg-blue-600 border border-white flex items-center justify-center overflow-hidden">
                      @php
                        $path = $doc['path_file'] ?? '';
                        $isImage = !str_ends_with(strtolower($path), '.pdf');
                      @endphp
                      @if($isImage && $path)
                        <img
                          src="{{ asset($path) }}"
                          alt="{{ $title }}"
                          class="w-full h-full object-cover"
                          onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');"
                        />
                        <img
                          src="{{ asset('assets/img/icon/photo.svg') }}"
                          alt=""
                          class="hidden w-3.5 h-3.5 object-contain brightness-0 invert"
                        />
                      @else
                        <img
                          src="{{ asset('assets/img/icon/document-text.svg') }}"
                          alt=""
                          class="w-3.5 h-3.5 object-contain brightness-0 invert"
                        />
                      @endif
                    </div>
                    <div class="min-w-0 flex-1">
                      <p class="text-xs font-bold text-slate-800 truncate" title="{{ $title }}">
                        {{ $title }}
                      </p>
                      <p class="text-[11px] text-slate-400 truncate" title="{{ $doc['nama_file'] ?? '' }}">
                        {{ $doc['nama_file'] ?? '-' }}
                      </p>
                    </div>
                    @if(!empty($doc['path_file']))
                      <button
                        type="button"
                        data-document-preview
                        data-preview-url="{{ asset($doc['path_file']) }}"
                        data-fallback-url="{{ route('storage.berkas', ['filename' => basename($doc['path_file'])]) }}"
                        data-file-name="{{ $title }}"
                        class="shrink-0 p-1.5 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition"
                        title="Pratinjau Dokumen"
                      >
                        <img
                          src="{{ asset('assets/img/icon/eye.svg') }}"
                          alt="Lihat"
                          class="w-3.5 h-3.5 object-contain"
                        />
                      </button>
                    @endif
                  </div>
                @endif
              @endforeach
            </div>
          </div>

          <!-- Pernyataan Integritas Data & Pakta Keabsahan -->
          <div class="p-3.5 sm:p-4 rounded-2xl bg-amber-50/70 border border-amber-200 space-y-2">
            <label class="flex items-start gap-2.5 cursor-pointer">
              <input
                type="checkbox"
                name="pernyataan_benar"
                id="check-pernyataan"
                required
                class="mt-1 w-4 h-4 rounded border-amber-300 text-blue-600 focus:ring-blue-500/20 cursor-pointer shrink-0"
              />
              <p class="text-xs sm:text-[13px] text-amber-950 leading-relaxed select-none">
                <span class="font-bold">Pernyataan Kebenaran Data:</span> Saya menyatakan dengan sesungguhnya bahwa seluruh data
                dan dokumen yang saya unggah adalah benar, sah, dan dapat
                dipertanggungjawabkan secara hukum. Apabila di kemudian hari
                ditemukan pemalsuan atau ketidaksesuaian data, saya bersedia
                menerima sanksi pembatalan pendaftaran sesuai ketentuan panitia
                SPMB SMK TI Bali Global Badung.
              </p>
            </label>
          </div>

          <!-- Navigation & Final Submit Buttons -->
          <div class="pt-3 sm:pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
            <a
              href="{{ route('pendaftaran.step3') }}"
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
              id="btn-submit-form"
              class="px-5 py-2.5 sm:px-6 sm:py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm sm:text-base rounded-xl shadow-md shadow-blue-600/20 hover:shadow-lg hover:shadow-blue-600/25 transition flex items-center gap-2 cursor-pointer"
            >
              <span>Kirim Pendaftaran Resmi</span>
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
      document.querySelectorAll('[data-document-preview]').forEach(button => {
        button.addEventListener('click', () => {
          const previewUrl = button.dataset.previewUrl;
          const fallbackUrl = button.dataset.fallbackUrl;
          const fileName = button.dataset.fileName || 'Berkas';
          const modal = document.getElementById('upload-doc-modal');
          const body = document.getElementById('upload-doc-modal-body');
          const title = document.getElementById('upload-doc-modal-title');
          const isImage = /\.(jpe?g|png|gif|webp|bmp|svg)(\?.*)?$/i.test(previewUrl) || /\.(jpe?g|png|gif|webp|bmp|svg)$/i.test(fileName) || !previewUrl.toLowerCase().endsWith('.pdf');

          title.textContent = fileName;
          body.replaceChildren();

          if (isImage) {
            const image = document.createElement('img');
            image.src = previewUrl;
            image.alt = fileName;
            image.className = 'max-h-[70vh] max-w-full mx-auto rounded-2xl shadow-md border border-slate-100 object-contain';
            image.onerror = function() {
              if (fallbackUrl && this.src !== fallbackUrl) {
                this.src = fallbackUrl;
                return;
              }
              body.innerHTML = '<div class="p-6 text-center text-rose-500 font-semibold"><p>Gagal memuat pratinjau gambar.</p><p class="text-xs text-slate-400 mt-1">Pastikan berkas gambar valid.</p></div>';
            };
            body.appendChild(image);
          } else {
            const frame = document.createElement('iframe');
            frame.src = previewUrl;
            frame.title = fileName;
            frame.className = 'w-full h-[70vh] rounded-2xl border border-slate-200';
            body.appendChild(frame);
          }

          modal.classList.replace('hidden', 'flex');
          document.body.classList.add('overflow-hidden');
        });
      });

      function closeUploadPreview() {
        const modal = document.getElementById('upload-doc-modal');
        if (!modal) return;
        modal.classList.replace('flex', 'hidden');
        document.getElementById('upload-doc-modal-body')?.replaceChildren();
        document.body.classList.remove('overflow-hidden');
      }

      document.addEventListener('keydown', event => {
        if (event.key === 'Escape') closeUploadPreview();
      });

      const formStep4 = document.getElementById('form-step-4');
      const submitBtn = document.getElementById('btn-submit-form');
      if (formStep4 && submitBtn) {
        formStep4.addEventListener('submit', () => {
          submitBtn.disabled = true;
          submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
          const span = submitBtn.querySelector('span');
          if (span) span.textContent = 'Mengirim Pendaftaran...';
        });
      }
    </script>
@endpush
