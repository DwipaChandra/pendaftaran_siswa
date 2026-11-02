<!-- ============================================================== -->
<!-- 6. HALAMAN KEUANGAN & KAS SISWA (7 POS BIAYA SPMB 2027)        -->
<!-- ============================================================== -->
@php
  $keuSummary = $keuanganSummary ?? [
      'total_pemasukan' => 0,
      'total_tagihan' => 0,
      'total_piutang' => 0,
      'siswa_lunas_count' => 0,
      'siswa_nyicil_count' => 0,
      'siswa_belum_count' => 0,
      'siswa_refund_count' => 0,
      'siswa_baju_lunas_count' => 0,
      'kas_kategori' => [
          'pendaftaran' => 0,
          'mos' => 0,
          'asuransi' => 0,
          'seragam' => 0,
          'dpp' => 0,
          'spp' => 0,
          'osis' => 0,
      ],
  ];

  $posConfig = [
      'pendaftaran' => [
          'nama' => 'Pendaftaran',
          'singkat' => 'Pendaftaran',
          'sub' => 'Rp 150rb (Diskon s/d 100%)',
      ],
      'mos' => [
          'nama' => 'MOS & Kesiswaan',
          'singkat' => 'MOS & Kepramukaan',
          'sub' => 'Rp 450.000 (Harga Tetap)',
      ],
      'asuransi' => [
          'nama' => 'Asuransi (3 Tahun)',
          'singkat' => 'Asuransi Siswa',
          'sub' => 'Rp 100.000 (3 Tahun)',
      ],
      'seragam' => [
          'nama' => 'Seragam / Baju',
          'singkat' => 'Seragam Sekolah',
          'sub' => 'Rp 1.975.000 (Syarat Baju)',
      ],
      'dpp' => [
          'nama' => 'DPP Semester 1',
          'singkat' => 'DPP Sem. 1',
          'sub' => 'TI 650rb / TKC 500rb (Diskon)',
      ],
      'spp' => [
          'nama' => 'SPP Bulan Pertama',
          'singkat' => 'SPP Bln Pertama',
          'sub' => 'TI 650rb / TKC 450rb (Diskon)',
      ],
      'osis' => [
          'nama' => 'OSIS Bulan Pertama',
          'singkat' => 'Iuran OSIS',
          'sub' => 'Rp 25.000 (Harga Tetap)',
      ],
  ];
@endphp

<section
  id="section-keuangan"
  class="space-y-6 no-print transition-all {{ ($activePage ?? 'beranda') !== 'keuangan' ? 'hidden' : '' }}"
  data-bayar-endpoint="{{ route('admin.keuangan.bayar') }}"
  data-refund-endpoint="{{ route('admin.keuangan.refund') }}"
  data-diskon-endpoint="{{ route('admin.keuangan.diskon-spp') }}"
  data-csrf="{{ csrf_token() }}"
