@extends('layouts.app')

@section('title', 'Kartu Tanda Peserta SPMB 2027/2028 - SMK TI Bali Global Badung')
@section('html_class', 'scroll-smooth')
@section('body_class', 'min-h-screen flex flex-col bg-slate-50 text-slate-900 selection:bg-blue-600 selection:text-white')

@push('styles')
    <style>
      .card-data-table td {
        padding-block: 0.25rem;
      }

      @media (min-width: 640px) {
        .card-data-table td {
          padding-block: 0.375rem;
        }
      }

      /* ==========================================
         PRINT MEDIA: PERSIS DENGAN TAMPILAN WEBSITE
         ========================================== */
      @media print {
        @page {
          size: A4 portrait;
          margin: 10mm 12mm;
        }

        *,
        *::before,
        *::after {
          -webkit-print-color-adjust: exact !important;
          print-color-adjust: exact !important;
        }

        body {
          background: #ffffff !important;
          color: #141414 !important;
          padding: 0 !important;
          margin: 0 !important;
        }

        /* Sembunyikan elemen antarmuka web saat dicetak */
        .no-print,
        nav,
        header,
        footer,
        #page-header-banner,
        #toast-container {
          display: none !important;
        }

        main {
          padding: 0 !important;
          margin: 0 !important;
          max-width: 100% !important;
        }

        /* Kartu Peserta Sama Persis dengan Tampilan Website */
        #printable-id-card {
          display: block !important;
          visibility: visible !important;
          position: relative !important;
          width: 100% !important;
          max-width: 760px !important;
          margin: 0 auto !important;
          padding: 1.5rem !important;
          border: 1px solid #cbd5e1 !important;
          border-radius: 1rem !important;
          box-shadow: none !important;
          background: #ffffff !important;
          page-break-inside: avoid !important;
          break-inside: avoid !important;
        }

        #printable-id-card * {
          visibility: visible !important;
        }

        #printable-id-card img {
          max-width: 100% !important;
          display: inline-block !important;
          visibility: visible !important;
        }

        /* Kunci tata letak pasfoto & tabel tetap 2 kolom berdampingan saat cetak */
        #card-body-grid {
          display: grid !important;
          grid-template-columns: 120px 1fr !important;
          gap: 1.25rem !important;
          align-items: start !important;
        }

        #card-body-grid > div:first-child {
          grid-column: 1 !important;
          display: flex !important;
          flex-direction: column !important;
          align-items: center !important;
        }

        #print-photo-box {
          width: 110px !important;
          height: 145px !important;
          display: flex !important;
          align-items: center !important;
          justify-content: center !important;
          overflow: hidden !important;
          background: #f8fafc !important;
          border: 1.5px solid #94a3b8 !important;
          border-radius: 0.5rem !important;
        }

        #print-photo-box img {
          width: 100% !important;
          height: 100% !important;
          object-fit: cover !important;
          display: block !important;
        }

        #card-body-grid > div:last-child {
          grid-column: 2 !important;
        }
      }
    </style>
@endpush

