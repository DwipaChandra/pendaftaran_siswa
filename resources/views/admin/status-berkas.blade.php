<!-- ============================================================== -->
<!-- 2. HALAMAN STATUS BERKAS (6 KARTU KPI REALTIME)                -->
<!-- ============================================================== -->
<section
  id="section-status-berkas"
  class="no-print transition-all py-2 sm:py-4 {{ ($activePage ?? 'status-berkas') !== 'status-berkas' ? 'hidden' : '' }}"
>
  <!-- Definisi SVG Pattern Doodle Bertema IT Sesuai Warna Setiap Kartu (Tersebar Rata, Seimbang & Pas) -->
  <svg class="sr-only" aria-hidden="true">
    <defs>
      @php
        $doodlePatterns = [
          'blue' => '#2563eb',
          'amber' => '#d97706',
          'sky' => '#0284c7',
          'orange' => '#ea580c',
          'rose' => '#e11d48',
          'emerald' => '#059669',
        ];
      @endphp
      @foreach ($doodlePatterns as $name => $color)
        <pattern id="it-doodle-{{ $name }}" width="360" height="200" patternUnits="userSpaceOnUse">
          <!-- BARIS ATAS (y ~ 50) - Tersebar Rata di Kiri (40), Tengah (160), Kanan (280) -->
          <!-- 1. Code Tag </> (Kiri Atas, Miring -10°) -->
          <g transform="rotate(-10 40 50)">
            <path d="M32 42 L24 50 L32 58 M44 42 L52 50 L44 58 M41 40 L37 60" stroke="{{ $color }}" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
          </g>

          <!-- 2. Terminal Prompt [ >_ ] (Tengah Atas, Miring +6°) -->
          <g transform="rotate(6 160 50)">
            <rect x="146" y="40" width="28" height="19" rx="3.5" stroke="{{ $color }}" stroke-width="1.4" fill="none"/>
            <path d="M151 46 L156 49.5 L151 53 M158 53 L165 53" stroke="{{ $color }}" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
          </g>

          <!-- 3. Microchip CPU (Kanan Atas, Miring -6°) -->
          <g transform="rotate(-6 280 50)">
            <rect x="269" y="39" width="22" height="22" rx="4" stroke="{{ $color }}" stroke-width="1.4" fill="none"/>
            <rect x="274" y="44" width="12" height="12" rx="2" stroke="{{ $color }}" stroke-width="1" fill="none"/>
            <line x1="275" y1="35" x2="275" y2="39" stroke="{{ $color }}" stroke-width="1.2" stroke-linecap="round"/>
            <line x1="285" y1="35" x2="285" y2="39" stroke="{{ $color }}" stroke-width="1.2" stroke-linecap="round"/>
            <line x1="275" y1="61" x2="275" y2="65" stroke="{{ $color }}" stroke-width="1.2" stroke-linecap="round"/>
            <line x1="285" y1="61" x2="285" y2="65" stroke="{{ $color }}" stroke-width="1.2" stroke-linecap="round"/>
            <line x1="265" y1="45" x2="269" y2="45" stroke="{{ $color }}" stroke-width="1.2" stroke-linecap="round"/>
            <line x1="265" y1="55" x2="269" y2="55" stroke="{{ $color }}" stroke-width="1.2" stroke-linecap="round"/>
            <line x1="291" y1="45" x2="295" y2="45" stroke="{{ $color }}" stroke-width="1.2" stroke-linecap="round"/>
            <line x1="291" y1="55" x2="295" y2="55" stroke="{{ $color }}" stroke-width="1.2" stroke-linecap="round"/>
          </g>

          <!-- BARIS BAWAH (y ~ 150) - Bersilang Seimbang di Kiri-Tengah (100), Tengah-Kanan (220), Kanan (340) -->
          <!-- 4. Curly Braces { } (Kiri-Tengah Bawah, Miring +12°) -->
          <g transform="rotate(12 100 148)">
            <text x="89" y="154" font-family="monospace" font-size="16" font-weight="bold" fill="{{ $color }}">{ }</text>
          </g>

          <!-- 5. Database Cylinder (Tengah-Kanan Bawah, Miring -8°) -->
          <g transform="rotate(-8 220 150)">
            <path d="M209 143 C209 140, 231 140, 231 143 C231 146, 209 146, 209 143 Z" stroke="{{ $color }}" stroke-width="1.3" fill="none"/>
            <path d="M209 143 L209 156 C209 159, 231 159, 231 156 L231 143 M209 149 C209 152, 231 152, 231 149" stroke="{{ $color }}" stroke-width="1.3" fill="none"/>
          </g>

          <!-- 6. Binary Bits 0101 (Kanan Bawah, Miring +10°) -->
          <g transform="rotate(10 340 150)">
            <text x="328" y="154" font-family="monospace" font-size="11" font-weight="bold" fill="{{ $color }}" letter-spacing="2">0101</text>
          </g>
        </pattern>
      @endforeach
    </defs>
  </svg>

  <style>
    .kpi-flip-card {
      perspective: 1000px;
      -webkit-perspective: 1000px;
    }
    .kpi-card-inner {
      position: relative;
      width: 100%;
      height: 100%;
      transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
      transform-style: preserve-3d;
      -webkit-transform-style: preserve-3d;
      will-change: transform;
    }
    .kpi-flip-card:hover .kpi-card-inner,
    .kpi-flip-card.flipped .kpi-card-inner {
      transform: rotateY(180deg);
      -webkit-transform: rotateY(180deg);
    }
    .kpi-card-front,
    .kpi-card-back {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      -webkit-backface-visibility: hidden;
      backface-visibility: hidden;
      border-radius: 1.5rem;
      overflow: hidden;
      transform: translate3d(0, 0, 0);
      -webkit-transform: translate3d(0, 0, 0);
    }
    .kpi-card-back {
      transform: rotateY(180deg) translate3d(0, 0, 0);
      -webkit-transform: rotateY(180deg) translate3d(0, 0, 0);
    }
  </style>

  <!-- Grid 6 KPI Cards Status Berkas (2 Baris x 3 Kolom di Layar Lebar) -->
  @php
    $kpiCards = [
      [
        'tone' => 'blue',
        'id' => 'kpi-total',
        'value' => $totalPendaftar,
        'title' => 'Total Pendaftar',
        'titleBadge' => null,
        'titleBadgeClass' => null,
        'subtitle' => 'Tersimpan di sistem',
        'patternUrl' => 'url(#it-doodle-blue)',
        'frontPatternOpacity' => 'opacity-[0.13]',
        'backPatternOpacity' => 'opacity-[0.09]',
        'frontClass' => 'bg-gradient-to-br from-white via-blue-50/85 to-blue-100/40 p-4 sm:p-5 hover:shadow-md hover:shadow-blue-500/10 border border-blue-200/90 hover:border-blue-400 transition-all flex flex-col justify-between',
        'titleClass' => 'text-[11px] font-black uppercase tracking-widest text-blue-700',
        'dotClass' => 'w-2.5 h-2.5 rounded-full bg-blue-600',
        'hoverPillClass' => 'text-[10px] text-blue-700 font-bold bg-blue-100/70 border border-blue-200 px-2 py-0.5 rounded-full',
        'backClass' => 'bg-gradient-to-br from-white via-blue-50 to-blue-100/50 p-4 sm:p-5 shadow-sm border border-blue-200 flex flex-col justify-between text-left',
        'backTitle' => 'Data Pendaftar',
        'backTitleClass' => 'text-xs sm:text-sm font-black uppercase tracking-wider text-blue-800 mb-1.5 pb-1 border-b border-blue-200/80',
        'bulletClass' => 'w-1.5 h-1.5 rounded-full bg-blue-600 shrink-0 mt-1',
        'bullets' => [
          'Seluruh formulir pendaftaran online',
          'Mencakup seluruh jalur & gelombang',
        ],
        'backFooterClass' => 'relative z-10 pt-2 border-t border-blue-200/70 flex items-center justify-between',
        'backFooterLabel' => 'Semua data',
        'backFooterLabelClass' => 'text-[10px] text-blue-700 font-bold',
        'tableFilter' => '',
        'actionLabel' => 'Buka Tabel',
        'actionBtnClass' => 'px-2.5 py-1 text-[11px] font-bold text-white bg-blue-600 hover:bg-blue-700 active:scale-95 rounded-lg transition inline-flex items-center gap-1 cursor-pointer',
      ],
      [
        'tone' => 'amber',
        'id' => 'kpi-pending',
        'value' => $menungguCount,
        'title' => 'Menunggu',
        'titleBadge' => null,
        'titleBadgeClass' => null,
        'subtitle' => 'Perlu ditinjau panitia',
        'patternUrl' => 'url(#it-doodle-amber)',
        'frontPatternOpacity' => 'opacity-[0.15]',
        'backPatternOpacity' => 'opacity-[0.11]',
        'frontClass' => 'bg-gradient-to-br from-white via-amber-50/85 to-amber-100/40 p-4 sm:p-5 hover:shadow-md hover:shadow-amber-500/10 border border-amber-200/90 hover:border-amber-400 transition-all flex flex-col justify-between',
        'titleClass' => 'text-[11px] font-black uppercase tracking-widest text-amber-700',
        'dotClass' => 'w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse',
        'hoverPillClass' => 'text-[10px] text-amber-800 font-bold bg-amber-100/70 border border-amber-200 px-2 py-0.5 rounded-full',
        'backClass' => 'bg-gradient-to-br from-white via-amber-50 to-amber-100/50 p-4 sm:p-5 shadow-sm border border-amber-200 flex flex-col justify-between text-left',
        'backTitle' => 'Kriteria Menunggu',
        'backTitleClass' => 'text-xs sm:text-sm font-black uppercase tracking-wider text-amber-800 mb-1.5 pb-1 border-b border-amber-200/80',
        'bulletClass' => 'w-1.5 h-1.5 rounded-full bg-amber-600 shrink-0 mt-1',
        'bullets' => [
          'Berkas baru selesai dikirim siswa',
          'Dalam antrean audit verifikator',
        ],
        'backFooterClass' => 'relative z-10 pt-2 border-t border-amber-200/70 flex items-center justify-between',
        'backFooterLabel' => 'Perlu aksi',
        'backFooterLabelClass' => 'text-[10px] text-amber-800 font-bold',
        'tableFilter' => 'Menunggu Verifikasi',
        'actionLabel' => 'Verifikasi Berkas',
        'actionBtnClass' => 'px-2.5 py-1 text-[11px] font-bold text-white bg-amber-600 hover:bg-amber-700 active:scale-95 rounded-lg transition inline-flex items-center gap-1 cursor-pointer',
      ],
      [
        'tone' => 'sky',
        'id' => 'kpi-verified',
        'value' => $terverifikasiCount ?? $diterimaCount,
        'title' => 'Terverifikasi',
        'titleBadge' => null,
        'titleBadgeClass' => null,
        'subtitle' => 'Lolos seleksi berkas',
        'patternUrl' => 'url(#it-doodle-sky)',
        'frontPatternOpacity' => 'opacity-[0.13]',
        'backPatternOpacity' => 'opacity-[0.09]',
        'frontClass' => 'bg-gradient-to-br from-white via-sky-50/85 to-sky-100/40 p-4 sm:p-5 hover:shadow-md hover:shadow-sky-500/10 border border-sky-200/90 hover:border-sky-400 transition-all flex flex-col justify-between',
        'titleClass' => 'text-[11px] font-black uppercase tracking-widest text-sky-700',
        'dotClass' => 'w-2.5 h-2.5 rounded-full bg-sky-600',
        'hoverPillClass' => 'text-[10px] text-sky-800 font-bold bg-sky-100/70 border border-sky-200 px-2 py-0.5 rounded-full',
        'backClass' => 'bg-gradient-to-br from-white via-sky-50 to-sky-100/50 p-4 sm:p-5 shadow-sm border border-sky-200 flex flex-col justify-between text-left',
        'backTitle' => 'Syarat Terverifikasi',
        'backTitleClass' => 'text-xs sm:text-sm font-black uppercase tracking-wider text-sky-800 mb-1.5 pb-1 border-b border-sky-200/80',
        'bulletClass' => 'w-1.5 h-1.5 rounded-full bg-sky-600 shrink-0 mt-1',
        'bullets' => [
          'Dokumen jelas, tajam & sah',
          'Lolos berkas & siap daftar ulang',
        ],
        'backFooterClass' => 'relative z-10 pt-2 border-t border-sky-200/70 flex items-center justify-between',
        'backFooterLabel' => 'Lolos berkas',
        'backFooterLabelClass' => 'text-[10px] text-sky-800 font-bold',
        'tableFilter' => 'Diterima',
        'actionLabel' => 'Lihat Data',
        'actionBtnClass' => 'px-2.5 py-1 text-[11px] font-bold text-white bg-sky-600 hover:bg-sky-700 active:scale-95 rounded-lg transition inline-flex items-center gap-1 cursor-pointer',
      ],
      [
        'tone' => 'orange',
        'id' => 'kpi-revision',
        'value' => $revisiCount ?? 0,
        'title' => 'Perlu Revisi',
        'titleBadge' => null,
        'titleBadgeClass' => null,
        'subtitle' => 'Perbaikan dokumen siswa',
        'patternUrl' => 'url(#it-doodle-orange)',
        'frontPatternOpacity' => 'opacity-[0.14]',
        'backPatternOpacity' => 'opacity-[0.10]',
        'frontClass' => 'bg-gradient-to-br from-white via-orange-50/85 to-orange-100/40 p-4 sm:p-5 hover:shadow-md hover:shadow-orange-500/10 border border-orange-200/90 hover:border-orange-400 transition-all flex flex-col justify-between',
        'titleClass' => 'text-[11px] font-black uppercase tracking-widest text-orange-700',
        'dotClass' => 'w-2.5 h-2.5 rounded-full bg-orange-500',
        'hoverPillClass' => 'text-[10px] text-orange-800 font-bold bg-orange-100/70 border border-orange-200 px-2 py-0.5 rounded-full',
        'backClass' => 'bg-gradient-to-br from-white via-orange-50 to-orange-100/50 p-4 sm:p-5 shadow-sm border border-orange-200 flex flex-col justify-between text-left',
        'backTitle' => 'Kriteria Revisi',
        'backTitleClass' => 'text-xs sm:text-sm font-black uppercase tracking-wider text-orange-800 mb-1.5 pb-1 border-b border-orange-200/80',
        'bulletClass' => 'w-1.5 h-1.5 rounded-full bg-orange-600 shrink-0 mt-1',
        'bullets' => [
          'Foto atau scan berkas buram/salah',
          'Siswa diminta mengunggah ulang',
        ],
        'backFooterClass' => 'relative z-10 pt-2 border-t border-orange-200/70 flex items-center justify-between',
        'backFooterLabel' => 'Tinjau revisi',
        'backFooterLabelClass' => 'text-[10px] text-orange-800 font-bold',
        'tableFilter' => 'Perbaikan Berkas',
        'actionLabel' => 'Tinjau Siswa',
        'actionBtnClass' => 'px-2.5 py-1 text-[11px] font-bold text-white bg-orange-600 hover:bg-orange-700 active:scale-95 rounded-lg transition inline-flex items-center gap-1 cursor-pointer',
      ],
      [
        'tone' => 'rose',
        'id' => 'kpi-rejected',
        'value' => $ditolakCount,
        'title' => 'Ditolak',
        'titleBadge' => null,
        'titleBadgeClass' => null,
        'subtitle' => 'Tidak memenuhi syarat',
        'patternUrl' => 'url(#it-doodle-rose)',
        'frontPatternOpacity' => 'opacity-[0.13]',
        'backPatternOpacity' => 'opacity-[0.09]',
        'frontClass' => 'bg-gradient-to-br from-white via-rose-50/85 to-rose-100/40 p-4 sm:p-5 hover:shadow-md hover:shadow-rose-500/10 border border-rose-200/90 hover:border-rose-400 transition-all flex flex-col justify-between',
        'titleClass' => 'text-[11px] font-black uppercase tracking-widest text-rose-700',
        'dotClass' => 'w-2.5 h-2.5 rounded-full bg-rose-600',
        'hoverPillClass' => 'text-[10px] text-rose-800 font-bold bg-rose-100/70 border border-rose-200 px-2 py-0.5 rounded-full',
        'backClass' => 'bg-gradient-to-br from-white via-rose-50 to-rose-100/50 p-4 sm:p-5 shadow-sm border border-rose-200 flex flex-col justify-between text-left',
        'backTitle' => 'Penyebab Ditolak',
        'backTitleClass' => 'text-xs sm:text-sm font-black uppercase tracking-wider text-rose-800 mb-1.5 pb-1 border-b border-rose-200/80',
        'bulletClass' => 'w-1.5 h-1.5 rounded-full bg-rose-600 shrink-0 mt-1',
        'bullets' => [
          'Dokumen tidak memenuhi kriteria',
          'Batas perbaikan terlampaui / batal',
        ],
        'backFooterClass' => 'relative z-10 pt-2 border-t border-rose-200/70 flex items-center justify-between',
        'backFooterLabel' => 'Data ditolak',
        'backFooterLabelClass' => 'text-[10px] text-rose-800 font-bold',
        'tableFilter' => 'Ditolak',
        'actionLabel' => 'Lihat Data',
        'actionBtnClass' => 'px-2.5 py-1 text-[11px] font-bold text-white bg-rose-600 hover:bg-rose-700 active:scale-95 rounded-lg transition inline-flex items-center gap-1 cursor-pointer',
      ],
      [
        'tone' => 'emerald',
        'id' => 'kpi-accepted',
        'value' => $sudahDaftarUlangCount ?? 0,
        'title' => 'Diterima',
        'titleBadge' => 'Daftar Ulang',
        'titleBadgeClass' => 'text-[9.5px] font-bold text-emerald-800 bg-emerald-100 border border-emerald-200 px-1.5 rounded-md',
        'subtitle' => 'Resmi siswa baru',
        'patternUrl' => 'url(#it-doodle-emerald)',
        'frontPatternOpacity' => 'opacity-[0.13]',
        'backPatternOpacity' => 'opacity-[0.09]',
        'frontClass' => 'bg-gradient-to-br from-white via-emerald-50/85 to-emerald-100/40 p-4 sm:p-5 hover:shadow-md hover:shadow-emerald-500/10 border border-emerald-200/90 hover:border-emerald-400 transition-all flex flex-col justify-between',
        'titleClass' => 'text-[11px] font-black uppercase tracking-widest text-emerald-700',
        'dotClass' => 'w-2.5 h-2.5 rounded-full bg-emerald-600',
        'hoverPillClass' => 'text-[10px] text-emerald-800 font-bold bg-emerald-100/70 border border-emerald-200 px-2 py-0.5 rounded-full',
        'backClass' => 'bg-gradient-to-br from-white via-emerald-50 to-emerald-100/50 p-4 sm:p-5 shadow-sm border border-emerald-200 flex flex-col justify-between text-left',
        'backTitle' => 'Syarat Diterima',
        'backTitleClass' => 'text-xs sm:text-sm font-black uppercase tracking-wider text-emerald-800 mb-1.5 pb-1 border-b border-emerald-200/80',
        'bulletClass' => 'w-1.5 h-1.5 rounded-full bg-emerald-600 shrink-0 mt-1',
        'bullets' => [
          'Berkas lolos verifikasi panitia',
          'Selesai administrasi daftar ulang',
        ],
        'backFooterClass' => 'relative z-10 pt-2 border-t border-emerald-200/70 flex items-center justify-between',
        'backFooterLabel' => 'Daftar ulang',
        'backFooterLabelClass' => 'text-[10px] text-emerald-800 font-bold',
        'tableFilter' => 'daftar-ulang',
        'actionLabel' => 'Buka Menu',
        'actionBtnClass' => 'px-2.5 py-1 text-[11px] font-bold text-white bg-emerald-600 hover:bg-emerald-700 active:scale-95 rounded-lg transition inline-flex items-center gap-1 cursor-pointer',
      ],
    ];
  @endphp
  <div class="max-w-6xl mx-auto">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-5">
      @foreach ($kpiCards as $card)
        <div class="kpi-flip-card group cursor-pointer h-[190px] sm:h-[200px]" onclick="this.classList.toggle('flipped')" title="Arahkan kursor atau sentuh untuk melihat rincian & aksi">
          <div class="kpi-card-inner">
            <!-- SISI DEPAN -->
            <div data-kpi-tone="{{ $card['tone'] }}" class="kpi-card-front {{ $card['frontClass'] }}">
              <svg class="absolute inset-0 w-full h-full pointer-events-none {{ $card['frontPatternOpacity'] }} select-none" xmlns="http://www.w3.org/2000/svg">
                <rect width="100%" height="100%" fill="{{ $card['patternUrl'] }}" />
              </svg>

              <div class="relative z-10 flex items-center justify-between">
                @if ($card['titleBadge'])
                  <div class="flex items-center gap-1.5">
                    <span class="{{ $card['titleClass'] }}">{{ $card['title'] }}</span>
                    <span class="{{ $card['titleBadgeClass'] }}">{{ $card['titleBadge'] }}</span>
                  </div>
                @else
                  <span class="{{ $card['titleClass'] }}">{{ $card['title'] }}</span>
                @endif
                <span class="{{ $card['dotClass'] }}"></span>
              </div>
              <div class="relative z-10 my-auto py-0.5">
                <p id="{{ $card['id'] }}" class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tabular-nums leading-none tracking-tight">{{ $card['value'] }}</p>
              </div>
              <div class="relative z-10 flex items-center justify-between text-xs text-slate-600 font-medium">
                <span>{{ $card['subtitle'] }}</span>
                <span class="{{ $card['hoverPillClass'] }}">Hover aksi & info</span>
              </div>
            </div>

            <!-- SISI BELAKANG -->
            <div data-kpi-tone="{{ $card['tone'] }}" class="kpi-card-back {{ $card['backClass'] }}">
              <svg class="absolute inset-0 w-full h-full pointer-events-none {{ $card['backPatternOpacity'] }} select-none" xmlns="http://www.w3.org/2000/svg">
                <rect width="100%" height="100%" fill="{{ $card['patternUrl'] }}" />
              </svg>

              <div class="relative z-10">
                <h4 class="{{ $card['backTitleClass'] }}">{{ $card['backTitle'] }}</h4>
                <ul class="space-y-1 text-[11px] sm:text-xs font-semibold text-slate-700 leading-snug">
                  @foreach ($card['bullets'] as $bullet)
                    <li class="flex items-start gap-1.5">
                      <span class="{{ $card['bulletClass'] }}"></span>
                      <span>{{ $bullet }}</span>
                    </li>
                  @endforeach
                </ul>
              </div>

              <div class="{{ $card['backFooterClass'] }}">
                <span class="{{ $card['backFooterLabelClass'] }}">{{ $card['backFooterLabel'] }}</span>
                <button
                  type="button"
                  onclick="openStatusInTable('{{ $card['tableFilter'] }}', event)"
                  class="{{ $card['actionBtnClass'] }}"
                >
                  <span>{{ $card['actionLabel'] }}</span>
                  <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