>
  <span class="sr-only">Halaman Keuangan & Kas Siswa</span>


  <!-- ============================================================== -->
  <!-- 2. RINGKASAN / SUMMARY CARD DI BAGIAN ATAS HALAMAN             -->
  <!-- ============================================================== -->
  <!-- 3 Kartu KPI Utama -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5">
    <!-- Card 1: Total Pemasukan Keseluruhan -->
    <div class="bg-gradient-to-br from-[#0f172a] to-[#1e293b] text-white rounded-2xl border border-slate-800 p-5 sm:p-6 shadow-md relative overflow-hidden">
      <div class="absolute -top-10 -right-10 w-32 h-32 bg-blue-500/20 rounded-full blur-2xl pointer-events-none"></div>
      <div class="flex items-center justify-between gap-3 relative z-10">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Pemasukan Keseluruhan</span>
        <div class="w-9 h-9 rounded-xl bg-blue-500/20 text-blue-400 border border-blue-400/30 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
          </svg>
        </div>
      </div>
      <div class="mt-3 relative z-10">
        <div class="text-2xl sm:text-3xl font-black text-white tracking-tight tabular-nums" id="summary-total-pemasukan">
          Rp {{ number_format($keuSummary['total_pemasukan'], 0, ',', '.') }}
        </div>
      </div>
    </div>

    <!-- Card 2: Total Piutang / Belum Terbayar -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6 relative overflow-hidden">
      <div class="flex items-center justify-between gap-3">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Piutang / Belum Bayar</span>
        <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 border border-amber-200/80 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10" />
            <polyline points="12 6 12 12 16 14" />
          </svg>
        </div>
      </div>
      <div class="mt-3">
        <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight tabular-nums" id="summary-total-piutang">
          Rp {{ number_format($keuSummary['total_piutang'], 0, ',', '.') }}
        </div>
      </div>
    </div>

    <!-- Card 3: Siswa Sah Daftar Ulang -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6 relative overflow-hidden">
      <div class="flex items-center justify-between gap-3">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Siswa Sah Daftar Ulang</span>
        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200/80 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
            <polyline points="22 4 12 14.01 9 11.01" />
          </svg>
        </div>
      </div>
      <div class="mt-3">
        <div class="flex items-baseline gap-2">
          <span class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight tabular-nums" id="summary-siswa-sah">
            {{ $keuSummary['siswa_lunas_count'] }}
          </span>
        </div>
      </div>
    </div>
  </div>

  <!-- Sub-Section: RINCIAN ALOKASI DANA PER 7 KATEGORI (POS BIAYA) -->
  <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6 space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
      <div>
        <h3 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
          <span>Alokasi Uang Masuk Per 7 Pos Keuangan</span>
        </h3>
      </div>
    </div>

    <!-- 7 Pos Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-3">
      @foreach ($posConfig as $posKey => $pos)
        @php
          $kasNominal = $keuSummary['kas_kategori'][$posKey] ?? 0;
        @endphp
        <div class="rounded-xl border border-slate-200/80 p-3.5 flex flex-col justify-between hover:border-blue-300 transition">
          <div>
            <h4 class="text-xs font-bold text-slate-800 leading-tight" title="{{ $pos['nama'] }}">
              {{ $pos['singkat'] }}
            </h4>
            <p class="text-[10px] text-slate-500 mt-0.5 line-clamp-1" title="{{ $pos['sub'] }}">
              {{ $pos['sub'] }}
            </p>
          </div>

          <div class="mt-3">
            <span class="text-[10px] text-slate-400 font-medium block">Kas Terkumpul</span>
            <div class="text-sm font-extrabold text-slate-900 tabular-nums">
              Rp {{ number_format($kasNominal, 0, ',', '.') }}
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>

  <!-- ============================================================== -->
  <!-- 3. TABEL DATA KEUANGAN SISWA & FILTER                          -->
  <!-- ============================================================== -->
  <div class="bg-white rounded-[14px] border border-slate-200/90 overflow-hidden">
    <!-- Header Tabel Keuangan & Kas Siswa -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 sm:p-6 border-b border-slate-200/80">
      <div class="flex items-center gap-3.5">
        <div class="w-11 h-11 rounded-[10px] bg-emerald-50 text-emerald-600 border border-emerald-100/90 flex items-center justify-center shrink-0">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <rect width="20" height="12" x="2" y="6" rx="2" />
            <circle cx="12" cy="12" r="2" />
            <path d="M6 12h.01M18 12h.01" />
          </svg>
        </div>
        <div>
          <h2 class="text-lg sm:text-[20px] font-bold text-slate-900 tracking-tight leading-tight">Tabel Keuangan & Kas Siswa</h2>
          <p class="text-[13.5px] text-slate-500 mt-0.5">Daftar calon siswa, rincian tagihan bersih per pos, cicilan, dan status kas siswa.</p>
        </div>
      </div>

      <div class="flex items-center gap-3 self-start sm:self-auto flex-wrap">
        <span class="text-xs font-medium text-slate-500 bg-slate-50 px-3 py-2 rounded-lg border border-slate-200/80">
          Terpilih: <strong id="keu-table-selected-badge" class="font-bold text-slate-800">0</strong> Siswa
        </span>
        <button
          type="button"
          onclick="exportKeuanganToExcel()"
          title="Ekspor Data Keuangan Siswa (Format Excel)"
          class="h-10 px-4 rounded-lg bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold text-sm inline-flex items-center gap-2 transition cursor-pointer active:scale-[0.98]"
        >
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M12 3v12m0 0-4-4m4 4 4-4M4 20h16"></path>
          </svg>
          <span>Ekspor Excel</span>
        </button>
      </div>
    </div>

    <!-- Filter Bar Interaktif: Sort, Search, Filter -->
    <div class="p-4 sm:p-5 border-b border-slate-200/80 flex items-center gap-2.5 bg-white">
      <!-- Sort Order Toggle -->
      <div class="inline-flex items-center p-1 bg-slate-100 rounded-lg border border-slate-200/90 h-10 shrink-0 gap-0.5" role="group" aria-label="Urutan siswa keuangan">
        <button
          type="button"
          id="keu-sort-newest"
          aria-label="Urutkan nomor registrasi terbaru"
          aria-pressed="true"
          onclick="setKeuSortOrder('desc')"
          class="keu-sort-button h-8 w-8 inline-flex items-center justify-center rounded-md bg-white text-blue-600 font-bold transition cursor-pointer"
        >
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="m3 16 4 4 4-4"></path>
            <path d="M7 20V4"></path>
            <path d="M11 4h10"></path>
            <path d="M11 9h7"></path>
            <path d="M11 14h4"></path>
          </svg>
        </button>
        <button
          type="button"
          id="keu-sort-oldest"
          aria-label="Urutkan nomor registrasi terlama"
          aria-pressed="false"
          onclick="setKeuSortOrder('asc')"
          class="keu-sort-button h-8 w-8 inline-flex items-center justify-center rounded-md text-slate-400 hover:text-slate-700 hover:bg-white/60 transition cursor-pointer"
        >
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="m3 8 4-4 4 4"></path>
            <path d="M7 4v16"></path>
            <path d="M11 4h4"></path>
            <path d="M11 9h7"></path>
            <path d="M11 14h10"></path>
          </svg>
        </button>
      </div>

      <!-- Search Input -->
      <label class="relative flex-1 min-w-0">
        <span class="sr-only">Cari siswa keuangan</span>
        <input
          id="keu-search-input"
          type="search"
          placeholder="Cari no. registrasi, nama, atau asal SMP"
          oninput="applyKeuanganFilter()"
          class="w-full h-10 text-sm rounded-lg border border-slate-200/90 bg-white pl-10 pr-3.5 py-2 text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition"
        />
        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <circle cx="11" cy="11" r="7"></circle>
          <path d="m20 20-3.5-3.5"></path>
        </svg>
      </label>

      <!-- Filter Dropdown Trigger & Menu -->
      <div class="relative shrink-0" id="keu-filter-container">
        <button
          type="button"
          id="keu-filter-trigger"
          aria-expanded="false"
          aria-haspopup="true"
          aria-controls="keu-filter-menu"
          class="h-10 px-3.5 rounded-lg border border-slate-200/90 bg-white hover:bg-slate-50 text-slate-700 font-medium text-sm inline-flex items-center gap-2 transition cursor-pointer select-none"
        >
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
          </svg>
          <span>Filter</span>
          <span id="keu-filter-badge" class="hidden px-1.5 py-0.5 text-[11px] font-bold rounded-full bg-blue-600 text-white leading-none">0</span>
          <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="m6 9 6 6 6-6"></path>
          </svg>
        </button>

        <div id="keu-filter-menu" class="hidden absolute top-full mt-2 right-0 w-60 bg-white rounded-xl border border-slate-200/90 shadow-xl p-2 z-40">
          <!-- 1. Category Status Daftar Ulang -->
          <div class="keu-filter-category relative" data-category="status">
            <button type="button" aria-expanded="false" class="keu-filter-category-trigger w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer text-left">
              <span class="flex items-center gap-2 min-w-0">
                <span class="truncate">Status Daftar Ulang</span>
                <span class="keu-filter-active-dot hidden w-2 h-2 rounded-full bg-blue-600 shrink-0"></span>
                <span class="keu-filter-active-label hidden text-[11px] font-semibold text-blue-600 truncate"></span>
              </span>
              <svg class="w-4 h-4 text-slate-400 shrink-0 ml-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="m9 18 6-6-6-6"></path>
              </svg>
            </button>
            <div class="keu-filter-flyout hidden absolute right-full top-0 mr-2 min-w-[220px] bg-white rounded-xl border border-slate-200/90 shadow-xl p-1.5 z-50">
              <div class="px-2.5 py-1 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 mb-1">Status Daftar Ulang</div>
              <button type="button" data-filter-category="status" data-filter-value="" data-filter-label="Semua" onclick="selectKeuFilter('status', '', 'Semua', event)" class="keu-filter-option w-full flex items-center justify-between px-2.5 py-2 rounded-lg text-xs text-left font-bold text-blue-700 bg-blue-50/70">
                <span>Semua Status</span>
                <svg class="keu-filter-check w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </button>
              <button type="button" data-filter-category="status" data-filter-value="Belum Daftar Ulang" data-filter-label="Belum" onclick="selectKeuFilter('status', 'Belum Daftar Ulang', 'Belum', event)" class="keu-filter-option w-full flex items-center justify-between px-2.5 py-2 rounded-lg text-xs text-left font-medium text-slate-700 hover:bg-slate-100">
                <span>Belum Daftar Ulang</span>
                <svg class="keu-filter-check hidden w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </button>
              <button type="button" data-filter-category="status" data-filter-value="Nyicil / Belum Sah" data-filter-label="Nyicil" onclick="selectKeuFilter('status', 'Nyicil / Belum Sah', 'Nyicil', event)" class="keu-filter-option w-full flex items-center justify-between px-2.5 py-2 rounded-lg text-xs text-left font-medium text-slate-700 hover:bg-slate-100">
                <span>Nyicil / Belum Sah</span>
                <svg class="keu-filter-check hidden w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </button>
              <button type="button" data-filter-category="status" data-filter-value="Sah Daftar Ulang" data-filter-label="Sah" onclick="selectKeuFilter('status', 'Sah Daftar Ulang', 'Sah', event)" class="keu-filter-option w-full flex items-center justify-between px-2.5 py-2 rounded-lg text-xs text-left font-medium text-slate-700 hover:bg-slate-100">
                <span>Sah Daftar Ulang</span>
                <svg class="keu-filter-check hidden w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </button>
              <button type="button" data-filter-category="status" data-filter-value="Direfund (Sekolah Negeri)" data-filter-label="Refund" onclick="selectKeuFilter('status', 'Direfund (Sekolah Negeri)', 'Refund', event)" class="keu-filter-option w-full flex items-center justify-between px-2.5 py-2 rounded-lg text-xs text-left font-medium text-slate-700 hover:bg-slate-100">
                <span>Direfund (Negeri)</span>
                <svg class="keu-filter-check hidden w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </button>
            </div>
          </div>

          <!-- 2. Category Status Baju -->
          <div class="keu-filter-category relative" data-category="baju">
            <button type="button" aria-expanded="false" class="keu-filter-category-trigger w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer text-left">
              <span class="flex items-center gap-2 min-w-0">
                <span class="truncate">Status Baju</span>
                <span class="keu-filter-active-dot hidden w-2 h-2 rounded-full bg-blue-600 shrink-0"></span>
                <span class="keu-filter-active-label hidden text-[11px] font-semibold text-blue-600 truncate"></span>
              </span>
              <svg class="w-4 h-4 text-slate-400 shrink-0 ml-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="m9 18 6-6-6-6"></path>
              </svg>
            </button>
            <div class="keu-filter-flyout hidden absolute right-full top-0 mr-2 min-w-[220px] bg-white rounded-xl border border-slate-200/90 shadow-xl p-1.5 z-50">
              <div class="px-2.5 py-1 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 mb-1">Status Baju / Seragam</div>
              <button type="button" data-filter-category="baju" data-filter-value="" data-filter-label="Semua" onclick="selectKeuFilter('baju', '', 'Semua', event)" class="keu-filter-option w-full flex items-center justify-between px-2.5 py-2 rounded-lg text-xs text-left font-bold text-blue-700 bg-blue-50/70">
                <span>Semua Status Baju</span>
                <svg class="keu-filter-check w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </button>
              <button type="button" data-filter-category="baju" data-filter-value="Siap Ambil Baju" data-filter-label="Siap Ambil" onclick="selectKeuFilter('baju', 'Siap Ambil Baju', 'Siap Ambil', event)" class="keu-filter-option w-full flex items-center justify-between px-2.5 py-2 rounded-lg text-xs text-left font-medium text-slate-700 hover:bg-slate-100">
                <span>Siap Ambil Baju</span>
                <svg class="keu-filter-check hidden w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </button>
              <button type="button" data-filter-category="baju" data-filter-value="Belum Lunas" data-filter-label="Belum Lunas" onclick="selectKeuFilter('baju', 'Belum Lunas', 'Belum Lunas', event)" class="keu-filter-option w-full flex items-center justify-between px-2.5 py-2 rounded-lg text-xs text-left font-medium text-slate-700 hover:bg-slate-100">
                <span>Belum Lunas</span>
                <svg class="keu-filter-check hidden w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </button>
            </div>
          </div>

          <!-- 3. Category Jurusan -->
          <div class="keu-filter-category relative" data-category="jurusan">
            <button type="button" aria-expanded="false" class="keu-filter-category-trigger w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer text-left">
              <span class="flex items-center gap-2 min-w-0">
                <span class="truncate">Jurusan</span>
                <span class="keu-filter-active-dot hidden w-2 h-2 rounded-full bg-blue-600 shrink-0"></span>
                <span class="keu-filter-active-label hidden text-[11px] font-semibold text-blue-600 truncate"></span>
              </span>
              <svg class="w-4 h-4 text-slate-400 shrink-0 ml-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="m9 18 6-6-6-6"></path>
              </svg>
            </button>
            <div class="keu-filter-flyout hidden absolute right-full top-0 mr-2 min-w-[220px] max-h-[280px] overflow-y-auto bg-white rounded-xl border border-slate-200/90 shadow-xl p-1.5 z-50">
              <div class="px-2.5 py-1 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 mb-1">Jurusan / Program Keahlian</div>
              <button type="button" data-filter-category="jurusan" data-filter-value="" data-filter-label="Semua" onclick="selectKeuFilter('jurusan', '', 'Semua', event)" class="keu-filter-option w-full flex items-center justify-between px-2.5 py-2 rounded-lg text-xs text-left font-bold text-blue-700 bg-blue-50/70">
                <span>Semua Jurusan</span>
                <svg class="keu-filter-check w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </button>
              @foreach ($jurusanList as $jur)
                <button type="button" data-filter-category="jurusan" data-filter-value="{{ $jur->id_jurusan }}" data-filter-label="{{ $jur->nama_jurusan }}" onclick="selectKeuFilter('jurusan', '{{ $jur->id_jurusan }}', '{{ addslashes($jur->nama_jurusan) }}', event)" class="keu-filter-option w-full flex items-center justify-between px-2.5 py-2 rounded-lg text-xs text-left font-medium text-slate-700 hover:bg-slate-100">
                  <span class="truncate">{{ $jur->nama_jurusan }}</span>
                  <svg class="keu-filter-check hidden w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </button>
              @endforeach
            </div>
          </div>

          <!-- 4. Category Gelombang -->
          <div class="keu-filter-category relative" data-category="gelombang">
            <button type="button" aria-expanded="false" class="keu-filter-category-trigger w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer text-left">
              <span class="flex items-center gap-2 min-w-0">
                <span class="truncate">Gelombang</span>
                <span class="keu-filter-active-dot hidden w-2 h-2 rounded-full bg-blue-600 shrink-0"></span>
                <span class="keu-filter-active-label hidden text-[11px] font-semibold text-blue-600 truncate"></span>
              </span>
              <svg class="w-4 h-4 text-slate-400 shrink-0 ml-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="m9 18 6-6-6-6"></path>
              </svg>
            </button>
            <div class="keu-filter-flyout hidden absolute right-full top-0 mr-2 min-w-[220px] max-h-[280px] overflow-y-auto bg-white rounded-xl border border-slate-200/90 shadow-xl p-1.5 z-50">
              <div class="px-2.5 py-1 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 mb-1">Gelombang Pendaftaran</div>
              <button type="button" data-filter-category="gelombang" data-filter-value="" data-filter-label="Semua" onclick="selectKeuFilter('gelombang', '', 'Semua', event)" class="keu-filter-option w-full flex items-center justify-between px-2.5 py-2 rounded-lg text-xs text-left font-bold text-blue-700 bg-blue-50/70">
                <span>Semua Gelombang</span>
                <svg class="keu-filter-check w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </button>
              @foreach ($gelombangList as $gel)
                <button type="button" data-filter-category="gelombang" data-filter-value="{{ $gel->id_gelombang }}" data-filter-label="{{ $gel->nama_gelombang }}" onclick="selectKeuFilter('gelombang', '{{ $gel->id_gelombang }}', '{{ addslashes($gel->nama_gelombang) }}', event)" class="keu-filter-option w-full flex items-center justify-between px-2.5 py-2 rounded-lg text-xs text-left font-medium text-slate-700 hover:bg-slate-100">
                  <span class="truncate">{{ $gel->nama_gelombang }}</span>
                  <svg class="keu-filter-check hidden w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </button>
              @endforeach
            </div>
          </div>

          <!-- 5. Category Jalur -->
          <div class="keu-filter-category relative" data-category="jalur">
            <button type="button" aria-expanded="false" class="keu-filter-category-trigger w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer text-left">
              <span class="flex items-center gap-2 min-w-0">
                <span class="truncate">Jalur</span>
                <span class="keu-filter-active-dot hidden w-2 h-2 rounded-full bg-blue-600 shrink-0"></span>
                <span class="keu-filter-active-label hidden text-[11px] font-semibold text-blue-600 truncate"></span>
              </span>
              <svg class="w-4 h-4 text-slate-400 shrink-0 ml-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="m9 18 6-6-6-6"></path>
              </svg>
            </button>
            <div class="keu-filter-flyout hidden absolute right-full top-0 mr-2 min-w-[220px] max-h-[280px] overflow-y-auto bg-white rounded-xl border border-slate-200/90 shadow-xl p-1.5 z-50">
              <div class="px-2.5 py-1 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 mb-1">Jalur Pendaftaran</div>
              <button type="button" data-filter-category="jalur" data-filter-value="" data-filter-label="Semua" onclick="selectKeuFilter('jalur', '', 'Semua', event)" class="keu-filter-option w-full flex items-center justify-between px-2.5 py-2 rounded-lg text-xs text-left font-bold text-blue-700 bg-blue-50/70">
                <span>Semua Jalur</span>
                <svg class="keu-filter-check w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </button>
              @foreach ($jalurList as $jal)
                <button type="button" data-filter-category="jalur" data-filter-value="{{ $jal->id_jalur }}" data-filter-label="{{ $jal->nama_jalur }}" onclick="selectKeuFilter('jalur', '{{ $jal->id_jalur }}', '{{ addslashes($jal->nama_jalur) }}', event)" class="keu-filter-option w-full flex items-center justify-between px-2.5 py-2 rounded-lg text-xs text-left font-medium text-slate-700 hover:bg-slate-100">
                  <span class="truncate">{{ $jal->nama_jalur }}</span>
                  <svg class="keu-filter-check hidden w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </button>
              @endforeach
            </div>
          </div>

          <div id="keu-filter-reset-divider" class="hidden border-t border-slate-100 my-1.5"></div>
          <button
            type="button"
            id="keu-filter-reset-btn"
            onclick="resetKeuFilters(event)"
            class="hidden w-full flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition cursor-pointer select-none"
          >
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
              <path d="M3 3v5h5"></path>
            </svg>
            <span>Reset Filter</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Contextual Bulk Action Bar -->
    <div id="keu-bulk-action-bar" class="hidden items-center justify-between gap-3 px-5 py-3 bg-blue-50/90 border-b border-blue-200 transition-all duration-200">
      <div class="flex items-center gap-2">
        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white font-bold text-xs" id="keu-bulk-selected-count">0</span>
        <span class="text-xs font-bold text-blue-900">Siswa Terpilih</span>
        <span class="text-xs text-blue-700 hidden sm:inline">• Tindakan serentak:</span>
      </div>
      <div class="flex items-center gap-2 flex-wrap">
        <button
          type="button"
          onclick="exportSelectedKeuangan()"
          class="h-8 px-3 rounded-lg bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 font-bold text-xs inline-flex items-center gap-1.5 transition active:scale-95 cursor-pointer"
        >
          <svg class="w-3.5 h-3.5 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v12m0 0-4-4m4 4 4-4M4 20h16"></path></svg>
          <span>Ekspor Excel</span>
        </button>
        <button
          type="button"
          onclick="openBulkResetModal()"
          class="h-8 px-3 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 font-bold text-xs inline-flex items-center gap-1.5 transition active:scale-95 cursor-pointer"
        >
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path></svg>
          <span>Reset Tagihan Terpilih</span>
        </button>
        <button
          type="button"
          onclick="openBulkDeleteModal()"
          class="h-8 px-3 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 font-bold text-xs inline-flex items-center gap-1.5 transition active:scale-95 cursor-pointer"
        >
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"></path><path d="M8 6V4h8v2"></path><path d="m19 6-1 14H6L5 6"></path><path d="M10 11v5"></path><path d="M14 11v5"></path></svg>
          <span>Hapus Terpilih</span>
        </button>
        <button
          type="button"
          onclick="clearKeuSelections()"
          class="h-8 px-2.5 rounded-lg text-slate-500 hover:text-slate-700 text-xs font-semibold cursor-pointer"
          title="Batalkan pilihan"
        >
          Batal
        </button>
      </div>
    </div>

    <!-- Tabel Data Siswa -->
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse" id="keu-table">
        <thead>
          <tr class="bg-slate-50/90 text-slate-600 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider">
            <th scope="col" data-export-ignore class="w-11 px-3 py-3.5 text-center">
              <input
                id="keu-select-all"
                type="checkbox"
                aria-label="Pilih semua siswa keuangan"
                class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500 cursor-pointer"
              />
            </th>
            <th class="py-3 px-2 w-10 text-center">No</th>
            <th class="py-3 px-4 min-w-[200px]">Calon Siswa</th>
            <th class="py-3 px-4 text-right min-w-[130px]">Tagihan Bersih</th>
            <th class="py-3 px-4 text-right min-w-[130px]">Sudah Dibayar</th>
            <th class="py-3 px-4 text-right min-w-[120px]">Sisa Tagihan</th>
            <th class="py-3 px-4 text-center min-w-[130px]">Status Baju</th>
            <th class="py-3 px-4 text-center min-w-[140px]">Status Daftar Ulang</th>
            <th class="py-3 px-4 text-center min-w-[150px]">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-xs text-slate-800" id="keu-table-body">
          @php
            $displayKeuanganList = isset($verifiedStudents) ? $verifiedStudents : $pendaftaranList->filter(fn ($p) => $p->status_berkas === 'Diterima')->values();
          @endphp
          @forelse ($displayKeuanganList as $index => $item)
            @php
              $billing = $pendaftaranBilling[$item->no_registrasi] ?? app(\App\Services\KeuanganService::class)->calculateStudentBilling($item);
              $student = $item->calonSiswa;
              $jurusanName = $item->jurusan?->nama_jurusan ?? '-';
              $jalurName = $item->jalur?->nama_jalur ?? '-';
              $gelombangName = $item->gelombang?->nama_gelombang ?? '-';

              $paidPct = $billing['total_tagihan_bersih'] > 0
                  ? min(100, round(($billing['total_sudah_dibayar'] / $billing['total_tagihan_bersih']) * 100))
                  : 0;
            @endphp
            <tr
              id="keu-row-{{ $item->no_registrasi }}"
              class="keu-data-row hover:bg-slate-50/80 transition"
              data-registration="{{ $item->no_registrasi }}"
              data-name="{{ strtolower($student?->nama_lengkap ?? '') }}"
              data-school="{{ strtolower($student?->asal_sekolah ?? '') }}"
              data-jurusan="{{ $item->id_jurusan }}"
              data-jurusan-name="{{ $jurusanName }}"
              data-jalur="{{ $item->id_jalur }}"
              data-jalur-name="{{ $jalurName }}"
              data-gelombang="{{ $item->id_gelombang }}"
              data-gelombang-name="{{ $gelombangName }}"
              data-status="{{ $billing['status_daftar_ulang'] }}"
              data-baju="{{ $billing['status_baju'] }}"
            >
              <!-- Checkbox -->
              <td class="w-11 px-3 py-3.5 text-center" data-export-ignore>
                <input
                  type="checkbox"
                  data-keu-select
                  value="{{ $item->no_registrasi }}"
                  aria-label="Pilih siswa {{ $student?->nama_lengkap }}"
                  class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500 cursor-pointer"
                />
              </td>

              <!-- No / Index -->
              <td class="py-3.5 px-2 text-center text-slate-400 font-bold tabular-nums keu-row-no">
                {{ $index + 1 }}
              </td>

              <!-- Calon Siswa (Nama, No Reg, Asal SMP) -->
              <td class="py-3.5 px-4">
                <div class="flex flex-col">
                  <div class="flex items-center gap-1.5">
                    <span class="font-extrabold text-slate-900 leading-tight">
                      {{ $student?->nama_lengkap ?? 'Tanpa Nama' }}
                    </span>
                  </div>
                  <div class="flex items-center gap-2 mt-0.5 text-[11px] text-slate-500">
                    <span class="font-mono font-bold text-blue-700 bg-blue-50 px-1 rounded">
                      #{{ $item->no_registrasi }}
                    </span>
                    <span>&bull;</span>
                    <span class="truncate max-w-[180px]" title="{{ $student?->asal_sekolah ?? '-' }}">
                      {{ $student?->asal_sekolah ?? '-' }}
                    </span>
                  </div>
                </div>
              </td>

              <!-- Total Tagihan Bersih -->
              <td class="py-3.5 px-4 text-right tabular-nums">
                <div class="flex flex-col items-end">
                  <span class="font-extrabold text-slate-900 text-xs sm:text-sm">
                    Rp {{ number_format($billing['total_tagihan_bersih'], 0, ',', '.') }}
                  </span>
                  @if ($billing['total_diskon'] > 0)
                    <span class="text-[10px] text-slate-400 line-through">
                      Rp {{ number_format($billing['total_dasar'], 0, ',', '.') }}
                    </span>
                  @else
                    <span class="text-[10px] text-slate-400">Tarif Normal</span>
                  @endif
                </div>
              </td>

              <!-- Sudah Dibayar -->
              <td class="py-3.5 px-4 text-right tabular-nums">
                <div class="flex flex-col items-end">
                  <span class="font-bold text-emerald-600 text-xs sm:text-sm">
                    Rp {{ number_format($billing['total_sudah_dibayar'], 0, ',', '.') }}
                  </span>
                  <!-- Mini Progress Bar -->
                  <div class="w-20 bg-slate-100 rounded-full h-1.5 mt-1 overflow-hidden">
                    <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $paidPct }}%"></div>
                  </div>
                  <span class="text-[10px] text-slate-400 mt-0.5">{{ $paidPct }}% terbayar</span>
                </div>
              </td>

              <!-- Sisa Tagihan -->
              <td class="keu-col-sisa py-3.5 px-4 text-right tabular-nums">
                @if ($billing['total_sisa'] <= 0)
                  <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Lunas
                  </span>
                @else
                  <span class="font-bold text-amber-600 text-xs sm:text-sm">
                    Rp {{ number_format($billing['total_sisa'], 0, ',', '.') }}
                  </span>
                @endif
              </td>

              <!-- Status Baju -->
              <td class="py-3.5 px-4 text-center">
                @if ($billing['status_baju'] === 'Siap Ambil Baju')
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>Siap Ambil</span>
                  </span>
                @else
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span>Belum Lunas</span>
                  </span>
                @endif
              </td>

              <!-- Status Daftar Ulang -->
              <td class="py-3.5 px-4 text-center">
                @if ($billing['status_daftar_ulang'] === 'Sah Daftar Ulang')
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>Sah Lunas</span>
                  </span>
                @elseif ($billing['status_daftar_ulang'] === 'Direfund (Sekolah Negeri)')
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/></svg>
                    <span>Direfund</span>
                  </span>
                @elseif ($billing['status_daftar_ulang'] === 'Nyicil / Belum Sah')
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span>Nyicil</span>
                  </span>
                @else
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-medium bg-slate-100 text-slate-600 border border-slate-200">
                    Belum Daftar Ulang
                  </span>
                @endif
              </td>

              <!-- Aksi -->
              <td class="py-3.5 px-4 text-center">
                <div class="flex items-center justify-center gap-1.5">
                  <!-- Tombol Catat Pembayaran / Status Lunas -->
                  @if ($billing['total_sisa'] <= 0)
                    <button
                      type="button"
                      onclick="openDetailModal('{{ $item->no_registrasi }}')"
                      title="Siswa sudah LUNAS seluruh tagihan. Klik untuk lihat rincian & kuitansi."
                      class="h-8 px-2.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 font-bold text-[11px] inline-flex items-center gap-1 transition active:scale-95 cursor-pointer"
                    >
                      <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                      <span>Lunas</span>
                    </button>
                  @else
                    <button
                      type="button"
                      onclick="openPaymentModal('{{ $item->no_registrasi }}')"
                      title="Catat Pembayaran Baru Siswa Ini"
                      class="h-8 px-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-[11px] inline-flex items-center gap-1 transition active:scale-95 cursor-pointer"
                    >
                      <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="20" height="14" x="2" y="5" rx="2" />
                        <line x1="2" x2="22" y1="10" y2="10" />
                      </svg>
                      <span>Bayar</span>
                    </button>
                  @endif

                  <!-- Tombol Detail & Riwayat Kuitansi -->
                  <button
                    type="button"
                    onclick="openDetailModal('{{ $item->no_registrasi }}')"
                    title="Rincian 7 Pos, Riwayat Kuitansi & Refund"
                    class="h-8 px-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px] inline-flex items-center gap-1 transition active:scale-95 cursor-pointer border border-slate-200"
                  >
                    <svg class="w-3.5 h-3.5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                      <circle cx="12" cy="12" r="3"/>
                    </svg>
                    <span>Rincian</span>
                  </button>

                  <!-- Menu Dropdown Opsi Lainnya -->
                  <div class="relative inline-block text-left keu-action-dropdown-container">
                    <button
                      type="button"
                      onclick="toggleKeuActionMenu('{{ $item->no_registrasi }}', event)"
                      class="h-8 w-8 rounded-lg bg-white hover:bg-slate-100 text-slate-600 hover:text-slate-900 inline-flex items-center justify-center transition active:scale-95 cursor-pointer border border-slate-200"
                      title="Pilihan Aksi Lainnya"
                    >
                      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/></svg>
                    </button>
                    <div id="keu-action-menu-{{ $item->no_registrasi }}" class="keu-action-menu hidden absolute right-0 mt-1 w-52 bg-white rounded-xl shadow-xl border border-slate-200/90 py-1.5 z-30 text-left">
                      <button
                        type="button"
                        onclick="openDetailModal('{{ $item->no_registrasi }}'); closeAllKeuActionMenus();"
                        class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer text-left"
                      >
                        <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        <span>Lihat Riwayat & Kuitansi</span>
                      </button>
                      <button
                        type="button"
                        onclick="openResetPaymentModal('{{ $item->no_registrasi }}', '{{ addslashes($student?->nama_lengkap ?? '') }}', {{ (int) $billing['total_sudah_dibayar'] }}); closeAllKeuActionMenus();"
                        class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-amber-700 hover:bg-amber-50 transition cursor-pointer text-left"
                      >
                        <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                        <span>Reset / Void Pembayaran</span>
                      </button>
                      <div class="border-t border-slate-100 my-1"></div>
                      <button
                        type="button"
                        onclick="openDeleteStudentModal('{{ $item->no_registrasi }}', '{{ addslashes($student?->nama_lengkap ?? '') }}'); closeAllKeuActionMenus();"
                        class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition cursor-pointer text-left"
                      >
                        <svg class="w-3.5 h-3.5 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="m19 6-1 14H6L5 6"/></svg>
                        <span>Hapus Siswa & Tagihan</span>
                      </button>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="py-12 text-center text-slate-400">
                Belum ada data pendaftar untuk dicatat keuangannya.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Empty Filter Notice -->
    <div id="keu-empty-filter-state" class="hidden py-12 text-center text-slate-500">
      <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <circle cx="11" cy="11" r="8"/>
        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
      <p class="font-bold text-slate-700">Tidak ada siswa yang sesuai filter</p>
      <p class="text-xs text-slate-400 mt-0.5">Coba ubah kata kunci pencarian atau sesuaikan pilihan filter.</p>
    </div>

    <!-- Footer Tabel Keuangan & Kas Siswa -->
    <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-3.5 bg-white border-t border-slate-200/90 text-xs sm:text-[13px] text-slate-500">
      <span id="keu-table-count">Menampilkan <span id="keu-visible-count" class="tabular-nums font-normal">0</span> pendaftar</span>

      <nav class="flex items-center gap-1.5" aria-label="Navigasi Halaman Keuangan">
        <button
          type="button"
          id="keu-page-prev"
          onclick="changeKeuPage(currentKeuPage - 1)"
          title="Halaman sebelumnya"
          class="w-9 h-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-300 cursor-not-allowed select-none transition"
          disabled
        >
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="m15 18-6-6 6-6"></path>
          </svg>
        </button>

        <span id="keu-page-indicator" class="px-2.5 py-1 text-xs font-semibold text-slate-600 select-none tabular-nums">1 / 1</span>

        <button
          type="button"
          id="keu-page-next"
          onclick="changeKeuPage(currentKeuPage + 1)"
          title="Halaman selanjutnya"
          class="w-9 h-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-300 cursor-not-allowed select-none transition"
          disabled
        >
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="m9 18 6-6-6-6"></path>
          </svg>
        </button>
      </nav>
    </div>
  </div>