@section('content')
    @if (session('success'))
      <!-- Popup Sukses Data Tersimpan -->
      <div
        id="popup-data-tersimpan"
        class="no-print fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4"
        onclick="this.remove()"
      >
        <div
          class="bg-white rounded-3xl p-6 sm:p-8 shadow-2xl max-w-xs w-full text-center border border-slate-100"
        >
          <div
            class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4"
          >
            <img
              src="{{ asset('assets/img/icon/check-green.svg') }}"
              alt="Sukses"
              class="w-8 h-8 object-contain shrink-0"
            />
          </div>
          <h3 class="text-base sm:text-lg font-black text-slate-900">
            Pendaftaran Berhasil!
          </h3>
          <p class="text-xs text-slate-500 mt-1">
            Data pendaftaran dan berkas Anda telah tersimpan dengan aman.
          </p>
          <button
            type="button"
            onclick="document.getElementById('popup-data-tersimpan').remove()"
            class="mt-5 w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm rounded-xl transition cursor-pointer shadow-md shadow-emerald-600/25"
          >
            OK, Lihat Kartu
          </button>
        </div>
      </div>
    @endif

    @php
      $logoDiskPath = public_path('assets/img/alogo.png');
      $logoBase64 = file_exists($logoDiskPath)
          ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoDiskPath))
          : asset('assets/img/alogo.png');

      $fotoBerkas = $pendaftaran->berkas->first(function ($b) {
          $jenis = strtolower($b->jenis_berkas ?? '');
          return str_contains($jenis, 'foto') || str_contains($jenis, 'pasfoto');
      });

      $hasValidPhoto = false;
      $fotoSrc = null;
      $fotoFallbackUrl = null;

      if ($fotoBerkas && !empty($fotoBerkas->path_file)) {
          $rawPath = (string) $fotoBerkas->path_file;
          $normalized = str_replace('\\', '/', $rawPath);
          $filename = basename($normalized);

          $possibleDiskPaths = [
              storage_path('app/public/berkas/' . $filename),
              storage_path('app/public/' . ltrim(preg_replace('#^storage/#', '', $normalized), '/')),
              public_path(ltrim($normalized, '/')),
              public_path('storage/berkas/' . $filename),
              public_path('berkas/' . $filename),
          ];

          $foundDiskPath = null;
          foreach ($possibleDiskPaths as $path) {
              if (file_exists($path) && is_file($path)) {
                  $foundDiskPath = $path;
                  break;
              }
          }

          if ($foundDiskPath) {
              $mime = mime_content_type($foundDiskPath) ?: 'image/jpeg';
              $fotoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($foundDiskPath));
              $hasValidPhoto = true;
          } else {
              $fotoSrc = asset($rawPath);
              $hasValidPhoto = true;
          }

          $fotoFallbackUrl = route('storage.berkas', ['filename' => $filename]);
      }

      $cardRows = [
          ['label' => 'No. Registrasi', 'id' => 'print-nisn-card', 'td_class' => 'font-bold text-slate-900', 'span_class' => 'font-mono', 'value' => $pendaftaran->no_registrasi],
          ['label' => 'Nama Lengkap', 'id' => 'print-nama', 'td_class' => 'font-extrabold text-slate-950 uppercase', 'value' => $calonSiswa->nama_lengkap],
          ['label' => 'Tempat Lahir', 'id' => 'print-tempat-lahir', 'td_class' => 'text-slate-800 font-medium', 'value' => $calonSiswa->tempat_lahir ?? '-'],
          ['label' => 'Tanggal Lahir', 'id' => 'print-tanggal-lahir', 'td_class' => 'text-slate-800 font-medium', 'value' => $calonSiswa->tanggal_lahir ? date('d-m-Y', strtotime($calonSiswa->tanggal_lahir)) : '-'],
          ['label' => 'Jenis Kelamin', 'id' => 'print-jk', 'td_class' => 'text-slate-800 font-medium', 'value' => $calonSiswa->jenis_kelamin ?? '-'],
          ['label' => 'Agama', 'id' => 'print-agama', 'td_class' => 'text-slate-800 font-medium', 'value' => $calonSiswa->agama ?? '-'],
          ['label' => 'Asal Sekolah', 'id' => 'print-sekolah', 'td_class' => 'text-slate-800 font-medium', 'value' => $calonSiswa->asal_sekolah ?? '-'],
          ['label' => 'Alamat Siswa', 'id' => 'print-alamat-siswa', 'td_class' => 'text-slate-800 font-medium', 'value' => $calonSiswa->alamat_domisili ?? '-'],
          ['label' => 'Alamat Orang Tua', 'id' => 'print-alamat-ortu', 'td_class' => 'text-slate-800 font-medium', 'value' => $calonSiswa->alamat_ortu ?? '-'],
          ['label' => 'WhatsApp Siswa', 'id' => 'print-wa-siswa', 'td_class' => 'text-slate-800 font-medium', 'value' => $calonSiswa->no_whatsapp ?? '-'],
          ['label' => 'Email Siswa', 'id' => 'print-email-siswa', 'td_class' => 'text-slate-800 font-medium', 'value' => $calonSiswa->user?->email ?? '-'],
          ['label' => 'Pilihan Kompetensi', 'id' => 'print-jurusan', 'td_class' => 'text-slate-800 font-bold', 'value' => $pendaftaran->jurusan->nama_jurusan ?? '-'],
          ['label' => 'Jalur Pendaftaran', 'id' => 'print-jalur', 'td_class' => 'text-slate-800 font-medium', 'value' => ($pendaftaran->jalur->nama_jalur ?? '-') . (!empty($pendaftaran->nama_sub_jalur) ? ' (' . $pendaftaran->nama_sub_jalur . ')' : '')],
          ['label' => 'Gelombang Pendaftaran', 'id' => 'print-gelombang', 'td_class' => 'text-slate-800 font-medium', 'value' => $pendaftaran->gelombang->nama_gelombang ?? '-'],
          ['label' => 'Nama Ayah', 'id' => 'print-bapak', 'td_class' => 'text-slate-800 font-medium', 'value' => $calonSiswa->nama_bapak ?: '-'],
          ['label' => 'Pekerjaan Ayah', 'id' => 'print-pekerjaan-bapak', 'td_class' => 'text-slate-800 font-medium', 'value' => $calonSiswa->pekerjaan_bapak ?: '-'],
          ['label' => 'Nama Ibu', 'id' => 'print-ibu', 'td_class' => 'text-slate-800 font-medium', 'value' => $calonSiswa->nama_ibu ?: '-'],
          ['label' => 'Pekerjaan Ibu', 'id' => 'print-pekerjaan-ibu', 'td_class' => 'text-slate-800 font-medium', 'value' => $calonSiswa->pekerjaan_ibu ?: '-'],
          ['label' => 'No. WhatsApp Ayah', 'id' => 'print-no-wa-ayah', 'td_class' => 'text-slate-800 font-medium', 'value' => $calonSiswa->no_wa_ayah ?: '-'],
          ['label' => 'No. WhatsApp Ibu', 'id' => 'print-no-wa-ibu', 'td_class' => 'text-slate-800 font-medium', 'value' => $calonSiswa->no_wa_ibu ?: '-'],
      ];
    @endphp

    <!-- MAIN CONTENT WRAPPER -->
    <main
      class="flex-1 max-w-4xl mx-auto w-full px-4 sm:px-6 py-5 sm:py-6 space-y-4"
    >
      @if ($errors->any())
        <div class="no-print rounded-2xl bg-rose-50 border border-rose-200 p-4 text-xs font-semibold text-rose-700">
          <ul class="list-disc pl-4 space-y-1">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <!-- TOP ACTION BAR -->
      <div
        id="page-header-banner"
        class="no-print overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm"
      >
        <div class="flex items-center justify-between gap-3 bg-white px-4 py-3 sm:px-5">
          <!-- Info Sisi Kiri -->
          <div class="flex min-w-0 items-center gap-2 sm:gap-3">
            <a
              href="{{ route('home') }}"
              class="inline-flex shrink-0 items-center gap-1.5 rounded-xl bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-700 transition hover:bg-slate-200 active:scale-95"
              title="Kembali ke Beranda"
            >
              <img
                src="{{ asset('assets/img/icon/arrow-left.svg') }}"
                alt=""
                class="h-3.5 w-3.5 shrink-0 object-contain"
              />
              <span>Beranda</span>
            </a>
            <div class="min-w-0 truncate rounded-xl bg-slate-100 px-3 py-1.5 text-xs text-slate-700">
              No. Registrasi:
              <strong
                id="banner-reg-no"
                class="ml-0.5 font-mono text-xs font-extrabold text-blue-700"
                >{{ $pendaftaran->no_registrasi }}</strong
              >
            </div>
          </div>

          <!-- Info Sisi Kanan: Cetak -->
          <div id="action-buttons-bar" class="flex shrink-0 items-center gap-2">
            <button
              type="button"
              onclick="window.print()"
              class="inline-flex cursor-pointer items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-blue-600 px-4 py-2 text-xs font-bold text-white shadow-md shadow-blue-600/25 transition-all hover:bg-blue-700 active:scale-95 sm:px-5 sm:py-2.5 sm:text-sm"
            >
              <img
                src="{{ asset('assets/img/icon/printer-white.svg') }}"
                alt=""
                class="h-4 w-4 shrink-0 object-contain"
              />
              <span>Cetak Kartu Peserta</span>
            </button>
          </div>
        </div>

        @include('registration.partials.application-status')
      </div>

      <!-- KOMPONEN KARTU TANDA PESERTA RESMI -->
      <section>
        <div
          id="printable-id-card"
          class="bg-white rounded-2xl p-4 sm:p-6 shadow-xl shadow-slate-300/70 relative text-slate-900 overflow-hidden"
        >
          <!-- Watermark Background Logo -->
          <div
            class="absolute inset-0 flex items-center justify-center opacity-[0.03] pointer-events-none select-none"
          >
            <img
              src="{{ asset('assets/img/icon/academic-cap.svg') }}"
              alt=""
              class="w-96 h-96 object-contain"
            />
          </div>

          <!-- KOP RESMI INSTITUSI SEKOLAH -->
          <div class="border-b-2 border-slate-900 pb-3 mb-3">
            <div
              class="flex items-center justify-between gap-3"
            >
              <div class="flex items-center gap-3.5">
                <img
                  src="{{ $logoBase64 }}"
                  alt="Logo SMK TI Bali Global Badung"
                  class="w-12 h-12 sm:w-16 sm:h-16 object-contain shrink-0"
                />
                <div class="text-left">
                  <p
                    class="text-[9.5px] sm:text-[10.5px] font-semibold uppercase tracking-widest text-slate-600"
                  >
                    Yayasan Widya Dharma Shanti
                  </p>
                  <h3
                    class="text-sm sm:text-base md:text-lg font-black text-slate-950 tracking-tight leading-snug uppercase"
                  >
                    SMK TI BALI GLOBAL BADUNG
                  </h3>
                  <p
                    class="text-[9.5px] sm:text-[10.5px] font-bold text-blue-700"
                  >
                    TERAKREDITASI &ldquo;A&rdquo; (UNGGUL) &bull; NPSN: 69959273
                  </p>
                </div>
              </div>

              <!-- Badge Tahun Ajaran -->
              <div class="flex flex-col items-end shrink-0">
                <span
                  class="px-2.5 py-1 bg-slate-900 text-white text-[9px] font-black rounded uppercase tracking-wider"
                >
                  SPMB 2027/2028
                </span>
                <span class="text-[8px] text-slate-500 font-semibold mt-0.5"
                  >Kartu Bukti Resmi</span
                >
              </div>
            </div>
          </div>

          <!-- JUDUL DOKUMEN -->
          <div class="text-center mb-3">
            <h4
              class="text-xs sm:text-sm font-black text-slate-950 uppercase tracking-wide"
            >
              KARTU TANDA PESERTA SELEKSI SPMB
            </h4>
            <p class="text-[9px] text-slate-500 font-medium">
              Tahun Ajaran 2027/2028
            </p>
          </div>

          <!-- BANNER REGISTRASI -->
          <div
            class="bg-slate-50 rounded-lg px-3 py-2 mb-3.5 border border-slate-300 flex flex-wrap items-center justify-between gap-2 text-xs"
          >
            <div class="flex items-center gap-2">
              <span class="text-slate-500 text-[10px] sm:text-[11px]"
                >No. Registrasi:</span
              >
              <strong
                id="print-reg-no"
                class="text-xs sm:text-sm font-black text-blue-700 tracking-wide font-mono"
              >
                {{ $pendaftaran->no_registrasi }}
              </strong>
            </div>
            <div class="flex items-center gap-2 text-[10px] sm:text-[11px]">
              <span class="text-slate-500">Tgl Pendaftaran:</span>
              <strong id="print-date" class="text-slate-800 font-semibold"
                >{{ date('d-m-Y', strtotime($pendaftaran->tgl_daftar)) }}</strong
              >
            </div>
            <div class="flex items-center gap-2 text-[10px] sm:text-[11px]">
              <span class="text-slate-500">Status:</span>
              <strong class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded font-semibold"
                >{{ $pendaftaran->status_berkas ?? 'Menunggu Verifikasi' }}</strong
              >
            </div>
          </div>

          <!-- KONTEN UTAMA: PASFOTO 3X4 & TABEL IDENTITAS -->
          <div
            id="card-body-grid"
            class="grid grid-cols-1 md:grid-cols-4 gap-3.5 sm:gap-4 items-start"
          >
            <!-- Kolom Kiri: Pasfoto 3x4 Calon Siswa -->
            <div class="md:col-span-1 flex flex-col items-center">
              <div
                id="print-photo-box"
                class="w-24 sm:w-28 h-32 sm:h-36 bg-slate-100 border-2 border-slate-300 rounded-lg overflow-hidden flex items-center justify-center"
              >
                @if ($hasValidPhoto && $fotoSrc)
                  <img
                    src="{{ $fotoSrc }}"
                    alt="Pasfoto {{ $calonSiswa->nama_lengkap }}"
                    class="w-full h-full object-cover"
                    data-fallback-url="{{ $fotoFallbackUrl }}"
                    onerror="if(this.dataset.fallbackUrl && !this.dataset.fallbackTried){this.dataset.fallbackTried='1';this.src=this.dataset.fallbackUrl;return;}this.style.display='none';if(this.nextElementSibling){this.nextElementSibling.classList.replace('hidden','flex');}"
                  />
                @endif
                <div
                  class="{{ ($hasValidPhoto && $fotoSrc) ? 'hidden' : 'flex' }} w-full h-full flex-col items-center justify-center p-2 text-center text-slate-400"
                >
                  <img
                    src="{{ asset('assets/img/icon/user.svg') }}"
                    alt=""
                    class="w-7 h-7 object-contain opacity-40 shrink-0"
                  />
                  <span class="text-[8px] font-bold mt-1 text-slate-500"
                    >Pasfoto 3x4</span
                  >
                </div>
              </div>
              <span
                class="text-[8px] text-slate-400 font-semibold mt-1 text-center"
                >Pasfoto 3x4 Calon Siswa</span
              >
            </div>

            <!-- Kolom Kanan: Rincian Data Lengkap -->
            <div class="md:col-span-3 text-[11px] sm:text-xs">
              <table class="card-data-table w-full border-collapse">
                <tbody>
                  @foreach ($cardRows as $row)
                    <tr @unless ($loop->last) class="border-b border-slate-100" @endunless>
                      <td class="text-slate-500{{ $loop->first ? ' w-32 sm:w-40' : '' }} font-medium">
                        {{ $row['label'] }}
                      </td>
                      <td class="{{ $row['td_class'] }}">
                        : <span id="{{ $row['id'] }}" @if (!empty($row['span_class'])) class="{{ $row['span_class'] }}" @endif>{{ $row['value'] }}</span>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>

          <!-- TANDA TANGAN SISWA & ORANG TUA / WALI -->
          <div
            class="mt-4 pt-3 border-t border-slate-200 flex items-end justify-between gap-4"
          >
            <!-- Tanda Tangan Orang Tua / Wali -->
            <div class="flex flex-col items-start text-left">
              <p class="text-[9px] text-slate-600 font-medium">
                Tanda Tangan Orang Tua / Wali,
              </p>
              <div class="h-10"></div>
              <p class="text-[10px] text-slate-700 font-semibold tracking-wide">
                ( {{ $calonSiswa->nama_bapak ?: ($calonSiswa->nama_ibu ?: '........................................') }} )
              </p>
            </div>

            <!-- Tanda Tangan Calon Siswa -->
            <div class="flex flex-col items-end text-right">
              <p class="text-[9px] text-slate-600 font-medium">
                Tanda Tangan Calon Siswa,
              </p>
              <div class="h-10"></div>
              <p class="text-[10px] text-slate-700 font-semibold tracking-wide">
                ( {{ $calonSiswa->nama_lengkap }} )
              </p>
            </div>
          </div>
        </div>
      </section>
    </main>

    <!-- Footer Simple -->
    <footer
      class="mt-auto py-4 text-center text-[11px] text-slate-400 no-print"
    >
      <p>
        &copy; 2027 Panitia SPMB SMK TI Bali Global Badung. Seluruh Hak Cipta
        Dilindungi.
      </p>
    </footer>
@endsection