</section>

<!-- ============================================================== -->
<!-- 4. MODAL CATAT PEMBAYARAN (ALOKASI 7 KATEGORI POS BIAYA)       -->
<!-- ============================================================== -->
<div
  id="keu-payment-modal"
  class="fixed inset-0 z-[80] hidden bg-slate-900/60 items-center justify-center p-3 sm:p-4 no-print overflow-y-auto"
  onclick="if (event.target === this) closePaymentModal();"
>
  <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-2xl w-full my-auto flex flex-col max-h-[92vh] overflow-hidden">
    <!-- Header Modal -->
    <div class="px-5 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-900 via-slate-800 to-blue-900 text-white flex items-center justify-between sticky top-0 z-20 shrink-0">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-blue-500/20 text-blue-400 border border-blue-400/30 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect width="20" height="14" x="2" y="5" rx="2" />
            <line x1="2" x2="22" y1="10" y2="10" />
          </svg>
        </div>
        <div>
          <h3 class="text-base font-extrabold tracking-tight" id="keu-modal-student-name">
            Catat Pembayaran Siswa
          </h3>
          <p class="text-xs text-slate-300 font-medium" id="keu-modal-student-meta">
            No. Reg: - &bull; Jurusan: -
          </p>
        </div>
      </div>
      <button
        type="button"
        onclick="closePaymentModal()"
        class="text-slate-300 hover:text-white p-1.5 rounded-lg hover:bg-white/10 transition cursor-pointer"
      >
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <!-- Body Modal: Scrollable -->
    <div class="p-5 overflow-y-auto flex-1 space-y-4 text-xs">
      <!-- Section: Penyesuaian Diskon SPP Manual (Kebijakan Kepala Sekolah) -->
      <div id="keu-spp-custom-wrapper" class="rounded-xl border border-slate-200 bg-slate-50/60 p-3.5 space-y-2.5">
        <div class="flex items-center justify-between cursor-pointer" onclick="toggleCustomSppAccordion()">
          <div class="flex items-center gap-2">
            <span class="w-5 h-5 rounded-md bg-amber-100 text-amber-700 font-bold flex items-center justify-center text-[10px]">%</span>
            <span class="font-bold text-slate-800 text-xs">Penyesuaian Diskon SPP Khusus (Kebijakan Kepala Sekolah)</span>
          </div>
          <span class="text-slate-400 hover:text-slate-700 transition" id="spp-accordion-icon">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
          </span>
        </div>
        <div id="spp-custom-panel" class="hidden pt-2 border-t border-slate-200/80 space-y-2">
          <p class="text-[11px] text-slate-500">
            Khusus jalur kebijakan Kepala Sekolah (Afirmasi, Inklusi, Minat Bakat, Kerabat, dll), Admin dapat menyesuaikan persentase diskon SPP secara manual.
          </p>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
            <div>
              <label class="block text-[10px] font-bold text-slate-600 mb-0.5">Persentase Diskon SPP (%)</label>
              <input
                type="number"
                id="custom-spp-percent-input"
                min="0"
                max="100"
                step="1"
                placeholder="Contoh: 50"
                class="w-full h-8 text-xs rounded-lg border border-slate-300 bg-white px-2 font-bold text-slate-800 focus:outline-none focus:border-blue-600"
              />
            </div>
            <div class="sm:col-span-2">
              <label class="block text-[10px] font-bold text-slate-600 mb-0.5">Alasan / Kebijakan Kepsek</label>
              <input
                type="text"
                id="custom-spp-note-input"
                placeholder="Contoh: Surat Rekomendasi Yayasan No. 12"
                class="w-full h-8 text-xs rounded-lg border border-slate-300 bg-white px-2 text-slate-800 focus:outline-none focus:border-blue-600"
              />
            </div>
          </div>
          <div class="text-right pt-1">
            <button
              type="button"
              onclick="applyCustomSppDiscount()"
              class="px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 active:bg-amber-800 text-white font-bold text-xs transition cursor-pointer"
            >
              Terapkan Diskon SPP
            </button>
          </div>
        </div>
      </div>

      <!-- Daftar 7 Pos Kategori Pembayaran -->
      <div class="space-y-2">
        <label class="block text-xs font-extrabold text-slate-800 uppercase tracking-wider">
          Alokasi Pembayaran Per 7 Pos Kategori:
        </label>

        <div class="divide-y divide-slate-100 rounded-xl border border-slate-200 overflow-hidden bg-white" id="keu-allocation-rows-container">
          <!-- Rows will be dynamically rendered / updated via JS -->
        </div>
      </div>

      <!-- Banner Siswa Sudah Lunas Penuh -->
      <div id="keu-modal-all-lunas-banner" class="hidden p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0 text-emerald-600">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          </div>
          <div>
            <h4 class="font-extrabold text-sm text-emerald-900">Seluruh Biaya Siswa Ini Sudah LUNAS</h4>
            <p class="text-xs text-emerald-700 mt-0.5">Semua 7 pos keuangan telah terbayar penuh. Tidak ada sisa tagihan yang perlu dibayar.</p>
          </div>
        </div>
        <button
          type="button"
          onclick="closePaymentModal(); openDetailModal(window.keuanganState.currentStudentReg);"
          class="px-3.5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition inline-flex items-center gap-1.5 shrink-0 cursor-pointer active:scale-95"
        >
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          <span>Lihat Kuitansi & Rincian</span>
        </button>
      </div>

      <!-- Form Pengisian Pembayaran (Disembunyikan jika sudah lunas) -->
      <div id="keu-payment-form-fields" class="space-y-4">
        <!-- Live Total Calculation Bar -->
        <div id="keu-live-total-bar" class="p-3.5 rounded-xl bg-slate-900 text-white flex items-center justify-between gap-3 shadow-md">
          <div>
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Nominal Yang Akan Dibayar</span>
            <span class="text-lg sm:text-xl font-black text-emerald-400 tabular-nums" id="keu-modal-total-input">
              Rp 0
            </span>
          </div>
          <div class="text-right text-[11px] text-slate-300">
            <span class="block">Jumlah Pos Diisi: <strong id="keu-modal-category-count" class="text-white">0</strong></span>
          </div>
        </div>

        <!-- Detail Transaksi (Metode, Tanggal, Catatan) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
          <div>
            <label class="block text-[11px] font-bold text-slate-700 mb-1">Metode Pembayaran <span class="text-rose-500">*</span></label>
            <select
              id="keu-modal-method"
              class="w-full h-9 rounded-xl border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-800 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-100 transition cursor-pointer"
            >
              <option value="Tunai">Tunai (Cash di Loket)</option>
              <option value="Transfer Bank BCA">Transfer Bank BCA</option>
              <option value="Transfer Bank BPD Bali">Transfer Bank BPD Bali</option>
              <option value="Transfer Bank BRI">Transfer Bank BRI</option>
              <option value="Transfer Bank Mandiri">Transfer Bank Mandiri</option>
              <option value="Transfer Lainnya / QRIS">Transfer Lainnya / QRIS</option>
            </select>
          </div>

          <div>
            <label class="block text-[11px] font-bold text-slate-700 mb-1">Tanggal Pembayaran <span class="text-rose-500">*</span></label>
            <input
              type="date"
              id="keu-modal-date"
              value="{{ now('Asia/Makassar')->format('Y-m-d') }}"
              class="w-full h-9 rounded-xl border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-800 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-100 transition"
            />
          </div>

          <div class="sm:col-span-2">
            <label class="block text-[11px] font-bold text-slate-700 mb-1">Catatan Transaksi (Opsional)</label>
            <input
              type="text"
              id="keu-modal-notes"
              placeholder="Contoh: Cicilan tahap 1 seragam dan OSIS dari orang tua..."
              class="w-full h-9 rounded-xl border border-slate-300 bg-white px-3 text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-100 transition"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Footer Modal -->
    <div class="px-5 py-3.5 border-t border-slate-200 bg-slate-50 flex items-center justify-between gap-3 sticky bottom-0 z-20 shrink-0">
      <button
        type="button"
        id="keu-modal-close-btn"
        onclick="closePaymentModal()"
        class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 border border-slate-300 transition cursor-pointer"
      >
        Batal
      </button>

      <div class="flex items-center gap-4">
        <div class="text-right" id="keu-modal-footer-total-container">
          <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Total Pembayaran</span>
          <span class="text-base sm:text-lg font-black text-blue-700 tabular-nums" id="keu-modal-footer-total">
            Rp 0
          </span>
        </div>

        <button
          type="button"
          id="keu-modal-submit-btn"
          onclick="submitPaymentRecord()"
          class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-extrabold text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 shadow-md transition cursor-pointer active:scale-95 disabled:opacity-50"
        >
          <span id="keu-modal-submit-label">Simpan Pembayaran & Kuitansi</span>
          <svg id="keu-modal-spinner" class="hidden w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ============================================================== -->
<!-- 5. MODAL DETAIL KEUANGAN, RIWAYAT TRANSAKSI & REFUND           -->
<!-- ============================================================== -->
<div
  id="keu-detail-modal"
  class="fixed inset-0 z-[80] hidden bg-slate-900/60 items-center justify-center p-3 sm:p-4 no-print overflow-y-auto"
  onclick="if (event.target === this) closeDetailModal();"
>
  <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-3xl w-full my-auto flex flex-col max-h-[92vh] overflow-hidden">
    <!-- Header Modal Detail -->
    <div class="px-5 py-4 border-b border-slate-200 bg-white flex items-center justify-between sticky top-0 z-20 shrink-0">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
        </div>
        <div>
          <h3 class="text-base font-extrabold text-slate-900 tracking-tight" id="detail-student-name">
            Rincian Keuangan Siswa
          </h3>
          <p class="text-xs text-slate-500" id="detail-student-meta">
            No. Reg: - &bull; Jurusan: -
          </p>
        </div>
      </div>
      <button
        type="button"
        onclick="closeDetailModal()"
        class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer"
      >
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <!-- Body Modal Detail -->
    <div class="p-5 overflow-y-auto flex-1 space-y-5 text-xs">
      <!-- Status Badges Bar -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 p-3.5 rounded-xl bg-slate-50 border border-slate-200">
        <div>
          <span class="text-[10px] text-slate-400 uppercase font-bold block">Tagihan Bersih</span>
          <span class="text-xs sm:text-sm font-black text-slate-900 tabular-nums" id="detail-tagihan-bersih">-</span>
        </div>
        <div>
          <span class="text-[10px] text-slate-400 uppercase font-bold block">Total Dibayar</span>
          <span class="text-xs sm:text-sm font-black text-emerald-600 tabular-nums" id="detail-sudah-bayar">-</span>
        </div>
        <div>
          <span class="text-[10px] text-slate-400 uppercase font-bold block">Sisa Tagihan</span>
          <span class="text-xs sm:text-sm font-black text-amber-600 tabular-nums" id="detail-sisa-tagihan">-</span>
        </div>
        <div>
          <span class="text-[10px] text-slate-400 uppercase font-bold block">Status Baju</span>
          <span class="text-xs font-bold" id="detail-status-baju">-</span>
        </div>
      </div>

      <!-- Breakdown 7 Kategori Tabel -->
      <div>
        <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-2">
          Rincian Pos Biaya Tagihan (7 Kategori):
        </h4>
        <div class="rounded-xl border border-slate-200 overflow-hidden">
          <table class="w-full text-left text-xs border-collapse">
            <thead class="bg-slate-50 text-[11px] font-bold text-slate-600 border-b border-slate-200">
              <tr>
                <th class="py-2.5 px-3">Pos Kategori</th>
                <th class="py-2.5 px-3 text-right">Tarif Dasar</th>
                <th class="py-2.5 px-3 text-right">Tagihan Bersih</th>
                <th class="py-2.5 px-3 text-right">Sudah Bayar</th>
                <th class="py-2.5 px-3 text-right">Sisa</th>
                <th class="py-2.5 px-3 text-center">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100" id="detail-categories-table-body">
              <!-- Dynamically populated -->
            </tbody>
          </table>
        </div>
      </div>

      <!-- Riwayat Kuitansi Transaksi -->
      <div>
        <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-2 flex items-center justify-between">
          <span>Riwayat Transaksi & Kuitansi Siswa:</span>
          <span class="text-[11px] font-semibold text-slate-400" id="detail-history-count">0 Transaksi</span>
        </h4>
        <div class="space-y-2" id="detail-history-list">
          <!-- Dynamically populated -->
        </div>
      </div>

      <!-- SECTION 5: FITUR PENGEMBALIAN BIAYA / REFUND (DITERIMA DI SEKOLAH NEGERI) -->
      <div class="rounded-xl border border-rose-200 bg-rose-50/50 p-4 space-y-3">
        <div class="flex items-start gap-3">
          <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/></svg>
          </div>
          <div>
            <h5 class="text-xs font-extrabold text-rose-900">
              Pengembalian Biaya / Refund (Diterima di Sekolah Negeri)
            </h5>
            <p class="text-[11px] text-rose-700 mt-0.5 leading-relaxed">
              Aturan Sekolah: Siswa yang diterima di sekolah negeri sebelum MPLS selesai berhak atas pengembalian dana,
              <strong>KECUALI</strong> biaya <strong>"Pendaftaran"</strong> dan <strong>"Seragam / Baju"</strong>.
            </p>
          </div>
        </div>

        <!-- Perhitungan Refund Live Preview -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-1 border-t border-rose-200/60 text-[11px]">
          <div>
            <span class="text-slate-500 block">Total Masuk:</span>
            <strong class="text-slate-900" id="refund-box-total-paid">Rp 0</strong>
          </div>
          <div>
            <span class="text-rose-600 block">Pendaftaran (Hangus):</span>
            <strong class="text-rose-700" id="refund-box-pendaftaran">Rp 0</strong>
          </div>
          <div>
            <span class="text-rose-600 block">Seragam (Hangus):</span>
            <strong class="text-rose-700" id="refund-box-seragam">Rp 0</strong>
          </div>
          <div class="bg-rose-100/70 p-1.5 rounded-lg border border-rose-200">
            <span class="text-rose-800 font-bold block">Hak Uang Kembali:</span>
            <strong class="text-xs font-black text-rose-900 tabular-nums" id="refund-box-amount">Rp 0</strong>
          </div>
        </div>

        <div class="text-right pt-1" id="refund-action-container">
          <button
            type="button"
            id="btn-trigger-refund"
            onclick="openRefundConfirmationModal()"
            class="px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white font-bold text-xs inline-flex items-center gap-1.5 transition cursor-pointer active:scale-95"
          >
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/></svg>
            <span>Proses Refund (Diterima Negeri)</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Footer Modal Detail -->
    <div class="px-5 py-3 border-t border-slate-200 bg-slate-50 flex items-center justify-end sticky bottom-0 z-20 shrink-0">
      <button
        type="button"
        onclick="closeDetailModal()"
        class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 border border-slate-300 transition cursor-pointer"
      >
        Tutup
      </button>
    </div>
  </div>
</div>

<!-- ============================================================== -->
<!-- 6. MODAL KONFIRMASI REFUND DANA SISWA                          -->
<!-- ============================================================== -->
<div
  id="keu-refund-modal"
  class="fixed inset-0 z-[90] hidden bg-slate-900/60 items-center justify-center p-4 no-print"
  onclick="if (event.target === this) closeRefundConfirmationModal();"
>
  <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-md w-full p-5 sm:p-6 space-y-4">
    <div class="w-12 h-12 rounded-full bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center mx-auto">
      <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/></svg>
    </div>

    <div class="text-center">
      <h3 class="text-base font-extrabold text-slate-900">Konfirmasi Proses Refund</h3>
      <p class="text-xs text-slate-500 mt-1">
        Pengembalian dana untuk siswa yang diterima di sekolah negeri sebelum MPLS selesai.
      </p>
    </div>

    <!-- Kalkulasi Angka Refund -->
    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-2">
      <div class="flex justify-between">
        <span class="text-slate-500">Total Sudah Dibayar:</span>
        <strong id="confirm-refund-total-paid" class="text-slate-800">Rp 0</strong>
      </div>
      <div class="flex justify-between text-rose-600">
        <span>Non-refundable (Pendaftaran + Baju):</span>
        <strong id="confirm-refund-non-refundable">- Rp 0</strong>
      </div>
      <div class="border-t border-slate-200 pt-2 flex justify-between text-sm font-extrabold text-rose-900">
        <span>Total Uang Dikembalikan:</span>
        <span class="tabular-nums" id="confirm-refund-final-amount">Rp 0</span>
      </div>
    </div>

    <div>
      <label class="block text-xs font-bold text-slate-700 mb-1">
        Alasan / Nama Sekolah Negeri Tujuan <span class="text-rose-500">*</span>
      </label>
      <textarea
        id="confirm-refund-reason"
        rows="2"
        placeholder="Contoh: Diterima di SMAN 1 Mengwi Jalur Zonasi (SK Kelulusan terlampir)..."
        class="w-full text-xs rounded-xl border border-slate-300 p-2.5 text-slate-800 focus:outline-none focus:border-rose-600 focus:ring-1 focus:ring-rose-100"
      ></textarea>
    </div>

    <div class="flex items-center justify-end gap-2.5 pt-2">
      <button
        type="button"
        onclick="closeRefundConfirmationModal()"
        class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition cursor-pointer"
      >
        Batal
      </button>
      <button
        type="button"
        id="btn-submit-refund-final"
        onclick="submitRefundProcess()"
        class="px-4 py-2 rounded-xl text-xs font-extrabold text-white bg-rose-600 hover:bg-rose-700 transition cursor-pointer inline-flex items-center gap-1.5"
      >
        <span id="refund-btn-text">Ya, Proses Pengembalian Dana</span>
        <svg id="refund-spinner" class="hidden w-3.5 h-3.5 animate-spin text-white" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </button>
    </div>
  </div>
</div>

<!-- ============================================================== -->
<!-- 7. MODAL CETAK KUITANSI RESMI SPMB 2027                        -->
<!-- ============================================================== -->
<div
  id="keu-kuitansi-modal"
  class="fixed inset-0 z-[100] hidden bg-slate-900/60 items-center justify-center p-3 sm:p-4 no-print overflow-y-auto"
  onclick="if (event.target === this) closeKuitansiModal();"
>
  <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-xl w-full my-auto flex flex-col overflow-hidden">
    <!-- Header Modal -->
    <div class="px-5 py-3.5 border-b border-slate-200 bg-slate-50 flex items-center justify-between no-print">
      <div class="flex items-center gap-2 text-slate-800 font-bold text-sm">
        <svg class="w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
        <span>Pratinjau Kuitansi Pembayaran</span>
      </div>
      <div class="flex items-center gap-2">
        <button
          type="button"
          onclick="printKuitansi()"
          class="px-3.5 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs inline-flex items-center gap-1.5 transition cursor-pointer active:scale-95"
        >
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
          <span>Cetak Kuitansi</span>
        </button>
        <button
          type="button"
          onclick="closeKuitansiModal()"
          class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-200 transition cursor-pointer"
        >
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
    </div>

    <!-- Area Kuitansi Siap Cetak -->
    <div class="p-6 overflow-y-auto max-h-[80vh] bg-white" id="printable-kuitansi-area">
      <!-- Kop Surat Kuitansi -->
      <div class="border-b-2 border-slate-900 pb-4 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <img src="{{ asset('assets/img/alogo.png') }}" alt="Logo" class="w-12 h-12 object-contain shrink-0" />
          <div class="leading-tight">
            <h4 class="font-black text-sm text-slate-900 uppercase tracking-tight">SMK TI BALI GLOBAL BADUNG</h4>
            <p class="text-[10px] text-slate-600">Jl. Tibung Sari No. 1, Dalung, Kuta Utara, Badung - Bali</p>
            <p class="text-[9px] text-slate-500">Telp: (0361) 419097 &bull; Website: smktibaliglobalbadung.sch.id</p>
          </div>
        </div>
        <div class="text-right leading-tight">
          <span class="inline-block px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-blue-100 text-blue-900 border border-blue-200">
            KUITANSI RESMI
          </span>
          <div class="font-mono font-bold text-xs text-slate-900 mt-1" id="kwt-number">KWT-2027-XXXX</div>
          <div class="text-[10px] text-slate-500" id="kwt-date">-</div>
        </div>
      </div>

      <!-- Detail Pembayar & Nominal -->
      <div class="py-4 space-y-2.5 text-xs text-slate-800">
        <div class="grid grid-cols-3 gap-1">
          <span class="text-slate-500">Telah Diterima Dari</span>
          <span class="col-span-2 font-bold text-slate-900" id="kwt-student-name">: -</span>
        </div>
        <div class="grid grid-cols-3 gap-1">
          <span class="text-slate-500">Nomor Registrasi / Jurusan</span>
          <span class="col-span-2 font-mono font-semibold" id="kwt-student-reg">: -</span>
        </div>
        <div class="grid grid-cols-3 gap-1">
          <span class="text-slate-500">Metode Pembayaran</span>
          <span class="col-span-2 font-medium" id="kwt-payment-method">: -</span>
        </div>
        <div class="grid grid-cols-3 gap-1 bg-slate-50 p-2 rounded-lg border border-slate-200">
          <span class="text-slate-600 font-bold self-center">Uang Sejumlah</span>
          <div class="col-span-2">
            <span class="text-base font-black text-blue-900 block" id="kwt-total-amount">Rp 0</span>
            <span class="text-[10.5px] italic text-slate-600 block mt-0.5" id="kwt-terbilang">"Nol Rupiah"</span>
          </div>
        </div>
      </div>

      <!-- Rincian Kategori yang Dibayarkan -->
      <div class="py-2 space-y-1.5">
        <span class="text-[10.5px] font-bold text-slate-700 uppercase tracking-wider block">
          Alokasi Rincian Pembayaran:
        </span>
        <table class="w-full text-left text-[11px] border-collapse border border-slate-200">
          <thead class="bg-slate-100 text-slate-700 font-bold">
            <tr>
              <th class="py-1.5 px-2.5 border border-slate-200">No</th>
              <th class="py-1.5 px-2.5 border border-slate-200">Pos Biaya (Kategori)</th>
              <th class="py-1.5 px-2.5 border border-slate-200 text-right">Nominal</th>
            </tr>
          </thead>
          <tbody id="kwt-items-tbody">
            <!-- Dynamically populated -->
          </tbody>
          <tfoot class="bg-slate-50 font-black text-slate-900">
            <tr>
              <td colspan="2" class="py-1.5 px-2.5 border border-slate-200 text-right uppercase">Total Pembayaran</td>
              <td class="py-1.5 px-2.5 border border-slate-200 text-right tabular-nums text-blue-900 font-bold" id="kwt-tfoot-total">Rp 0</td>
            </tr>
          </tfoot>
        </table>
        <p class="text-[10px] text-slate-400 italic pt-1" id="kwt-notes">Catatan: -</p>
      </div>

      <!-- Tanda Tangan Penyerah & Penerima -->
      <div class="pt-6 grid grid-cols-2 text-center text-xs text-slate-800">
        <div>
          <span class="text-slate-500 block mb-12">Penyetor / Orang Tua Siswa,</span>
          <span class="font-bold border-b border-slate-800 pb-0.5 px-4" id="kwt-sign-parent">( ........................................ )</span>
        </div>
        <div>
          <span class="text-slate-500 block mb-12">Badung, <span id="kwt-sign-date">-</span><br>Panitia / Bendahara SPMB,</span>
          <span class="font-bold border-b border-slate-800 pb-0.5 px-4" id="kwt-officer-name">Admin SPMB 2027</span>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 8. MODAL RESET / VOID SELURUH PEMBAYARAN SISWA -->
<div id="keu-reset-payment-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 items-center justify-center p-4">
  <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-200">
    <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-amber-50/50">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
        </div>
        <div>
          <h3 class="font-bold text-slate-900 text-base">Reset / Void Pembayaran</h3>
          <p class="text-xs text-slate-500">Kembalikan status siswa ke Rp 0</p>
        </div>
      </div>
      <button type="button" onclick="closeResetPaymentModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg cursor-pointer">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <form id="form-reset-payment" onsubmit="submitResetPayment(event)" class="p-5 space-y-4">
      <input type="hidden" id="reset-payment-reg" name="no_registrasi" />
      <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs text-slate-700">
        <div class="flex justify-between py-0.5">
          <span class="text-slate-500">Calon Siswa:</span>
          <strong id="reset-payment-name" class="text-slate-900">-</strong>
        </div>
        <div class="flex justify-between py-0.5">
          <span class="text-slate-500">Nomor Registrasi:</span>
          <span id="reset-payment-reg-label" class="font-mono font-bold text-blue-700">-</span>
        </div>
        <div class="flex justify-between py-0.5">
          <span class="text-slate-500">Total Telah Masuk:</span>
          <span id="reset-payment-amount" class="font-black text-rose-600">Rp 0</span>
        </div>
      </div>
      <div>
        <label for="reset-payment-alasan" class="block text-xs font-bold text-slate-700 mb-1">
          Alasan Pembatalan / Reset <span class="text-rose-600">* (Wajib & Tercatat di Log Audit)</span>
        </label>
        <textarea
          id="reset-payment-alasan"
          name="alasan"
          rows="3"
          required
          minlength="5"
          placeholder="Contoh: Salah input nominal oleh admin kasir, pembayaran dialihkan, dll"
          class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition"
        ></textarea>
      </div>
      <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
        <button type="button" onclick="closeResetPaymentModal()" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-50 transition cursor-pointer">
          Batal
        </button>
        <button type="submit" id="btn-submit-reset-payment" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs inline-flex items-center gap-1.5 transition cursor-pointer">
          <span>Ya, Reset Pembayaran</span>
        </button>
      </div>
    </form>
  </div>
</div>

<!-- 9. MODAL HAPUS SISWA DUMMY & TAGIHAN KEUANGAN -->
<div id="keu-delete-student-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 items-center justify-center p-4">
  <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-200">
    <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-rose-50/50">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="m19 6-1 14H6L5 6"/><path d="M10 11v5"/><path d="M14 11v5"/></svg>
        </div>
        <div>
          <h3 class="font-bold text-slate-900 text-base">Hapus Siswa & Tagihan</h3>
          <p class="text-xs text-rose-600 font-medium">Khusus Data Siswa Dummy / Uji Coba</p>
        </div>
      </div>
      <button type="button" onclick="closeDeleteStudentModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg cursor-pointer">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <form id="form-delete-student" onsubmit="submitDeleteStudent(event)" class="p-5 space-y-4">
      <input type="hidden" id="delete-student-reg" name="no_registrasi" />
      <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-900 leading-relaxed">
        Peringatan: Seluruh data pendaftaran, tagihan, dan riwayat keuangan atas nama <strong id="delete-student-name">-</strong> (<span id="delete-student-reg-label" class="font-mono font-bold">-</span>) akan dihapus secara permanen dari database.
      </div>
      <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
        <button type="button" onclick="closeDeleteStudentModal()" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-50 transition cursor-pointer">
          Batal
        </button>
        <button type="submit" id="btn-submit-delete-student" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs inline-flex items-center gap-1.5 transition cursor-pointer">
          <span>Hapus Permanen</span>
        </button>
      </div>
    </form>
  </div>
</div>

<!-- 10. MODAL VOID / BATALKAN KUITANSI TERTENTU -->
<div id="keu-void-kuitansi-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 items-center justify-center p-4">
  <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-200">
    <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-rose-50/50">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        </div>
        <div>
          <h3 class="font-bold text-slate-900 text-base">Batalkan (Void) Kuitansi</h3>
          <p class="text-xs text-slate-500">Membatalkan 1 transaksi pembayaran</p>
        </div>
      </div>
      <button type="button" onclick="closeVoidKuitansiModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg cursor-pointer">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <form id="form-void-kuitansi" onsubmit="submitVoidKuitansi(event)" class="p-5 space-y-4">
      <input type="hidden" id="void-kuitansi-number" name="no_kuitansi" />
      <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs text-slate-700">
        <div class="flex justify-between py-0.5">
          <span class="text-slate-500">Nomor Kuitansi:</span>
          <span id="void-kuitansi-label" class="font-mono font-bold text-rose-700">-</span>
        </div>
        <div class="flex justify-between py-0.5">
          <span class="text-slate-500">Nominal Pembayaran:</span>
          <span id="void-kuitansi-amount" class="font-black text-slate-900">Rp 0</span>
        </div>
      </div>
      <div>
        <label for="void-kuitansi-alasan" class="block text-xs font-bold text-slate-700 mb-1">
          Alasan Pembatalan Kuitansi <span class="text-rose-600">* (Wajib & Tercatat di Log Audit)</span>
        </label>
        <textarea
          id="void-kuitansi-alasan"
          name="alasan"
          rows="3"
          required
          minlength="5"
          placeholder="Tuliskan alasan pembatalan transaksi kuitansi ini..."
          class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:border-rose-500 focus:ring-2 focus:ring-rose-100 transition"
        ></textarea>
      </div>
      <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
        <button type="button" onclick="closeVoidKuitansiModal()" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-50 transition cursor-pointer">
          Batal
        </button>
        <button type="submit" id="btn-submit-void-kuitansi" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs inline-flex items-center gap-1.5 transition cursor-pointer">
          <span>Batalkan Kuitansi Ini</span>
        </button>
      </div>
    </form>
  </div>
</div>

<!-- 11. MODAL BULK RESET TAGIHAN -->
<div id="keu-bulk-reset-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 items-center justify-center p-4">
  <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-200">
    <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-amber-50/50">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
        </div>
        <div>
          <h3 class="font-bold text-slate-900 text-base">Reset Tagihan Terpilih</h3>
          <p class="text-xs text-slate-500">Mereset tagihan sejumlah siswa ke Rp 0</p>
        </div>
      </div>
      <button type="button" onclick="closeBulkResetModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg cursor-pointer">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <form id="form-bulk-reset" onsubmit="submitBulkReset(event)" class="p-5 space-y-4">
      <p class="text-xs text-slate-600">
        Anda akan mereset pembayaran untuk <strong id="bulk-reset-count" class="text-blue-700 font-bold">0</strong> siswa terpilih. Status pembayaran akan kembali ke Rp 0.
      </p>
      <div>
        <label for="bulk-reset-alasan" class="block text-xs font-bold text-slate-700 mb-1">
          Alasan Pembatalan / Reset <span class="text-rose-600">* (Wajib & Tercatat di Log Audit)</span>
        </label>
        <textarea
          id="bulk-reset-alasan"
          name="alasan"
          rows="3"
          required
          minlength="5"
          placeholder="Tuliskan alasan pembatalan masal ini..."
          class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition"
        ></textarea>
      </div>
      <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
        <button type="button" onclick="closeBulkResetModal()" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-50 transition cursor-pointer">
          Batal
        </button>
        <button type="submit" id="btn-submit-bulk-reset" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs inline-flex items-center gap-1.5 transition cursor-pointer">
          <span>Ya, Reset Semua Terpilih</span>
        </button>
      </div>
    </form>
  </div>
</div>

<!-- 12. MODAL BULK HAPUS SISWA -->
<div id="keu-bulk-delete-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 items-center justify-center p-4">
  <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-200">
    <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-rose-50/50">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="m19 6-1 14H6L5 6"/><path d="M10 11v5"/><path d="M14 11v5"/></svg>
        </div>
        <div>
          <h3 class="font-bold text-slate-900 text-base">Hapus Siswa Terpilih</h3>
          <p class="text-xs text-rose-600 font-medium">Khusus Data Dummy / Uji Coba</p>
        </div>
      </div>
      <button type="button" onclick="closeBulkDeleteModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg cursor-pointer">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <form id="form-bulk-delete" onsubmit="submitBulkDelete(event)" class="p-5 space-y-4">
      <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-900 leading-relaxed">
        Peringatan: Anda akan menghapus <strong id="bulk-delete-count" class="font-bold">0</strong> siswa terpilih beserta seluruh data pendaftaran, tagihan, dan riwayat keuangannya secara permanen dari database.
      </div>
      <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
        <button type="button" onclick="closeBulkDeleteModal()" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-50 transition cursor-pointer">
          Batal
        </button>
        <button type="submit" id="btn-submit-bulk-delete" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs inline-flex items-center gap-1.5 transition cursor-pointer">
          <span>Ya, Hapus Terpilih</span>
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ============================================================== -->
<!-- JAVASCRIPT LOGIC & ENGINE KEUANGAN 7 POS                       -->
<!-- ============================================================== -->
<script id="admin-billing-cache" type="application/json">
  {!! json_encode($pendaftaranBilling ?? []) !!}
</script>
<script>
  // State Keuangan
  window.keuanganState = {
    currentStudentReg: null,
    currentStudentData: null,
    currentBillingData: null,
    allBillingCache: (() => {
      try {
        const raw = document.getElementById('admin-billing-cache')?.textContent;
        return raw ? JSON.parse(raw) : [];
      } catch (e) {
        return [];
      }
    })(),
  };

  // Filter, Pagination & Urutan State
  const currentKeuFilters = {
    status: '',
    baju: '',
    jurusan: '',
    gelombang: '',
    jalur: ''
  };
  let currentKeuSortOrder = 'desc';
  let currentKeuPage = 1;
  const keuPerPage = 10;

  function changeKeuPage(page) {
    currentKeuPage = page;
    renderKeuPagination();
  }

  function getFilteredKeuanganRows(rows = Array.from(document.querySelectorAll('.keu-data-row'))) {
    const q = (document.getElementById('keu-search-input')?.value || '').toLowerCase().trim();
    const jurVal = currentKeuFilters.jurusan;
    const gelVal = currentKeuFilters.gelombang;
    const jalVal = currentKeuFilters.jalur;
    const statusVal = currentKeuFilters.status;
    const bajuVal = currentKeuFilters.baju;

    return rows.filter(row => {
      const name = (row.dataset.name || '').toLowerCase();
      const reg = (row.dataset.registration || '').toLowerCase();
      const school = (row.dataset.school || '').toLowerCase();
      const jur = String(row.dataset.jurusan || '');
      const gel = String(row.dataset.gelombang || '');
      const jal = String(row.dataset.jalur || '');
      const status = row.dataset.status || '';
      const baju = row.dataset.baju || '';

      const matchQ = !q || name.includes(q) || reg.includes(q) || school.includes(q);
      const matchJur = !jurVal || jur === String(jurVal);
      const matchGel = !gelVal || gel === String(gelVal);
      const matchJal = !jalVal || jal === String(jalVal);
      const matchStatus = !statusVal || status === statusVal;
      const matchBaju = !bajuVal || baju === bajuVal;

      return matchQ && matchJur && matchGel && matchJal && matchStatus && matchBaju;
    });
  }

  function renderKeuPagination() {
    const rows = Array.from(document.querySelectorAll('.keu-data-row'));
    const emptyNotice = document.getElementById('keu-empty-filter-state');
    const filteredRows = getFilteredKeuanganRows(rows);
    const totalFiltered = filteredRows.length;
    const totalPages = Math.max(1, Math.ceil(totalFiltered / keuPerPage));

    if (currentKeuPage > totalPages) {
      currentKeuPage = totalPages;
    }
    if (currentKeuPage < 1) {
      currentKeuPage = 1;
    }

    const startIndex = (currentKeuPage - 1) * keuPerPage;
    const endIndex = Math.min(startIndex + keuPerPage, totalFiltered);

    // Sembunyikan semua baris
    rows.forEach(row => {
      row.style.display = 'none';
      row.classList.add('hidden');
    });

    // Tampilkan hanya baris di halaman aktif
    filteredRows.forEach((row, idx) => {
      if (idx >= startIndex && idx < endIndex) {
        row.style.display = '';
        row.classList.remove('hidden');
      }
    });

    if (emptyNotice) {
      emptyNotice.classList.toggle('hidden', totalFiltered > 0);
    }

    // Perbarui counter footer "Menampilkan X pendaftar"
    const visibleCount = totalFiltered > 0 ? (endIndex - startIndex) : 0;
    const visibleCountEl = document.getElementById('keu-visible-count');
    if (visibleCountEl) {
      visibleCountEl.textContent = visibleCount;
    }

    // Perbarui indikator halaman "1 / X"
    const pageIndicator = document.getElementById('keu-page-indicator');
    if (pageIndicator) {
      pageIndicator.textContent = currentKeuPage + ' / ' + totalPages;
    }

    // Perbarui tombol prev/next
    const prevBtn = document.getElementById('keu-page-prev');
    const nextBtn = document.getElementById('keu-page-next');

    if (prevBtn) {
      const canPrev = currentKeuPage > 1;
      prevBtn.disabled = !canPrev;
      prevBtn.classList.toggle('cursor-not-allowed', !canPrev);
      prevBtn.classList.toggle('bg-slate-50', !canPrev);
      prevBtn.classList.toggle('text-slate-300', !canPrev);
      prevBtn.classList.toggle('cursor-pointer', canPrev);
      prevBtn.classList.toggle('bg-white', canPrev);
      prevBtn.classList.toggle('text-slate-600', canPrev);
      prevBtn.classList.toggle('hover:bg-blue-50', canPrev);
      prevBtn.classList.toggle('hover:text-blue-600', canPrev);
      prevBtn.classList.toggle('hover:border-blue-200', canPrev);
      prevBtn.classList.toggle('active:scale-95', canPrev);
    }

    if (nextBtn) {
      const canNext = currentKeuPage < totalPages;
      nextBtn.disabled = !canNext;
      nextBtn.classList.toggle('cursor-not-allowed', !canNext);
      nextBtn.classList.toggle('bg-slate-50', !canNext);
      nextBtn.classList.toggle('text-slate-300', !canNext);
      nextBtn.classList.toggle('cursor-pointer', canNext);
      nextBtn.classList.toggle('bg-white', canNext);
      nextBtn.classList.toggle('text-slate-600', canNext);
      nextBtn.classList.toggle('hover:bg-blue-50', canNext);
      nextBtn.classList.toggle('hover:text-blue-600', canNext);
      nextBtn.classList.toggle('hover:border-blue-200', canNext);
      nextBtn.classList.toggle('active:scale-95', canNext);
    }

    updateKeuSelectAllState();
    updateKeuSelectedBadge();
    updateKeuRowNumbers();
  }

  /**
   * Filter Tabel Keuangan
   */
  function applyKeuanganFilter() {
    currentKeuPage = 1;
    renderKeuPagination();
  }

  /**
   * Pilih Kategori Filter Keuangan
   */
  function selectKeuFilter(category, value, label, event) {
    if (event) {
      event.preventDefault();
      event.stopPropagation();
    }

    currentKeuFilters[category] = value;

    const categoryItem = document.querySelector(`.keu-filter-category[data-category="${category}"]`);
    if (categoryItem) {
      categoryItem.querySelectorAll('.keu-filter-option').forEach(option => {
        const isSelected = String(option.dataset.filterValue) === String(value);
        const check = option.querySelector('.keu-filter-check');
        option.classList.toggle('font-bold', isSelected);
        option.classList.toggle('text-blue-700', isSelected);
        option.classList.toggle('bg-blue-50/70', isSelected);
        option.classList.toggle('font-medium', !isSelected);
        option.classList.toggle('text-slate-700', !isSelected);
        option.classList.toggle('hover:bg-slate-100', !isSelected);
        if (check) {
          check.classList.toggle('hidden', !isSelected);
        }
      });

      const activeDot = categoryItem.querySelector('.keu-filter-active-dot');
      const activeLabel = categoryItem.querySelector('.keu-filter-active-label');
      if (activeDot) {
        activeDot.classList.toggle('hidden', value === '');
      }
      if (activeLabel) {
        activeLabel.textContent = value ? label : '';
        activeLabel.classList.toggle('hidden', value === '');
      }
    }

    const activeFilterCount = Object.values(currentKeuFilters).filter(Boolean).length;
    const filterBadge = document.getElementById('keu-filter-badge');
    const filterTrigger = document.getElementById('keu-filter-trigger');
    if (filterBadge) {
      filterBadge.textContent = activeFilterCount;
      filterBadge.classList.toggle('hidden', activeFilterCount === 0);
    }
    if (filterTrigger) {
      filterTrigger.classList.toggle('border-blue-300', activeFilterCount > 0);
      filterTrigger.classList.toggle('text-blue-700', activeFilterCount > 0);
      filterTrigger.classList.toggle('bg-blue-50/40', activeFilterCount > 0);
    }
    const resetButton = document.getElementById('keu-filter-reset-btn');
    const resetDivider = document.getElementById('keu-filter-reset-divider');
    if (resetButton) {
      resetButton.classList.toggle('hidden', activeFilterCount === 0);
    }
    if (resetDivider) {
      resetDivider.classList.toggle('hidden', activeFilterCount === 0);
    }

    applyKeuanganFilter();
  }

  /**
   * Reset Semua Filter Keuangan
   */
  function resetKeuFilters(event) {
    if (event) {
      event.preventDefault();
      event.stopPropagation();
    }

    Object.keys(currentKeuFilters).forEach(cat => {
      selectKeuFilter(cat, '', 'Semua');
    });
  }

  /**
   * Urutkan Data Siswa Berdasarkan No. Registrasi
   */
  function setKeuSortOrder(order) {
    currentKeuSortOrder = order;
    const newestButton = document.getElementById('keu-sort-newest');
    const oldestButton = document.getElementById('keu-sort-oldest');

    [newestButton, oldestButton].forEach(button => {
      if (!button) return;
      const isActive = (order === 'desc' && button === newestButton) || (order === 'asc' && button === oldestButton);
      button.setAttribute('aria-pressed', String(isActive));
      button.classList.toggle('bg-white', isActive);
      button.classList.toggle('text-blue-600', isActive);
      button.classList.toggle('font-bold', isActive);
      button.classList.toggle('text-slate-400', !isActive);
      button.classList.toggle('hover:text-slate-700', !isActive);
      button.classList.toggle('hover:bg-white/60', !isActive);
    });

    const tbody = document.getElementById('keu-table-body');
    if (!tbody) return;
    const rows = Array.from(tbody.querySelectorAll('.keu-data-row'));
    rows.sort((a, b) => {
      const regA = a.dataset.registration || '';
      const regB = b.dataset.registration || '';
      const comp = regA.localeCompare(regB, 'id-ID', { numeric: true });
      return currentKeuSortOrder === 'desc' ? -comp : comp;
    });
    rows.forEach(r => tbody.appendChild(r));
    renderKeuPagination();
  }

  /**
   * Perbarui Nomor Urut Baris yang Terlihat
   */
  function updateKeuRowNumbers() {
    const rows = Array.from(document.querySelectorAll('.keu-data-row'));
    const filteredRows = getFilteredKeuanganRows(rows);
    filteredRows.forEach((r, idx) => {
      const cell = r.querySelector('.keu-row-no');
      if (cell) {
        cell.textContent = idx + 1;
      }
    });
  }

  /**
   * Perbarui Badge Siswa Terpilih
   */
  function updateKeuSelectedBadge() {
    const tableBody = document.getElementById('keu-table-body');
    const badge = document.getElementById('keu-table-selected-badge');
    const bulkBar = document.getElementById('keu-bulk-action-bar');
    const bulkCount = document.getElementById('keu-bulk-selected-count');
    if (!tableBody || !badge) return;

    const checkboxes = Array.from(tableBody.querySelectorAll('[data-keu-select]'));
    const selectedCount = checkboxes.filter(cb => {
      const row = cb.closest('tr');
      return cb.checked && row && !row.classList.contains('hidden') && !row.hidden && row.style.display !== 'none';
    }).length;
    badge.textContent = selectedCount;

    if (bulkBar) {
      if (selectedCount > 0) {
        bulkBar.classList.remove('hidden');
        bulkBar.classList.add('flex');
        if (bulkCount) bulkCount.textContent = selectedCount;
      } else {
        bulkBar.classList.add('hidden');
        bulkBar.classList.remove('flex');
      }
    }
  }

  function clearKeuSelections() {
    const tableBody = document.getElementById('keu-table-body');
    if (!tableBody) return;
    tableBody.querySelectorAll('[data-keu-select]').forEach(cb => cb.checked = false);
    const selectAll = document.getElementById('keu-select-all');
    if (selectAll) {
      selectAll.checked = false;
      selectAll.indeterminate = false;
    }
    updateKeuSelectedBadge();
  }

  /**
   * Perbarui Status Checkbox "Pilih Semua"
   */
  function updateKeuSelectAllState() {
    const tableBody = document.getElementById('keu-table-body');
    const selectAll = document.getElementById('keu-select-all');
    if (!tableBody || !selectAll) return;

    const checkboxes = Array.from(tableBody.querySelectorAll('[data-keu-select]'));
    const visibleCheckboxes = checkboxes.filter(cb => {
      const row = cb.closest('tr');
      return row && !row.classList.contains('hidden') && !row.hidden && row.style.display !== 'none';
    });
    const selectedVisibleCount = visibleCheckboxes.filter(cb => cb.checked).length;

    if (visibleCheckboxes.length === 0) {
      selectAll.checked = false;
      selectAll.indeterminate = false;
    } else if (selectedVisibleCount === visibleCheckboxes.length) {
      selectAll.checked = true;
      selectAll.indeterminate = false;
    } else if (selectedVisibleCount > 0) {
      selectAll.checked = false;
      selectAll.indeterminate = true;
    } else {
      selectAll.checked = false;
      selectAll.indeterminate = false;
    }
  }

  /**
   * Toggle Pilih Semua Checkbox
   */
  function toggleKeuSelectAll(checked) {
    const tableBody = document.getElementById('keu-table-body');
    if (!tableBody) return;
    const checkboxes = Array.from(tableBody.querySelectorAll('[data-keu-select]'));
    checkboxes.forEach(cb => {
      const row = cb.closest('tr');
      if (row && !row.classList.contains('hidden') && !row.hidden && row.style.display !== 'none') {
        cb.checked = checked;
      }
    });
    updateKeuSelectedBadge();
    updateKeuSelectAllState();
  }

  /**
   * Inisialisasi Dropdown Filter Interaktif
   */
  (function initKeuFilterDropdown() {
    const container = document.getElementById('keu-filter-container');
    const trigger = document.getElementById('keu-filter-trigger');
    const menu = document.getElementById('keu-filter-menu');
    if (!container || !trigger || !menu) {
      return;
    }

    let closeTimer = null;

    function closeDropdown() {
      clearTimeout(closeTimer);
      menu.classList.add('hidden');
      trigger.setAttribute('aria-expanded', 'false');
      container.querySelectorAll('.keu-filter-category').forEach(category => {
        category.classList.remove('flyout-active');
        const categoryTrigger = category.querySelector('.keu-filter-category-trigger');
        if (categoryTrigger) {
          categoryTrigger.setAttribute('aria-expanded', 'false');
        }
      });
    }

    function openDropdown() {
      clearTimeout(closeTimer);
      menu.classList.remove('hidden');
      trigger.setAttribute('aria-expanded', 'true');
    }

    function scheduleCloseDropdown() {
      clearTimeout(closeTimer);
      closeTimer = setTimeout(() => {
        closeDropdown();
      }, 180);
    }

    trigger.addEventListener('mouseenter', openDropdown);
    trigger.addEventListener('pointerenter', openDropdown);
    container.addEventListener('mouseenter', openDropdown);
    container.addEventListener('pointerenter', openDropdown);
    container.addEventListener('mouseleave', scheduleCloseDropdown);
    menu.addEventListener('mouseenter', openDropdown);
    menu.addEventListener('pointerenter', openDropdown);

    trigger.addEventListener('click', event => {
      event.stopPropagation();
      if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
        openDropdown();
        return;
      }

      const isOpen = !menu.classList.contains('hidden');
      menu.classList.toggle('hidden', isOpen);
      trigger.setAttribute('aria-expanded', String(!isOpen));
    });

    container.querySelectorAll('.keu-filter-category-trigger').forEach(categoryTrigger => {
      categoryTrigger.addEventListener('click', event => {
        event.stopPropagation();
        const category = categoryTrigger.closest('.keu-filter-category');
        const supportsHover = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
        const isOpen = category.classList.contains('flyout-active');
        container.querySelectorAll('.keu-filter-category').forEach(item => {
          item.classList.remove('flyout-active');
          const itemTrigger = item.querySelector('.keu-filter-category-trigger');
          if (itemTrigger) {
            itemTrigger.setAttribute('aria-expanded', 'false');
          }
        });
        const shouldOpen = supportsHover || !isOpen;
        category.classList.toggle('flyout-active', shouldOpen);
        categoryTrigger.setAttribute('aria-expanded', String(shouldOpen));
      });
    });

    container.querySelectorAll('.keu-filter-category').forEach(category => {
      category.addEventListener('mouseenter', () => {
        container.querySelectorAll('.keu-filter-category').forEach(item => {
          item.classList.toggle('flyout-active', item === category);
          const itemTrigger = item.querySelector('.keu-filter-category-trigger');
          if (itemTrigger) {
            itemTrigger.setAttribute('aria-expanded', String(item === category));
          }
        });
      });
      category.addEventListener('mouseleave', () => {
        category.classList.remove('flyout-active');
        const categoryTrigger = category.querySelector('.keu-filter-category-trigger');
        if (categoryTrigger) {
          categoryTrigger.setAttribute('aria-expanded', 'false');
        }
      });
    });

    document.addEventListener('click', event => {
      if (!container.contains(event.target)) {
        closeDropdown();
      }
    });
    document.addEventListener('keydown', event => {
      if (event.key === 'Escape') {
        closeDropdown();
      }
    });
  })();

  /**
   * Listener Checkbox dan Seleksi
   */
  (function initKeuEventListeners() {
    const selectAll = document.getElementById('keu-select-all');
    if (selectAll) {
      selectAll.addEventListener('change', function() {
        toggleKeuSelectAll(this.checked);
      });
    }

    const tableBody = document.getElementById('keu-table-body');
    if (tableBody) {
      tableBody.addEventListener('change', function(e) {
        if (e.target && e.target.hasAttribute('data-keu-select')) {
          updateKeuSelectAllState();
          updateKeuSelectedBadge();
        }
      });
    }

    renderKeuPagination();
  })();

  window.initKeuanganView = function() {
    renderKeuPagination();
  };

  /**
   * Buka Modal Catat Pembayaran
   */
  async function openPaymentModal(noRegistrasi) {
    window.keuanganState.currentStudentReg = noRegistrasi;
    const modal = document.getElementById('keu-payment-modal');
    if (!modal) return;

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    // Fetch data terbaru siswa
    try {
      const response = await fetch(`/admin/keuangan/detail/${noRegistrasi}`);
      const data = await response.json();
      if (!data.success) {
        alert(data.message || 'Gagal memuat rincian siswa');
        return;
      }

      window.keuanganState.currentStudentData = data.student;
      window.keuanganState.currentBillingData = data.billing;

      // Update Header Siswa
      document.getElementById('keu-modal-student-name').textContent = data.student.nama;
      document.getElementById('keu-modal-student-meta').textContent =
        `No. Reg: ${data.student.no_registrasi} • ${data.student.jurusan} • ${data.student.gelombang} (${data.student.jalur})`;

      // Render 7 Pos Input Rows
      renderPaymentCategoryRows(data.billing);

      // Reset accordion
      const panel = document.getElementById('spp-custom-panel');
      if (panel) panel.classList.add('hidden');
      const inputCustomSpp = document.getElementById('custom-spp-percent-input');
      if (inputCustomSpp) {
        inputCustomSpp.value = data.billing.categories.spp.diskon_persen || '';
      }

    } catch (err) {
      console.error(err);
      alert('Terjadi kesalahan saat memuat data');
    }
  }

  function closePaymentModal() {
    const modal = document.getElementById('keu-payment-modal');
    if (modal) {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }
  }

  /**
   * Render Baris Input Alokasi 7 Kategori
   */
  function renderPaymentCategoryRows(billing) {
    const container = document.getElementById('keu-allocation-rows-container');
    if (!container) return;

    const cats = billing.categories;
    const isAllLunas = Number(billing.total_sisa || 0) <= 0;
    let html = '';

    const lunasBanner = document.getElementById('keu-modal-all-lunas-banner');
    const formFields = document.getElementById('keu-payment-form-fields');
    const submitBtn = document.getElementById('keu-modal-submit-btn');
    const footerTotalContainer = document.getElementById('keu-modal-footer-total-container');
    const closeBtn = document.getElementById('keu-modal-close-btn');
    const sppAccordionWrapper = document.getElementById('keu-spp-custom-wrapper');

    if (isAllLunas) {
      if (lunasBanner) {
        lunasBanner.classList.remove('hidden');
        lunasBanner.classList.add('flex');
      }
      if (formFields) formFields.classList.add('hidden');
      if (submitBtn) submitBtn.classList.add('hidden');
      if (footerTotalContainer) footerTotalContainer.classList.add('hidden');
      if (closeBtn) closeBtn.textContent = 'Tutup';
      if (sppAccordionWrapper) sppAccordionWrapper.classList.add('hidden');
    } else {
      if (lunasBanner) {
        lunasBanner.classList.add('hidden');
        lunasBanner.classList.remove('flex');
      }
      if (formFields) formFields.classList.remove('hidden');
      if (submitBtn) submitBtn.classList.remove('hidden');
      if (footerTotalContainer) footerTotalContainer.classList.remove('hidden');
      if (closeBtn) closeBtn.textContent = 'Batal';
      if (sppAccordionWrapper) sppAccordionWrapper.classList.remove('hidden');
    }

    Object.keys(cats).forEach(key => {
      const c = cats[key];
      const isLunas = c.is_lunas;

      html += `
        <div class="p-3 sm:p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 ${isLunas ? 'bg-slate-50/70' : 'bg-white'}" data-category-row="${key}">
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
              <span class="font-extrabold text-slate-800 text-xs">${c.nama}</span>
              ${c.diskon_persen > 0 ? `<span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Diskon ${c.diskon_persen}%</span>` : ''}
              ${c.is_refundable ? '<span class="px-1.5 py-0.5 rounded text-[9.5px] font-semibold bg-blue-50 text-blue-700">Refundable</span>' : '<span class="px-1.5 py-0.5 rounded text-[9.5px] font-semibold bg-rose-50 text-rose-700">Non-Refundable</span>'}
            </div>
            <div class="flex items-center gap-2 text-[11px] text-slate-500 mt-1">
              <span>Bersih: <strong class="text-slate-800 font-bold">${formatRupiah(c.tagihan_bersih)}</strong></span>
              <span>•</span>
              <span>Sudah: <strong class="text-emerald-600 font-bold">${formatRupiah(c.sudah_dibayar)}</strong></span>
              <span>•</span>
              <span>Sisa: <strong class="${c.sisa > 0 ? 'text-amber-600' : 'text-slate-400'} font-bold">${formatRupiah(c.sisa)}</strong></span>
            </div>
          </div>

          <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
            ${isLunas ? `
              <span class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold inline-flex items-center gap-1">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Lunas
              </span>
            ` : `
              <button
                type="button"
                data-btn-penuh="${key}"
                data-max-val="${c.sisa}"
                onclick="fillRemainingForCategory('${key}', ${c.sisa})"
                class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-blue-50 hover:text-blue-700 border border-slate-200 text-slate-700 font-bold text-[11px] transition cursor-pointer select-none active:scale-95"
                title="Isi sisa tagihan pos ini (${formatRupiah(c.sisa)})"
              >
                Penuh
              </button>
              <div class="relative w-36">
                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 font-semibold text-xs pointer-events-none">Rp</span>
                <input
                  type="number"
                  min="0"
                  max="${c.sisa}"
                  step="any"
                  placeholder="0"
                  data-cat-input="${key}"
                  data-cat-max="${c.sisa}"
                  oninput="handleCategoryInput(this)"
                  onkeyup="handleCategoryInput(this)"
                  onchange="handleCategoryInput(this)"
                  class="w-full h-8 pl-8 pr-2 text-right rounded-lg border border-slate-300 font-bold text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-100 tabular-nums transition"
                />
              </div>
            `}
          </div>
        </div>
      `;
    });

    container.innerHTML = html;
    calculateModalInputTotal();
  }

  function handleCategoryInput(input) {
    if (!input) return;
    const maxVal = parseFloat(input.dataset.catMax || 0);
    let val = parseFloat(input.value || 0);
    if (val < 0) {
      val = 0;
      input.value = 0;
    }
    if (val > maxVal && maxVal > 0) {
      val = maxVal;
      input.value = maxVal;
    }

    // Update style tombol "Penuh" agar aktif/menyala saat terisi penuh
    const catKey = input.dataset.catInput;
    const pbtn = document.querySelector(`[data-btn-penuh="${catKey}"]`);
    if (pbtn) {
      const isFull = val > 0 && Math.abs(val - maxVal) < 1;
      pbtn.classList.toggle('bg-blue-600', isFull);
      pbtn.classList.toggle('text-white', isFull);
      pbtn.classList.toggle('border-blue-600', isFull);
      pbtn.classList.toggle('bg-slate-100', !isFull);
      pbtn.classList.toggle('text-slate-700', !isFull);
      pbtn.classList.toggle('border-slate-200', !isFull);
    }

    calculateModalInputTotal();
  }

  function fillRemainingForCategory(catKey, remaining) {
    const input = document.querySelector(`[data-cat-input="${catKey}"]`);
    if (!input) return;

    const remainingNum = parseFloat(remaining) || 0;
    const currentVal = parseFloat(input.value || 0);

    // Toggle: Jika sudah terisi penuh, klik lagi untuk mengosongkan
    if (currentVal === remainingNum && remainingNum > 0) {
      input.value = '';
    } else {
      input.value = remainingNum;
    }

    handleCategoryInput(input);
  }

  function calculateModalInputTotal() {
    const inputs = document.querySelectorAll('[data-cat-input]');
    let total = 0;
    let count = 0;
    const totalInputs = inputs.length;

    inputs.forEach(inp => {
      const val = parseFloat(inp.value || 0);
      if (val > 0) {
        total += val;
        count++;
      }
    });

    const formatted = formatRupiah(total);

    // Update di live total bar (atas/tengah)
    const display = document.getElementById('keu-modal-total-input');
    if (display) display.textContent = formatted;

    const countDisplay = document.getElementById('keu-modal-category-count');
    if (countDisplay) {
      countDisplay.textContent = totalInputs > 0 ? `${count} / ${totalInputs}` : count;
    }

    // Update di footer modal (selalu terlihat di dekat tombol simpan)
    const footerDisplay = document.getElementById('keu-modal-footer-total');
    if (footerDisplay) footerDisplay.textContent = formatted;

    // Update teks pada tombol simpan agar jelas nominalnya
    const submitLabel = document.getElementById('keu-modal-submit-label');
    if (submitLabel) {
      if (total > 0) {
        submitLabel.textContent = `Simpan Pembayaran (${formatted})`;
      } else {
        submitLabel.textContent = 'Simpan Pembayaran & Kuitansi';
      }
    }
  }

  function toggleCustomSppAccordion() {
    const panel = document.getElementById('spp-custom-panel');
    if (panel) panel.classList.toggle('hidden');
  }

  /**
   * Terapkan Diskon SPP Manual (Kebijakan Kepala Sekolah)
   */
  async function applyCustomSppDiscount() {
    const noReg = window.keuanganState.currentStudentReg;
    const pct = parseFloat(document.getElementById('custom-spp-percent-input')?.value || 0);
    const note = document.getElementById('custom-spp-note-input')?.value || '';

    if (isNaN(pct) || pct < 0 || pct > 100) {
      alert('Persentase diskon SPP harus antara 0 hingga 100%');
      return;
    }

    try {
      const res = await fetch('/admin/keuangan/diskon-spp', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          no_registrasi: noReg,
          diskon_spp: pct,
          catatan: note
        })
      });
      const data = await res.json();
      if (!data.success) {
        alert(data.message || 'Gagal mengubah diskon SPP');
        return;
      }

      window.keuanganState.currentBillingData = data.billing;
      renderPaymentCategoryRows(data.billing);
      alert('Diskon SPP berhasil diperbarui menjadi ' + pct + '%');

      // Update cache
      window.keuanganState.allBillingCache[noReg] = data.billing;
    } catch (e) {
      console.error(e);
      alert('Gagal menerapkan diskon SPP');
    }
  }

  /**
   * Simpan Pembayaran Siswa
   */
  async function submitPaymentRecord() {
    const noReg = window.keuanganState.currentStudentReg;
    const method = document.getElementById('keu-modal-method')?.value || 'Tunai';
    const date = document.getElementById('keu-modal-date')?.value || '';
    const notes = document.getElementById('keu-modal-notes')?.value || '';

    const allocations = {};
    const inputs = document.querySelectorAll('[data-cat-input]');
    let totalBayar = 0;

    inputs.forEach(inp => {
      const cat = inp.dataset.catInput;
      const val = parseFloat(inp.value || 0);
      if (val > 0) {
        allocations[cat] = val;
        totalBayar += val;
      }
    });

    if (totalBayar <= 0) {
      alert('Silakan masukkan nominal pembayaran untuk minimal satu kategori pos biaya.');
      return;
    }

    const btn = document.getElementById('keu-modal-submit-btn');
    const spinner = document.getElementById('keu-modal-spinner');
    const label = document.getElementById('keu-modal-submit-label');

    btn.disabled = true;
    if (spinner) spinner.classList.remove('hidden');
    if (label) label.textContent = 'Menyimpan...';

    try {
      const section = document.getElementById('section-keuangan');
      const endpoint = section?.dataset?.bayarEndpoint || '/admin/keuangan/bayar';
      const csrfToken = section?.dataset?.csrf || '{{ csrf_token() }}';

      const res = await fetch(endpoint, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          no_registrasi: noReg,
          metode_pembayaran: method,
          tgl_bayar: date,
          catatan: notes,
          alokasi: allocations
        })
      });

      const data = await res.json();
      btn.disabled = false;
      if (spinner) spinner.classList.add('hidden');
      if (label) label.textContent = 'Simpan Pembayaran & Kuitansi';

      if (!res.ok || !data.success) {
        const errorMsg = data.errors ? Object.values(data.errors).flat().join('\n') : (data.message || 'Gagal mencatat pembayaran');
        alert(errorMsg);
        return;
      }

      closePaymentModal();

      // Buka otomatis kuitansi resmi
      if (data.pembayaran && data.pembayaran.no_kuitansi) {
        openKuitansiModal(data.pembayaran.no_kuitansi);
      }

      // Reload halaman agar summary cards & tabel ter-update sinkron
      setTimeout(() => {
        window.location.reload();
      }, 1500);

    } catch (err) {
      console.error(err);
      btn.disabled = false;
      if (spinner) spinner.classList.add('hidden');
      if (label) label.textContent = 'Simpan Pembayaran & Kuitansi';
      alert('Terjadi kesalahan jaringan.');
    }
  }

  /**
   * Buka Modal Rincian & Riwayat Transaksi Siswa
   */
  async function openDetailModal(noRegistrasi) {
    window.keuanganState.currentStudentReg = noRegistrasi;
    const modal = document.getElementById('keu-detail-modal');
    if (!modal) return;

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    try {
      const res = await fetch(`/admin/keuangan/detail/${noRegistrasi}`);
      const data = await res.json();
      if (!data.success) {
        alert('Gagal memuat detail');
        return;
      }

      window.keuanganState.currentStudentData = data.student;
      window.keuanganState.currentBillingData = data.billing;

      // Header
      document.getElementById('detail-student-name').textContent = data.student.nama;
      document.getElementById('detail-student-meta').textContent =
        `No. Reg: ${data.student.no_registrasi} • NISN: ${data.student.nisn} • ${data.student.jurusan} (${data.student.jalur})`;

      // Top Stats
      document.getElementById('detail-tagihan-bersih').textContent = formatRupiah(data.billing.total_tagihan_bersih);
      document.getElementById('detail-sudah-bayar').textContent = formatRupiah(data.billing.total_sudah_dibayar);
      document.getElementById('detail-sisa-tagihan').textContent = formatRupiah(data.billing.total_sisa);
      document.getElementById('detail-status-baju').textContent = data.billing.status_baju;

      // Render 7 Categories Table
      const tbody = document.getElementById('detail-categories-table-body');
      let catHtml = '';
      Object.keys(data.billing.categories).forEach(k => {
        const c = data.billing.categories[k];
        catHtml += `
          <tr class="hover:bg-slate-50">
            <td class="py-2 px-3 font-semibold text-slate-800">${c.nama}</td>
            <td class="py-2 px-3 text-right text-slate-500 tabular-nums">${formatRupiah(c.dasar)}</td>
            <td class="py-2 px-3 text-right font-bold text-slate-900 tabular-nums">${formatRupiah(c.tagihan_bersih)}</td>
            <td class="py-2 px-3 text-right text-emerald-600 font-bold tabular-nums">${formatRupiah(c.sudah_dibayar)}</td>
            <td class="py-2 px-3 text-right text-amber-600 font-bold tabular-nums">${formatRupiah(c.sisa)}</td>
            <td class="py-2 px-3 text-center">
              ${c.is_lunas ? '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700">Lunas</span>' : '<span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700">Belum</span>'}
            </td>
          </tr>
        `;
      });
      tbody.innerHTML = catHtml;

      // Riwayat Transaksi
      const historyList = document.getElementById('detail-history-list');
      document.getElementById('detail-history-count').textContent = `${data.riwayat.length} Transaksi`;

      if (data.riwayat.length === 0) {
        historyList.innerHTML = '<p class="text-xs text-slate-400 py-3 text-center italic">Belum ada transaksi pembayaran untuk siswa ini.</p>';
      } else {
        let histHtml = '';
        data.riwayat.forEach(h => {
          const isRefund = h.tipe_transaksi === 'refund';
          histHtml += `
            <div class="p-3 rounded-xl border ${isRefund ? 'border-rose-200 bg-rose-50/40' : 'border-slate-200 bg-slate-50/70'} flex flex-col sm:flex-row sm:items-center justify-between gap-2">
              <div>
                <div class="flex items-center gap-2">
                  <span class="font-mono font-bold text-xs ${isRefund ? 'text-rose-700' : 'text-blue-700'}">${h.no_kuitansi}</span>
                  <span class="text-[10px] px-1.5 py-0.5 rounded font-semibold ${isRefund ? 'bg-rose-100 text-rose-800' : 'bg-slate-200 text-slate-700'}">${h.metode_pembayaran}</span>
                  <span class="text-[10px] text-slate-400">• ${h.tgl_bayar}</span>
                </div>
                <div class="text-[11px] text-slate-600 mt-1">${h.catatan}</div>
                <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                  ${h.items.map(it => `<span class="px-1.5 py-0.5 rounded bg-white border border-slate-200 text-[10px] text-slate-700">${it.nama_kategori}: <strong>${formatRupiah(it.nominal)}</strong></span>`).join('')}
                </div>
              </div>

              <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center shrink-0 border-t sm:border-t-0 pt-2 sm:pt-0 border-slate-200">
                <span class="text-sm font-black tabular-nums ${isRefund ? 'text-rose-600' : 'text-emerald-600'}">${formatRupiah(h.total_bayar)}</span>
                <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                  <button
                    type="button"
                    onclick="openKuitansiModal('${h.no_kuitansi}')"
                    class="px-2.5 py-1 rounded-md bg-white hover:bg-slate-100 border border-slate-300 font-bold text-[10.5px] text-slate-700 transition cursor-pointer"
                  >
                    Cetak
                  </button>
                  ${!isRefund ? `
                    <button
                      type="button"
                      onclick="triggerVoidKuitansi('${h.no_kuitansi}', '${h.total_bayar}')"
                      class="px-2.5 py-1 rounded-md bg-rose-50 hover:bg-rose-100 border border-rose-200 font-bold text-[10.5px] text-rose-700 transition cursor-pointer"
                      title="Batalkan / Void transaksi kuitansi ini"
                    >
                      Batalkan / Void
                    </button>
                  ` : ''}
                </div>
              </div>
            </div>
          `;
        });
        historyList.innerHTML = histHtml;
      }

      // Refund Calculation Box
      document.getElementById('refund-box-total-paid').textContent = formatRupiah(data.billing.total_sudah_dibayar);
      document.getElementById('refund-box-pendaftaran').textContent = formatRupiah(data.billing.categories.pendaftaran.sudah_dibayar);
      document.getElementById('refund-box-seragam').textContent = formatRupiah(data.billing.categories.seragam.sudah_dibayar);
      document.getElementById('refund-box-amount').textContent = formatRupiah(data.billing.estimasi_refund);

      const refundBtn = document.getElementById('btn-trigger-refund');
      if (refundBtn) {
        if (data.billing.keuangan.is_refunded) {
          refundBtn.disabled = true;
          refundBtn.className = 'px-3.5 py-2 rounded-xl bg-slate-200 text-slate-500 font-bold text-xs cursor-not-allowed';
          refundBtn.innerHTML = '<span>Telah Direfund (Selesai)</span>';
        } else if (data.billing.estimasi_refund <= 0) {
          refundBtn.disabled = true;
          refundBtn.className = 'px-3.5 py-2 rounded-xl bg-slate-200 text-slate-500 font-bold text-xs cursor-not-allowed';
          refundBtn.innerHTML = '<span>Tidak Ada Dana yang Dapat Dikembalikan</span>';
        } else {
          refundBtn.disabled = false;
          refundBtn.className = 'px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white font-bold text-xs inline-flex items-center gap-1.5 transition cursor-pointer active:scale-95';
          refundBtn.innerHTML = '<svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/></svg><span>Proses Refund (Diterima Negeri)</span>';
        }
      }

    } catch (e) {
      console.error(e);
      alert('Gagal mengambil data detail');
    }
  }

  function closeDetailModal() {
    const modal = document.getElementById('keu-detail-modal');
    if (modal) {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }
  }

  /**
   * Dialog Konfirmasi Refund
   */
  function openRefundConfirmationModal() {
    const b = window.keuanganState.currentBillingData;
    if (!b || b.estimasi_refund <= 0) return;

    document.getElementById('confirm-refund-total-paid').textContent = formatRupiah(b.total_sudah_dibayar);
    document.getElementById('confirm-refund-non-refundable').textContent = '- ' + formatRupiah(b.non_refundable_total);
    document.getElementById('confirm-refund-final-amount').textContent = formatRupiah(b.estimasi_refund);

    const m = document.getElementById('keu-refund-modal');
    if (m) {
      m.classList.remove('hidden');
      m.classList.add('flex');
    }
  }

  function closeRefundConfirmationModal() {
    const m = document.getElementById('keu-refund-modal');
    if (m) {
      m.classList.add('hidden');
      m.classList.remove('flex');
    }
  }

  async function submitRefundProcess() {
    const noReg = window.keuanganState.currentStudentReg;
    const reason = document.getElementById('confirm-refund-reason')?.value || '';

    if (!reason.trim()) {
      alert('Mohon cantumkan alasan atau sekolah negeri tujuan.');
      return;
    }

    const btn = document.getElementById('btn-submit-refund-final');
    const spinner = document.getElementById('refund-spinner');
    const text = document.getElementById('refund-btn-text');

    btn.disabled = true;
    if (spinner) spinner.classList.remove('hidden');
    if (text) text.textContent = 'Memproses Refund...';

    try {
      const res = await fetch('/admin/keuangan/refund', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          no_registrasi: noReg,
          alasan: reason
        })
      });

      const data = await res.json();
      btn.disabled = false;
      if (spinner) spinner.classList.add('hidden');
      if (text) text.textContent = 'Ya, Proses Pengembalian Dana';

      if (!data.success) {
        alert(data.message || 'Gagal memproses refund');
        return;
      }

      closeRefundConfirmationModal();
      closeDetailModal();

      alert('Refund dana siswa berhasil diproses sebesar ' + formatRupiah(data.refund.refund_amount));

      // Reload
      window.location.reload();
    } catch (err) {
      console.error(err);
      btn.disabled = false;
      if (spinner) spinner.classList.add('hidden');
      if (text) text.textContent = 'Ya, Proses Pengembalian Dana';
      alert('Terjadi kesalahan jaringan');
    }
  }

  /**
   * Modal Kuitansi Resmi & Cetak
   */
  async function openKuitansiModal(noKuitansi) {
    const modal = document.getElementById('keu-kuitansi-modal');
    if (!modal) return;

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    try {
      const res = await fetch(`/admin/keuangan/kuitansi/${noKuitansi}`);
      const data = await res.json();
      if (!data.success) {
        alert('Gagal memuat kuitansi');
        return;
      }

      const kwt = data.kuitansi;
      document.getElementById('kwt-number').textContent = kwt.no_kuitansi;
      document.getElementById('kwt-date').textContent = kwt.tgl_bayar;
      document.getElementById('kwt-student-name').textContent = `: ${kwt.student.nama_lengkap}`;
      document.getElementById('kwt-student-reg').textContent = `: ${kwt.student.no_registrasi} / ${kwt.student.jurusan}`;
      document.getElementById('kwt-payment-method').textContent = `: ${kwt.metode_pembayaran}`;
      document.getElementById('kwt-total-amount').textContent = kwt.total_bayar_format;
      document.getElementById('kwt-terbilang').textContent = `"${kwt.terbilang}"`;
      document.getElementById('kwt-tfoot-total').textContent = kwt.total_bayar_format;
      document.getElementById('kwt-notes').textContent = kwt.catatan ? `Catatan: ${kwt.catatan}` : 'Catatan: Pembayaran SPMB 2027';
      document.getElementById('kwt-sign-date').textContent = kwt.tgl_bayar;
      document.getElementById('kwt-officer-name').textContent = kwt.nama_petugas;

      // Populate Table Items
      const tbody = document.getElementById('kwt-items-tbody');
      let tbodyHtml = '';
      kwt.items.forEach((it, idx) => {
        tbodyHtml += `
          <tr>
            <td class="py-1 px-2.5 border border-slate-200 text-center text-slate-500">${idx + 1}</td>
            <td class="py-1 px-2.5 border border-slate-200 font-semibold text-slate-800">${it.nama}</td>
            <td class="py-1 px-2.5 border border-slate-200 text-right tabular-nums font-bold text-slate-900">${it.nominal_format}</td>
          </tr>
        `;
      });
      tbody.innerHTML = tbodyHtml;

    } catch (e) {
      console.error(e);
      alert('Gagal mengambil kuitansi');
    }
  }

  function closeKuitansiModal() {
    const modal = document.getElementById('keu-kuitansi-modal');
    if (modal) {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }
  }

  function printKuitansi() {
    window.print();
  }

  /**
   * Ekspor Rekap Keuangan ke Excel (SheetJS)
   */
  function exportKeuanganToExcel() {
    if (typeof XLSX === 'undefined') {
      alert('Library SheetJS belum dimuat.');
      return;
    }

    const tableBody = document.getElementById('keu-table-body');
    const allFilteredRows = getFilteredKeuanganRows();
    const selectedCheckboxes = Array.from(tableBody?.querySelectorAll('[data-keu-select]:checked') || []);

    let targetRows;
    if (selectedCheckboxes.length > 0) {
      const selectedRegs = new Set(selectedCheckboxes.map(cb => cb.value));
      targetRows = Array.from(document.querySelectorAll('.keu-data-row')).filter(r => selectedRegs.has(r.dataset.registration));
    } else {
      targetRows = allFilteredRows;
    }

    if (targetRows.length === 0) {
      if (typeof showToast === 'function') {
        showToast('Tidak ada data keuangan yang cocok untuk diekspor.', 'warning');
      } else {
        alert('Tidak ada data keuangan yang cocok untuk diekspor.');
      }
      return;
    }

    if (typeof showToast === 'function') {
      showToast(`Mengekspor ${targetRows.length} data keuangan ke Excel...`, 'info');
    }

    const excelData = [];
    excelData.push([
      'No. Registrasi',
      'Nama Siswa',
      'Asal Sekolah',
      'Jurusan',
      'Jalur',
      'Gelombang',
      'Tagihan Bersih (Rp)',
      'Sudah Dibayar (Rp)',
      'Sisa Tagihan (Rp)',
      'Status Baju',
      'Status Daftar Ulang'
    ]);

    targetRows.forEach(r => {
      const reg = r.dataset.registration;
      const billing = window.keuanganState.allBillingCache[reg];
      const name = r.querySelector('strong, span.font-extrabold')?.textContent?.trim() || '';
      const school = r.dataset.school || '';

      excelData.push([
        reg,
        name,
        school,
        r.dataset.jurusanName || r.dataset.jurusan,
        r.dataset.jalurName || r.dataset.jalur,
        r.dataset.gelombangName || r.dataset.gelombang,
        billing ? billing.total_tagihan_bersih : 0,
        billing ? billing.total_sudah_dibayar : 0,
        billing ? billing.total_sisa : 0,
        r.dataset.baju,
        r.dataset.status
      ]);
    });

    const ws = XLSX.utils.aoa_to_sheet(excelData);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Rekap Keuangan');
    XLSX.writeFile(wb, `Rekap_Keuangan_SPMB_2027_${new Date().toISOString().slice(0, 10)}.xlsx`);
  }

  function formatRupiah(num) {
    num = Number(num) || 0;
    return 'Rp ' + Math.round(num).toLocaleString('id-ID');
  }

  // --- Dropdown Menu Aksi ---
  function toggleKeuActionMenu(regNo, event) {
    if (event) event.stopPropagation();
    const menu = document.getElementById(`keu-action-menu-${regNo}`);
    const isHidden = menu ? menu.classList.contains('hidden') : true;
    closeAllKeuActionMenus();
    if (menu && isHidden) {
      menu.classList.remove('hidden');
    }
  }

  function closeAllKeuActionMenus() {
    document.querySelectorAll('.keu-action-menu').forEach(menu => menu.classList.add('hidden'));
  }

  document.addEventListener('click', () => closeAllKeuActionMenus());

  // --- Reset / Void Pembayaran Siswa ---
  function openResetPaymentModal(regNo, studentName, totalPaid) {
    document.getElementById('reset-payment-reg').value = regNo;
    document.getElementById('reset-payment-name').textContent = studentName || regNo;
    document.getElementById('reset-payment-reg-label').textContent = `#${regNo}`;
    document.getElementById('reset-payment-amount').textContent = formatRupiah(totalPaid);
    document.getElementById('reset-payment-alasan').value = '';
    const m = document.getElementById('keu-reset-payment-modal');
    if (m) {
      m.classList.remove('hidden');
      m.classList.add('flex');
    }
  }

  function closeResetPaymentModal() {
    const m = document.getElementById('keu-reset-payment-modal');
    if (m) {
      m.classList.add('hidden');
      m.classList.remove('flex');
    }
  }

  async function submitResetPayment(event) {
    event.preventDefault();
    const regNo = document.getElementById('reset-payment-reg').value;
    const alasan = document.getElementById('reset-payment-alasan').value;
    const btn = document.getElementById('btn-submit-reset-payment');

    btn.disabled = true;
    btn.innerHTML = '<span>Memproses...</span>';

    try {
      const res = await fetch('{{ route('admin.keuangan.reset-pembayaran') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ no_registrasi: regNo, alasan: alasan })
      });
      const data = await res.json();
      if (!res.ok || !data.success) {
        alert(data.message || 'Gagal mereset pembayaran.');
        btn.disabled = false;
        btn.innerHTML = '<span>Ya, Reset Pembayaran</span>';
        return;
      }
      closeResetPaymentModal();
      alert(data.message || 'Pembayaran berhasil di-reset.');
      window.location.reload();
    } catch (e) {
      alert('Terjadi kesalahan jaringan.');
      btn.disabled = false;
      btn.innerHTML = '<span>Ya, Reset Pembayaran</span>';
    }
  }

  // --- Hapus Siswa & Tagihan ---
  function openDeleteStudentModal(regNo, studentName) {
    document.getElementById('delete-student-reg').value = regNo;
    document.getElementById('delete-student-name').textContent = studentName || regNo;
    document.getElementById('delete-student-reg-label').textContent = `#${regNo}`;
    const m = document.getElementById('keu-delete-student-modal');
    if (m) {
      m.classList.remove('hidden');
      m.classList.add('flex');
    }
  }

  function closeDeleteStudentModal() {
    const m = document.getElementById('keu-delete-student-modal');
    if (m) {
      m.classList.add('hidden');
      m.classList.remove('flex');
    }
  }

  async function submitDeleteStudent(event) {
    event.preventDefault();
    const regNo = document.getElementById('delete-student-reg').value;
    const btn = document.getElementById('btn-submit-delete-student');

    btn.disabled = true;
    btn.innerHTML = '<span>Menghapus...</span>';

    try {
      const res = await fetch('{{ route('admin.keuangan.hapus-siswa') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ no_registrasi: regNo })
      });
      const data = await res.json();
      if (!res.ok || !data.success) {
        alert(data.message || 'Gagal menghapus siswa.');
        btn.disabled = false;
        btn.innerHTML = '<span>Hapus Permanen</span>';
        return;
      }
      closeDeleteStudentModal();
      alert(data.message || 'Data siswa berhasil dihapus.');
      window.location.reload();
    } catch (e) {
      alert('Terjadi kesalahan jaringan.');
      btn.disabled = false;
      btn.innerHTML = '<span>Hapus Permanen</span>';
    }
  }

  // --- Void Kuitansi ---
  function triggerVoidKuitansi(noKuitansi, amount) {
    document.getElementById('void-kuitansi-number').value = noKuitansi;
    document.getElementById('void-kuitansi-label').textContent = noKuitansi;
    document.getElementById('void-kuitansi-amount').textContent = formatRupiah(amount);
    document.getElementById('void-kuitansi-alasan').value = '';
    const m = document.getElementById('keu-void-kuitansi-modal');
    if (m) {
      m.classList.remove('hidden');
      m.classList.add('flex');
    }
  }

  function closeVoidKuitansiModal() {
    const m = document.getElementById('keu-void-kuitansi-modal');
    if (m) {
      m.classList.add('hidden');
      m.classList.remove('flex');
    }
  }

  async function submitVoidKuitansi(event) {
    event.preventDefault();
    const noKuitansi = document.getElementById('void-kuitansi-number').value;
    const alasan = document.getElementById('void-kuitansi-alasan').value;
    const btn = document.getElementById('btn-submit-void-kuitansi');

    btn.disabled = true;
    btn.innerHTML = '<span>Membatalkan...</span>';

    try {
      const res = await fetch('{{ route('admin.keuangan.void-kuitansi') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ no_kuitansi: noKuitansi, alasan: alasan })
      });
      const data = await res.json();
      if (!res.ok || !data.success) {
        alert(data.message || 'Gagal membatalkan kuitansi.');
        btn.disabled = false;
        btn.innerHTML = '<span>Batalkan Kuitansi Ini</span>';
        return;
      }
      closeVoidKuitansiModal();
      alert(data.message || 'Kuitansi berhasil dibatalkan (void).');
      window.location.reload();
    } catch (e) {
      alert('Terjadi kesalahan jaringan.');
      btn.disabled = false;
      btn.innerHTML = '<span>Batalkan Kuitansi Ini</span>';
    }
  }

  // --- Bulk Action Handlers ---
  function getSelectedKeuRegs() {
    const tableBody = document.getElementById('keu-table-body');
    if (!tableBody) return [];
    return Array.from(tableBody.querySelectorAll('[data-keu-select]:checked'))
      .map(cb => cb.value)
      .filter(Boolean);
  }

  function openBulkResetModal() {
    const regs = getSelectedKeuRegs();
    if (regs.length === 0) {
      alert('Pilih minimal 1 siswa terlebih dahulu.');
      return;
    }
    document.getElementById('bulk-reset-count').textContent = regs.length;
    document.getElementById('bulk-reset-alasan').value = '';
    const m = document.getElementById('keu-bulk-reset-modal');
    if (m) {
      m.classList.remove('hidden');
      m.classList.add('flex');
    }
  }

  function closeBulkResetModal() {
    const m = document.getElementById('keu-bulk-reset-modal');
    if (m) {
      m.classList.add('hidden');
      m.classList.remove('flex');
    }
  }

  async function submitBulkReset(event) {
    event.preventDefault();
    const regs = getSelectedKeuRegs();
    const alasan = document.getElementById('bulk-reset-alasan').value;
    const btn = document.getElementById('btn-submit-bulk-reset');

    btn.disabled = true;
    btn.innerHTML = '<span>Memproses...</span>';

    try {
      const res = await fetch('{{ route('admin.keuangan.reset-terpilih') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ no_registrasi: regs, alasan: alasan })
      });
      const data = await res.json();
      if (!res.ok || !data.success) {
        alert(data.message || 'Gagal mereset tagihan terpilih.');
        btn.disabled = false;
        btn.innerHTML = '<span>Ya, Reset Semua Terpilih</span>';
        return;
      }
      closeBulkResetModal();
      alert(data.message || 'Tagihan siswa terpilih berhasil di-reset.');
      window.location.reload();
    } catch (e) {
      alert('Terjadi kesalahan jaringan.');
      btn.disabled = false;
      btn.innerHTML = '<span>Ya, Reset Semua Terpilih</span>';
    }
  }

  function openBulkDeleteModal() {
    const regs = getSelectedKeuRegs();
    if (regs.length === 0) {
      alert('Pilih minimal 1 siswa terlebih dahulu.');
      return;
    }
    document.getElementById('bulk-delete-count').textContent = regs.length;
    const m = document.getElementById('keu-bulk-delete-modal');
    if (m) {
      m.classList.remove('hidden');
      m.classList.add('flex');
    }
  }

  function closeBulkDeleteModal() {
    const m = document.getElementById('keu-bulk-delete-modal');
    if (m) {
      m.classList.add('hidden');
      m.classList.remove('flex');
    }
  }

  async function submitBulkDelete(event) {
    event.preventDefault();
    const regs = getSelectedKeuRegs();
    const btn = document.getElementById('btn-submit-bulk-delete');

    btn.disabled = true;
    btn.innerHTML = '<span>Menghapus...</span>';

    try {
      const res = await fetch('{{ route('admin.keuangan.destroy-selected') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ no_registrasi: regs })
      });
      const data = await res.json();
      if (!res.ok || !data.success) {
        alert(data.message || 'Gagal menghapus siswa terpilih.');
        btn.disabled = false;
        btn.innerHTML = '<span>Ya, Hapus Terpilih</span>';
        return;
      }
      closeBulkDeleteModal();
      alert(data.message || 'Siswa terpilih berhasil dihapus.');
      window.location.reload();
    } catch (e) {
      alert('Terjadi kesalahan jaringan.');
      btn.disabled = false;
      btn.innerHTML = '<span>Ya, Hapus Terpilih</span>';
    }
  }

  function exportSelectedKeuangan() {
    const regs = getSelectedKeuRegs();
    if (regs.length === 0) {
      alert('Pilih minimal 1 siswa untuk diekspor.');
      return;
    }
    if (typeof XLSX === 'undefined') {
      alert('Modul Excel (SheetJS) belum termuat.');
      return;
    }

    const excelData = [];
    excelData.push([
      'No. Registrasi',
      'Nama Lengkap',
      'Asal SMP',
      'Jurusan',
      'Jalur',
      'Gelombang',
      'Tagihan Bersih (Rp)',
      'Sudah Dibayar (Rp)',
      'Sisa Tagihan (Rp)',
      'Status Baju',
      'Status Daftar Ulang'
    ]);

    regs.forEach(reg => {
      const row = document.getElementById(`keu-row-${reg}`);
      if (!row) return;
      const billing = window.keuanganState.allBillingCache[reg];
      const name = row.querySelector('strong, span.font-extrabold')?.textContent?.trim() || '';
      const school = row.dataset.school || '';

      excelData.push([
        reg,
        name,
        school,
        row.dataset.jurusanName || row.dataset.jurusan,
        row.dataset.jalurName || row.dataset.jalur,
        row.dataset.gelombangName || row.dataset.gelombang,
        billing ? billing.total_tagihan_bersih : 0,
        billing ? billing.total_sudah_dibayar : 0,
        billing ? billing.total_sisa : 0,
        row.dataset.baju,
        row.dataset.status
      ]);
    });

    const ws = XLSX.utils.aoa_to_sheet(excelData);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Terpilih');
    XLSX.writeFile(wb, `Rekap_Keuangan_Terpilih_${new Date().toISOString().slice(0, 10)}.xlsx`);
  }
</script>

<style>
  .keu-filter-flyout::before {
    content: '';
    position: absolute;
    top: -8px;
    bottom: -8px;
    right: -14px;
    width: 16px;
    background: transparent;
  }

  .keu-filter-category:hover > .keu-filter-flyout,
  .keu-filter-category:focus-within > .keu-filter-flyout,
  .keu-filter-category.flyout-active > .keu-filter-flyout {
    display: block;
  }

  @media print {
    body * {
      visibility: hidden !important;
    }
    #printable-kuitansi-area,
    #printable-kuitansi-area * {
      visibility: visible !important;
    }
    #printable-kuitansi-area {
      position: absolute !important;
      left: 0 !important;
      top: 0 !important;
      width: 100% !important;
      max-width: 680px !important;
      margin: 0 auto !important;
      padding: 1.5rem !important;
      border: 1px solid #cbd5e1 !important;
      background: #ffffff !important;
      display: block !important;
    }
  }
</style>

