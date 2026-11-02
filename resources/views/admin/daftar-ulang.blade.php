<!-- ============================================================== -->
<!-- 5. HALAMAN DAFTAR ULANG SISWA TERVERIFIKASI                   -->
<!-- ============================================================== -->
@php
  // Ambil data murid yang berstatus Diterima (terverifikasi penuh)
  $verifiedStudents = $pendaftaranList->filter(function ($item) {
      return $item->status_berkas === 'Diterima';
  });

@endphp

<section
  id="section-daftar-ulang"
  class="bg-white rounded-[14px] border border-slate-200/90 no-print transition-all {{ ($activePage ?? 'beranda') !== 'daftar-ulang' ? 'hidden' : '' }}"
  data-status-endpoint="{{ route('admin.daftar-ulang.status') }}"
  data-delete-endpoint="{{ route('admin.pendaftaran.destroy', ['no_registrasi' => '__NO_REGISTRASI__']) }}"
  data-csrf="{{ csrf_token() }}"
>
  <!-- Header Tabel Daftar Ulang -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 sm:p-6 border-b border-slate-200/80">
    <div class="flex items-center gap-3.5">
      <div class="w-11 h-11 rounded-[10px] bg-emerald-50 text-emerald-600 border border-emerald-100/90 flex items-center justify-center shrink-0">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-6 9 2 2 4-4" />
        </svg>
      </div>
      <div>
        <h2 class="text-lg sm:text-[20px] font-bold text-slate-900 tracking-tight leading-tight">Tabel Daftar Ulang Siswa</h2>
        <p class="text-[13.5px] text-slate-500 mt-0.5">Siswa terverifikasi yang siap atau menunggu proses daftar ulang.</p>
      </div>
    </div>

    <div class="flex items-center gap-3 self-start sm:self-auto flex-wrap">
      <span class="text-xs font-medium text-slate-500 bg-slate-50 px-3 py-2 rounded-lg border border-slate-200/80">
        Terpilih: <strong id="du-table-selected-badge" class="font-bold text-slate-800">0</strong> Siswa
      </span>
      <button
        type="button"
        onclick="exportDaftarUlangToExcel()"
        title="Ekspor Data Daftar Ulang (Format Excel)"
        class="h-10 px-4 rounded-lg bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold text-sm inline-flex items-center gap-2 transition cursor-pointer active:scale-[0.98]"
      >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M12 3v12m0 0-4-4m4 4 4-4M4 20h16"></path>
        </svg>
        <span>Ekspor Excel</span>
      </button>
    </div>
  </div>

  <div class="p-4 sm:p-5 border-b border-slate-200/80 flex items-center gap-2.5 bg-white">
    <div class="inline-flex items-center p-1 bg-slate-100 rounded-lg border border-slate-200/90 h-10 shrink-0 gap-0.5" role="group" aria-label="Urutan siswa daftar ulang">
      <button
        type="button"
        id="du-sort-newest"
        aria-label="Urutkan nomor registrasi terbaru"
        aria-pressed="true"
        onclick="setDuSortOrder('desc')"
        class="du-sort-button h-8 w-8 inline-flex items-center justify-center rounded-md bg-white text-blue-600 font-bold transition cursor-pointer"
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
        id="du-sort-oldest"
        aria-label="Urutkan nomor registrasi terlama"
        aria-pressed="false"
        onclick="setDuSortOrder('asc')"
        class="du-sort-button h-8 w-8 inline-flex items-center justify-center rounded-md text-slate-400 hover:text-slate-700 hover:bg-white/60 transition cursor-pointer"
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

    <label class="relative flex-1 min-w-0">
      <span class="sr-only">Cari siswa daftar ulang</span>
      <input
        id="du-search-input"
        type="search"
        placeholder="Cari no. registrasi, nama, atau asal SMP"
        class="w-full h-10 text-sm rounded-lg border border-slate-200/90 bg-white pl-10 pr-3.5 py-2 text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition"
      />
      <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <circle cx="11" cy="11" r="7"></circle>
        <path d="m20 20-3.5-3.5"></path>
      </svg>
    </label>

    <div class="relative shrink-0" id="du-filter-container">
      <button
        type="button"
        id="du-filter-trigger"
        aria-expanded="false"
        aria-haspopup="true"
        aria-controls="du-filter-menu"
        class="h-10 px-3.5 rounded-lg border border-slate-200/90 bg-white hover:bg-slate-50 text-slate-700 font-medium text-sm inline-flex items-center gap-2 transition cursor-pointer select-none"
      >
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
        </svg>
        <span>Filter</span>
        <span id="du-filter-badge" class="hidden px-1.5 py-0.5 text-[11px] font-bold rounded-full bg-blue-600 text-white leading-none">0</span>
        <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="m6 9 6 6 6-6"></path>
        </svg>
      </button>

      <div id="du-filter-menu" class="hidden absolute top-full mt-2 right-0 w-60 bg-white rounded-xl border border-slate-200/90 shadow-xl p-2 z-40">
        <div class="du-filter-category relative" data-category="status">
          <button type="button" aria-expanded="false" class="du-filter-category-trigger w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer text-left">
            <span class="flex items-center gap-2 min-w-0">
              <span class="truncate">Status Daftar Ulang</span>
              <span class="du-filter-active-dot hidden w-2 h-2 rounded-full bg-blue-600 shrink-0"></span>
              <span class="du-filter-active-label hidden text-[11px] font-semibold text-blue-600 truncate"></span>
            </span>
            <svg class="w-4 h-4 text-slate-400 shrink-0 ml-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="m9 18 6-6-6-6"></path>
            </svg>
          </button>
          <div class="du-filter-flyout hidden absolute right-full top-0 mr-2 min-w-[220px] bg-white rounded-xl border border-slate-200/90 shadow-xl p-1.5 z-50">
            <div class="px-2.5 py-1 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 mb-1">Status Daftar Ulang</div>
            <button type="button" data-filter-category="status" data-filter-value="" data-filter-label="Semua" onclick="selectDuFilter('status', '', 'Semua', event)" class="du-filter-option w-full flex items-center justify-between px-2.5 py-2 rounded-lg text-xs text-left font-bold text-blue-700 bg-blue-50/70">
              <span>Semua Status</span>
              <svg class="du-filter-check w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </button>
            <button type="button" data-filter-category="status" data-filter-value="Menunggu" data-filter-label="Menunggu" onclick="selectDuFilter('status', 'Menunggu', 'Menunggu', event)" class="du-filter-option w-full flex items-center justify-between px-2.5 py-2 rounded-lg text-xs text-left font-medium text-slate-700 hover:bg-slate-100">
              <span>Menunggu</span>
              <svg class="du-filter-check hidden w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </button>
            <button type="button" data-filter-category="status" data-filter-value="Sudah Daftar Ulang" data-filter-label="Sudah Daftar Ulang" onclick="selectDuFilter('status', 'Sudah Daftar Ulang', 'Sudah Daftar Ulang', event)" class="du-filter-option w-full flex items-center justify-between px-2.5 py-2 rounded-lg text-xs text-left font-medium text-slate-700 hover:bg-slate-100">
              <span>Sudah Daftar Ulang</span>
              <svg class="du-filter-check hidden w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </button>
          </div>
        </div>

        <div class="du-filter-category relative" data-category="payment">
          <button type="button" aria-expanded="false" class="du-filter-category-trigger w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer text-left">
            <span class="flex items-center gap-2 min-w-0">
              <span class="truncate">Status Pembayaran Awal</span>
              <span class="du-filter-active-dot hidden w-2 h-2 rounded-full bg-blue-600 shrink-0"></span>
              <span class="du-filter-active-label hidden text-[11px] font-semibold text-blue-600 truncate"></span>
            </span>
            <svg class="w-4 h-4 text-slate-400 shrink-0 ml-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="m9 18 6-6-6-6"></path>
            </svg>
          </button>
          <div class="du-filter-flyout hidden absolute right-full top-0 mr-2 min-w-[220px] bg-white rounded-xl border border-slate-200/90 shadow-xl p-1.5 z-50">
            <div class="px-2.5 py-1 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 mb-1">Status Pembayaran Awal</div>
            <button type="button" data-filter-category="payment" data-filter-value="" data-filter-label="Semua" onclick="selectDuFilter('payment', '', 'Semua', event)" class="du-filter-option w-full flex items-center justify-between px-2.5 py-2 rounded-lg text-xs text-left font-bold text-blue-700 bg-blue-50/70">
              <span>Semua Status</span>
              <svg class="du-filter-check w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </button>
            <button type="button" data-filter-category="payment" data-filter-value="Belum" data-filter-label="Belum" onclick="selectDuFilter('payment', 'Belum', 'Belum', event)" class="du-filter-option w-full flex items-center justify-between px-2.5 py-2 rounded-lg text-xs text-left font-medium text-slate-700 hover:bg-slate-100">
              <span>Belum</span>
              <svg class="du-filter-check hidden w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </button>
            <button type="button" data-filter-category="payment" data-filter-value="Nyicil" data-filter-label="Nyicil" onclick="selectDuFilter('payment', 'Nyicil', 'Nyicil', event)" class="du-filter-option w-full flex items-center justify-between px-2.5 py-2 rounded-lg text-xs text-left font-medium text-slate-700 hover:bg-slate-100">
              <span>Nyicil</span>
              <svg class="du-filter-check hidden w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </button>
            <button type="button" data-filter-category="payment" data-filter-value="Lunas" data-filter-label="Lunas" onclick="selectDuFilter('payment', 'Lunas', 'Lunas', event)" class="du-filter-option w-full flex items-center justify-between px-2.5 py-2 rounded-lg text-xs text-left font-medium text-slate-700 hover:bg-slate-100">
              <span>Lunas</span>
              <svg class="du-filter-check hidden w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </button>
          </div>
        </div>

        <div id="du-filter-reset-divider" class="hidden border-t border-slate-100 my-1.5"></div>
        <button
          type="button"
          id="du-filter-reset-btn"
          onclick="resetDuFilters(event)"
          class="hidden w-full items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition cursor-pointer select-none"
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
  <div id="du-bulk-action-bar" class="hidden items-center justify-between gap-3 px-5 py-3 bg-emerald-50/90 border-b border-emerald-200 transition-all duration-200">
    <div class="flex items-center gap-2">
      <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-600 text-white font-bold text-xs" id="du-bulk-selected-count">0</span>
      <span class="text-xs font-bold text-emerald-950">Siswa Terpilih</span>
      <span class="text-xs text-emerald-700 hidden sm:inline">• Tindakan serentak:</span>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
      <button
        type="button"
        onclick="exportSelectedDaftarUlang()"
        class="h-8 px-3 rounded-lg bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 font-bold text-xs inline-flex items-center gap-1.5 transition active:scale-95 cursor-pointer"
      >
        <svg class="w-3.5 h-3.5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v12m0 0-4-4m4 4 4-4M4 20h16"></path></svg>
        <span>Ekspor Excel</span>
      </button>
      <button
        type="button"
        onclick="bulkMarkSudahDaftarUlang()"
        class="h-8 px-3 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs inline-flex items-center gap-1.5 transition active:scale-95 cursor-pointer"
      >
        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        <span>Tandai Sudah Daftar Ulang</span>
      </button>
      <button
        type="button"
        id="du-bulk-delete-btn"
        onclick="triggerDuBulkDelete()"
        class="h-8 px-3 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 font-bold text-xs inline-flex items-center gap-1.5 transition active:scale-95 cursor-pointer"
      >
        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"></path><path d="M8 6V4h8v2"></path><path d="m19 6-1 14H6L5 6"></path><path d="M10 11v5"></path><path d="M14 11v5"></path></svg>
        <span id="du-bulk-delete-btn-text">Hapus Terpilih</span>
      </button>
      <button
        type="button"
        onclick="clearDuSelections()"
        class="h-8 px-2.5 rounded-lg text-slate-500 hover:text-slate-700 text-xs font-semibold cursor-pointer"
        title="Batalkan pilihan"
      >
        Batal
      </button>
    </div>
  </div>

  <!-- Tabel Siswa Terverifikasi -->
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse text-xs">
      <thead>
        <tr class="bg-slate-50/90 text-slate-600 border-b border-slate-200 uppercase tracking-wider text-[11px] font-bold">
          <th scope="col" data-export-ignore class="w-12 px-3 py-3.5 text-center">
            <input
              id="du-select-all"
              type="checkbox"
              aria-label="Pilih semua siswa daftar ulang"
              class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500 cursor-pointer"
            />
          </th>
          <th scope="col" class="px-4 py-3.5 whitespace-nowrap">No. Registrasi</th>
          <th scope="col" class="px-4 py-3.5 whitespace-nowrap">Tgl. Verifikasi</th>
          <th scope="col" class="px-4 py-3.5 whitespace-nowrap text-center">Tgl. Daftar Ulang</th>
          <th scope="col" class="px-4 py-3.5">Nama</th>
          <th scope="col" class="px-4 py-3.5">NISN</th>
          <th scope="col" class="px-4 py-3.5">WhatsApp</th>
          <th scope="col" class="px-4 py-3.5 whitespace-nowrap text-center">Status Pembayaran</th>
          <th scope="col" class="px-4 py-3.5">Jurusan</th>
          <th scope="col" hidden>Tanggal Daftar</th>
          <th scope="col" hidden>Status Daftar Ulang</th>
          <th scope="col" data-export-ignore class="px-4 py-3.5 text-center">AKSI</th>
        </tr>
      </thead>
      <tbody id="du-table-body" class="divide-y divide-slate-100">
        @foreach ($verifiedStudents as $item)
          @php
            $duStatus = $item->status_daftar_ulang ?? 'Menunggu';
            $isSudah = $duStatus === 'Sudah Daftar Ulang';
            $paymentStatus = $item->status_pembayaran_awal ?? $item->status_pembayaran ?? '';
            $normalizedPaymentStatus = strtolower(trim((string) $paymentStatus));
            $paymentBadgeClass = match (true) {
                str_starts_with($normalizedPaymentStatus, 'belum') => 'bg-rose-50 text-rose-700 border-rose-200',
                str_contains($normalizedPaymentStatus, 'cicil') => 'bg-amber-50 text-amber-700 border-amber-200',
                str_contains($normalizedPaymentStatus, 'lunas') => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                default => 'bg-slate-50 text-slate-600 border-slate-200',
            };
            $paymentLabel = match (true) {
                str_starts_with($normalizedPaymentStatus, 'belum') => 'Belum',
                str_contains($normalizedPaymentStatus, 'cicil') => 'Nyicil',
                str_contains($normalizedPaymentStatus, 'lunas') => 'Lunas',
                default => $paymentStatus ?: '-',
            };
            $tglVerif = $item->verifikasi?->tgl_verifikasi
                ? date('d M Y', strtotime($item->verifikasi->tgl_verifikasi))
                : ($item->tgl_daftar ? date('d M Y', strtotime($item->tgl_daftar)) : '-');
            $tglDuRaw = $isSudah ? ($item->tgl_daftar_ulang ?: now('Asia/Makassar')->format('Y-m-d H:i:s')) : null;
            $tglDu = $tglDuRaw
                ? (str_contains((string) $tglDuRaw, ':') ? \Illuminate\Support\Carbon::parse($tglDuRaw, 'Asia/Makassar')->format('d M Y H:i') : $tglDuRaw)
                : null;
          @endphp
          <tr
            id="du-row-{{ $item->no_registrasi }}"
            data-registration="{{ $item->no_registrasi }}"
            data-du-status="{{ $duStatus }}"
            data-payment-status="{{ $item->status_pembayaran_awal ?? $item->status_pembayaran ?? '' }}"
            data-search-text="{{ strtolower($item->no_registrasi . ' ' . ($item->calonSiswa?->nama_lengkap ?? '') . ' ' . ($item->calonSiswa?->asal_sekolah ?? '')) }}"
            class="hover:bg-blue-50/30 transition-colors text-slate-700 du-data-row"
          >
            <!-- Kolom Checkbox -->
            <td class="w-12 px-3 py-3.5 text-center">
              <input
                type="checkbox"
                name="no_registrasi[]"
                value="{{ $item->no_registrasi }}"
                data-du-select
                aria-label="Pilih siswa {{ $item->no_registrasi }}"
                class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500 cursor-pointer"
              />
            </td>

            <!-- Kolom No. Registrasi -->
            <td class="px-4 py-3.5 whitespace-nowrap">
              <button
                type="button"
                data-export-value="{{ $item->no_registrasi }}"
                onclick="openReviewDrawer('{{ $item->no_registrasi }}')"
                class="font-mono font-bold text-blue-600 hover:text-blue-800 hover:underline text-[13.5px] cursor-pointer inline-block"
                title="Lihat Detail {{ $item->no_registrasi }}"
              >
                {{ $item->no_registrasi }}
              </button>
              <span class="block text-[11px] text-slate-400 font-normal mt-0.5">
                {{ date('d M Y', strtotime($item->tgl_daftar)) }}
              </span>
            </td>

            <!-- Kolom Tgl. Verifikasi (Terpisah di Kiri Siswa) -->
            <td class="px-4 py-3.5 whitespace-nowrap text-slate-700 font-mono text-[12px]">
              {{ $tglVerif }}
            </td>

            <!-- Kolom Tgl. Daftar Ulang -->
            <td
              id="du-tgl-{{ $item->no_registrasi }}"
              class="px-4 py-3.5 whitespace-nowrap font-mono text-[12px] du-col-tgl-du text-center"
            >
              @if ($isSudah)
                <span data-export-value="{{ $tglDu }}" class="inline-flex h-8 w-max items-center justify-center rounded-full border border-emerald-200 bg-emerald-50 px-1 text-[11px] font-semibold leading-none text-emerald-700">
                  {{ $tglDu }}
                </span>
              @else
                <span data-export-value="Belum" class="inline-flex h-8 w-max items-center justify-center rounded-full border border-amber-200 bg-amber-50 px-1 text-[11px] font-semibold leading-none text-amber-700">
                  Belum
                </span>
              @endif
            </td>

            <!-- Kolom Nama -->
            <td class="px-4 py-3.5">
              <strong class="font-bold text-slate-900 text-[13.5px] break-words">
                {{ $item->calonSiswa->nama_lengkap ?? '-' }}
              </strong>
            </td>

            <!-- Kolom NISN -->
            <td class="px-4 py-3.5 whitespace-nowrap font-mono text-slate-700 text-[12.5px]">
              {{ $item->calonSiswa?->nisn ?: '-' }}
            </td>

            <!-- Kolom WhatsApp (Tanpa Ikon) -->
            <td class="px-4 py-3.5 whitespace-nowrap text-slate-700 font-medium font-mono text-[12.5px]">
              @if ($item->calonSiswa?->no_whatsapp)
                <a
                  href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $item->calonSiswa->no_whatsapp)) }}"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="hover:text-blue-600 hover:underline"
                  title="Hubungi Siswa via WhatsApp"
                >
                  {{ $item->calonSiswa->no_whatsapp }}
                </a>
              @else
                <span class="text-slate-400">-</span>
              @endif
            </td>

            <!-- Kolom Status Pembayaran -->
            <td id="du-payment-{{ $item->no_registrasi }}" class="px-4 py-3.5 whitespace-nowrap text-center">
              <span class="inline-flex items-center justify-center rounded-full border px-2.5 py-1 text-xs font-semibold {{ $paymentBadgeClass }}">
                {{ $paymentLabel }}
              </span>
            </td>

            <!-- Kolom Jurusan -->
            <td class="px-4 py-3.5 whitespace-nowrap">
              <span
                title="{{ $item->jurusan->nama_jurusan ?? '-' }}"
                class="inline-block px-2.5 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-200/70 font-bold text-xs"
              >
                {{ str_contains(strtoupper($item->jurusan->nama_jurusan ?? ''), 'KECANTIKAN') ? 'TKC' : explode(' ', $item->jurusan->nama_jurusan ?? '-', 2)[0] }}
              </span>
            </td>
            <td hidden>{{ $item->tgl_daftar ? date('d M Y', strtotime($item->tgl_daftar)) : '-' }}</td>
            <td hidden>{{ $duStatus }}</td>

            <!-- Kolom Aksi -->
            <td class="px-4 py-3 text-center whitespace-nowrap">
              <div class="inline-flex items-center justify-center gap-1.5">
                <button
                  type="button"
                  onclick="openDaftarUlangEdit('{{ $item->no_registrasi }}')"
                  title="Kelola status daftar ulang {{ $item->no_registrasi }}"
                  aria-label="Edit status daftar ulang dan pembayaran {{ $item->calonSiswa->nama_lengkap ?? $item->no_registrasi }}"
                  class="h-8 px-2.5 rounded-lg border border-slate-200 bg-white text-slate-700 hover:text-blue-600 hover:bg-blue-50 hover:border-blue-200 transition inline-flex items-center gap-1 font-semibold text-xs cursor-pointer active:scale-95"
                >
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 20h9"></path><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                  </svg>
                  <span>Edit Status</span>
                </button>
                <button
                  type="button"
                  onclick="openReviewDrawer('{{ $item->no_registrasi }}')"
                  title="Tinjau berkas pendaftaran {{ $item->no_registrasi }}"
                  class="w-8 h-8 rounded-lg border border-slate-200 bg-white text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 hover:border-emerald-200 transition inline-flex items-center justify-center cursor-pointer active:scale-95"
                >
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
                <button
                  type="button"
                  onclick="revertVerification('{{ $item->no_registrasi }}', '{{ addslashes($item->calonSiswa?->nama_lengkap ?? '') }}', {{ (int) ($pendaftaranBilling[$item->no_registrasi]['total_sudah_dibayar'] ?? 0) }})"
                  title="Batalkan Verifikasi & Kembalikan ke Menunggu"
                  class="w-8 h-8 rounded-lg border border-amber-200 bg-white text-amber-600 hover:text-amber-800 hover:bg-amber-50 hover:border-amber-300 transition inline-flex items-center justify-center cursor-pointer active:scale-95"
                >
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                </button>
              </div>
            </td>
          </tr>
        @endforeach
        <tr id="du-empty-row" class="{{ $verifiedStudents->count() > 0 ? 'hidden' : '' }}">
          <td colspan="10" class="px-6 py-12 text-center text-slate-400">
            <div class="flex flex-col items-center justify-center">
              <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center mb-2.5">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-6 9 2 2 4-4" />
                </svg>
              </div>
              <p id="du-empty-title" class="font-bold text-slate-700 text-sm">Belum Ada Siswa Terverifikasi</p>
              <p id="du-empty-description" class="text-xs text-slate-500 mt-0.5">Siswa yang lolos verifikasi akan otomatis tampil di sini.</p>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Footer Tabel Daftar Ulang -->
  <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-3.5 bg-white border-t border-slate-200/90 text-xs sm:text-[13px] text-slate-500">
    <span id="du-table-count">Menampilkan <span id="du-visible-count" class="tabular-nums font-normal">0</span> pendaftar</span>

    <nav class="flex items-center gap-1.5" aria-label="Navigasi Halaman Daftar Ulang">
      <button
        type="button"
        id="du-page-prev"
        onclick="changeDuPage(currentDuPage - 1)"
        title="Halaman sebelumnya"
        class="w-9 h-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-300 cursor-not-allowed select-none transition"
        disabled
      >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="m15 18-6-6 6-6"></path>
        </svg>
      </button>

      <span id="du-page-indicator" class="px-2.5 py-1 text-xs font-semibold text-slate-600 select-none tabular-nums">1 / 1</span>

      <button
        type="button"
        id="du-page-next"
        onclick="changeDuPage(currentDuPage + 1)"
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
</section>

<div
  id="du-edit-modal"
  class="hidden fixed inset-0 z-[90] bg-slate-900/60 items-center justify-center p-4"
  role="dialog"
  aria-modal="true"
  aria-labelledby="du-edit-title"
>
  <form id="du-edit-form" class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-5 shadow-2xl sm:p-6">
    <div class="mb-5">
      <h3 id="du-edit-title" class="text-lg font-bold text-slate-900">Edit Status Siswa</h3>
      <p class="mt-1 text-sm text-slate-500">Atur status daftar ulang dan pembayaran awal.</p>
      <p class="mt-2 text-xs font-semibold text-slate-600">No. Registrasi: <span id="du-edit-registration" class="font-mono text-slate-900"></span></p>
    </div>

    <label for="du-edit-registration-status" class="mb-1.5 block text-sm font-semibold text-slate-700">Status Daftar Ulang</label>
    <select id="du-edit-registration-status" required class="mb-4 h-10 w-full rounded-lg border border-slate-200 bg-white px-3 text-sm text-slate-800 focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-100">
      <option value="Menunggu">Menunggu</option>
      <option value="Sudah Daftar Ulang">Sudah Daftar Ulang</option>
    </select>

    <label for="du-edit-payment-status" class="mb-1.5 block text-sm font-semibold text-slate-700">Status Pembayaran</label>
    <select id="du-edit-payment-status" required class="h-10 w-full rounded-lg border border-slate-200 bg-white px-3 text-sm text-slate-800 focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-100">
      <option value="">Pilih status pembayaran</option>
      <option value="Belum">Belum</option>
      <option value="Nyicil">Nyicil</option>
      <option value="Lunas">Lunas</option>
    </select>

    <div class="mt-3 rounded-lg border border-blue-200 bg-blue-50/70 p-2.5 text-[11px] text-blue-700 leading-relaxed">
      <div class="flex items-start gap-1.5">
        <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="12" y1="16" x2="12" y2="12"></line>
          <line x1="12" y1="8" x2="12.01" y2="8"></line>
        </svg>
        <span>
          <strong>Terintegrasi Keuangan:</strong> Status <em>Lunas</em> di sini otomatis melunasi seluruh sisa tagihan pada modul Keuangan &amp; Kas Siswa.
        </span>
      </div>
    </div>

    <p id="du-edit-error" class="hidden mt-3 text-sm text-rose-600" role="alert"></p>

    <div class="mt-6 flex justify-end gap-2">
      <button id="du-edit-cancel" type="button" class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Batal</button>
      <button id="du-edit-submit" type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60">Simpan</button>
    </div>
  </form>
</div>

<style>
  #du-filter-menu::before {
    content: '';
    position: absolute;
    left: 0;
    right: 0;
    top: -10px;
    height: 12px;
    background: transparent;
  }

  .du-filter-flyout::before {
    content: '';
    position: absolute;
    top: -8px;
    bottom: -8px;
    right: -14px;
    width: 16px;
    background: transparent;
  }

  .du-filter-category:hover > .du-filter-flyout,
  .du-filter-category:focus-within > .du-filter-flyout,
  .du-filter-category.flyout-active > .du-filter-flyout {
    display: block;
  }
</style>

<!-- Script Filter dan Pengatur Status Daftar Ulang -->
<script>
  const currentDaftarUlangFilters = {
    status: '',
    payment: ''
  };
  let currentDuSortOrder = 'desc';
  let currentDuPage = 1;
  const duPerPage = 10;

  /**
   * Render pagination: sembunyikan/tampilkan baris sesuai halaman,
   * perbarui footer (range, total, tombol prev/next, indikator halaman).
   * Dipanggil setiap kali filter berubah, status berubah, atau halaman berpindah.
   */
  function renderDuPagination() {
    const rows = Array.from(document.querySelectorAll('.du-data-row'));
    const emptyRow = document.getElementById('du-empty-row');

    const filteredRows = getFilteredDaftarUlangRows(rows);

    const totalFiltered = filteredRows.length;
    const totalPages = Math.max(1, Math.ceil(totalFiltered / duPerPage));

    // Pastikan currentDuPage tidak melebihi total halaman
    if (currentDuPage > totalPages) {
      currentDuPage = totalPages;
    }
    if (currentDuPage < 1) {
      currentDuPage = 1;
    }

    const startIndex = (currentDuPage - 1) * duPerPage;
    const endIndex = Math.min(startIndex + duPerPage, totalFiltered);

    // Sembunyikan semua baris dulu
    rows.forEach(row => { row.style.display = 'none'; });

    // Tampilkan hanya baris di halaman aktif
    filteredRows.forEach((row, idx) => {
      row.style.display = (idx >= startIndex && idx < endIndex) ? '' : 'none';
    });

    // Baris kosong
    if (emptyRow) {
      emptyRow.classList.toggle('hidden', totalFiltered > 0);
      if (totalFiltered === 0) {
        const emptyTitle = document.getElementById('du-empty-title');
        const emptyDescription = document.getElementById('du-empty-description');
        const hasVerifiedStudents = rows.length > 0;
        if (emptyTitle) {
          emptyTitle.textContent = hasVerifiedStudents ? 'Tidak Ada Siswa yang Sesuai' : 'Belum Ada Siswa Terverifikasi';
        }
        if (emptyDescription) {
          emptyDescription.textContent = hasVerifiedStudents
            ? 'Coba ubah kata kunci atau pilihan filter.'
            : 'Siswa yang lolos verifikasi akan otomatis tampil di sini.';
        }
      }
    }

    // Perbarui footer counter
    const visibleCount = totalFiltered > 0 ? (endIndex - startIndex) : 0;
    const countEl = document.getElementById('du-visible-count');
    if (countEl) countEl.textContent = visibleCount;

    // Perbarui indikator halaman
    const pageIndicator = document.getElementById('du-page-indicator');
    if (pageIndicator) pageIndicator.textContent = currentDuPage + ' / ' + totalPages;

    // Perbarui tombol prev/next
    const prevBtn = document.getElementById('du-page-prev');
    const nextBtn = document.getElementById('du-page-next');

    if (prevBtn) {
      const canPrev = currentDuPage > 1;
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
      const canNext = currentDuPage < totalPages;
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

    // Perbarui checkbox & badge
    updateDuSelectAllState();
    updateDuSelectedBadge();
  }

  function getFilteredDaftarUlangRows(rows = Array.from(document.querySelectorAll('.du-data-row'))) {
    const searchInput = document.getElementById('du-search-input');
    const searchTerm = searchInput ? searchInput.value.trim().toLocaleLowerCase('id-ID') : '';

    const filteredRows = rows.filter(row => {
      const status = row.getAttribute('data-du-status') || 'Menunggu';
      const paymentStatus = normalizeDuPaymentStatus(row.getAttribute('data-payment-status'));
      const searchableText = (row.getAttribute('data-search-text') || row.textContent).toLocaleLowerCase('id-ID');

      return (!currentDaftarUlangFilters.status || status === currentDaftarUlangFilters.status) &&
        (!currentDaftarUlangFilters.payment || paymentStatus === currentDaftarUlangFilters.payment.toLocaleLowerCase('id-ID')) &&
        (!searchTerm || searchableText.includes(searchTerm));
    });
    filteredRows.sort((firstRow, secondRow) => {
      const firstRegistration = firstRow.getAttribute('data-registration') || '';
      const secondRegistration = secondRow.getAttribute('data-registration') || '';
      const comparison = firstRegistration.localeCompare(secondRegistration, 'id-ID', { numeric: true });
      return currentDuSortOrder === 'desc' ? -comparison : comparison;
    });

    return filteredRows;
  }

  function exportDaftarUlangToExcel() {
    const tableBody = document.getElementById('du-table-body');
    const rows = tableBody ? Array.from(tableBody.querySelectorAll('.du-data-row')) : [];
    const filteredRows = getFilteredDaftarUlangRows(rows);
    const selectedRegistrations = new Set(
      Array.from(tableBody?.querySelectorAll('[data-du-select]:checked') || []).map(checkbox => checkbox.value)
    );
    const targetRows = selectedRegistrations.size > 0
      ? rows.filter(row => selectedRegistrations.has(row.dataset.registration))
      : filteredRows;

    if (targetRows.length === 0) {
      if (typeof showToast === 'function') {
        showToast('Tidak ada data daftar ulang yang cocok untuk diekspor.', 'warning');
      }
      return;
    }

    const table = tableBody?.closest('table');
    const columns = Array.from(table?.tHead?.rows[0]?.cells || [])
      .map((header, index) => ({ header, index }))
      .filter(({ header }) => !header.hasAttribute('data-export-ignore'));
    const headers = columns.map(({ header }) => header.textContent);
    const exportRows = targetRows.map(row => columns.map(({ index }) => {
      const cell = row.cells[index];
      return cell?.querySelector('[data-export-value]')?.getAttribute('data-export-value')
        || cell?.querySelector('span[title]')?.getAttribute('title')
        || (cell?.hidden ? cell.textContent : cell?.innerText)
        || '';
    }));

    if (headers.length === 0) {
      showToast('Kolom tabel tidak tersedia untuk diekspor.', 'error');
      return;
    }

    window.exportRowsToExcel(
      headers,
      exportRows,
      `rekap_daftar_ulang_${new Date().toISOString().slice(0, 10)}.xlsx`,
      'Daftar Ulang'
    );
  }

  /**
   * Pindah halaman pagination daftar ulang
   */
  function changeDuPage(page) {
    currentDuPage = page;
    renderDuPagination();
  }

  function filterDaftarUlangTable() {
    currentDuPage = 1;
    renderDuPagination();
  }

  function setDuSortOrder(order) {
    currentDuSortOrder = order;
    const newestButton = document.getElementById('du-sort-newest');
    const oldestButton = document.getElementById('du-sort-oldest');

    [newestButton, oldestButton].forEach(button => {
      if (!button) {
        return;
      }

      const isActive = (order === 'desc' && button === newestButton) || (order === 'asc' && button === oldestButton);
      button.setAttribute('aria-pressed', String(isActive));
      button.classList.toggle('bg-white', isActive);
      button.classList.toggle('text-blue-600', isActive);
      button.classList.toggle('font-bold', isActive);
      button.classList.toggle('text-slate-400', !isActive);
      button.classList.toggle('hover:text-slate-700', !isActive);
      button.classList.toggle('hover:bg-white/60', !isActive);
    });

    filterDaftarUlangTable();
  }

  function normalizeDuPaymentStatus(status) {
    const normalizedStatus = String(status || '').trim().toLocaleLowerCase('id-ID');
    if (normalizedStatus.startsWith('belum')) {
      return 'belum';
    }
    if (normalizedStatus.includes('cicil')) {
      return 'nyicil';
    }
    if (normalizedStatus.includes('lunas')) {
      return 'lunas';
    }
    return normalizedStatus;
  }

  function getDuPaymentBadgeHtml(status) {
    const normalizedStatus = normalizeDuPaymentStatus(status);
    const badgeClasses = {
      belum: 'bg-rose-50 text-rose-700 border-rose-200',
      nyicil: 'bg-amber-50 text-amber-700 border-amber-200',
      lunas: 'bg-emerald-50 text-emerald-700 border-emerald-200'
    };
    const labels = {
      belum: 'Belum',
      nyicil: 'Nyicil',
      lunas: 'Lunas'
    };
    const label = labels[normalizedStatus] || status || '-';
    const classes = badgeClasses[normalizedStatus] || 'bg-slate-50 text-slate-600 border-slate-200';

    return `<span class="inline-flex items-center justify-center rounded-full border px-2.5 py-1 text-xs font-semibold ${classes}">${escapeDuHtml(label)}</span>`;
  }

  function selectDuFilter(category, value, label, event) {
    if (event) {
      event.preventDefault();
      event.stopPropagation();
    }

    currentDaftarUlangFilters[category] = value;

    const categoryItem = document.querySelector(`.du-filter-category[data-category="${category}"]`);
    if (categoryItem) {
      categoryItem.querySelectorAll('.du-filter-option').forEach(option => {
        const isSelected = option.dataset.filterValue === value;
        const check = option.querySelector('.du-filter-check');
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

      const activeDot = categoryItem.querySelector('.du-filter-active-dot');
      const activeLabel = categoryItem.querySelector('.du-filter-active-label');
      if (activeDot) {
        activeDot.classList.toggle('hidden', value === '');
      }
      if (activeLabel) {
        activeLabel.textContent = value ? label : '';
        activeLabel.classList.toggle('hidden', value === '');
      }
    }

    const activeFilterCount = Object.values(currentDaftarUlangFilters).filter(Boolean).length;
    const filterBadge = document.getElementById('du-filter-badge');
    const filterTrigger = document.getElementById('du-filter-trigger');
    if (filterBadge) {
      filterBadge.textContent = activeFilterCount;
      filterBadge.classList.toggle('hidden', activeFilterCount === 0);
    }
    if (filterTrigger) {
      filterTrigger.classList.toggle('border-blue-300', activeFilterCount > 0);
      filterTrigger.classList.toggle('text-blue-700', activeFilterCount > 0);
      filterTrigger.classList.toggle('bg-blue-50/40', activeFilterCount > 0);
    }
    const resetButton = document.getElementById('du-filter-reset-btn');
    const resetDivider = document.getElementById('du-filter-reset-divider');
    if (resetButton) {
      resetButton.classList.toggle('hidden', activeFilterCount === 0);
      resetButton.classList.toggle('flex', activeFilterCount > 0);
    }
    if (resetDivider) {
      resetDivider.classList.toggle('hidden', activeFilterCount === 0);
    }

    filterDaftarUlangTable();
  }

  function resetDuFilters(event) {
    if (event) {
      event.preventDefault();
      event.stopPropagation();
    }

    selectDuFilter('status', '', 'Semua');
    selectDuFilter('payment', '', 'Semua');
  }

  (function initDuFilterDropdown() {
    const container = document.getElementById('du-filter-container');
    const trigger = document.getElementById('du-filter-trigger');
    const menu = document.getElementById('du-filter-menu');
    if (!container || !trigger || !menu) {
      return;
    }

    let closeTimer = null;

    function closeDropdown() {
      clearTimeout(closeTimer);
      menu.classList.add('hidden');
      trigger.setAttribute('aria-expanded', 'false');
      container.querySelectorAll('.du-filter-category').forEach(category => {
        category.classList.remove('flyout-active');
        const categoryTrigger = category.querySelector('.du-filter-category-trigger');
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

    container.querySelectorAll('.du-filter-category-trigger').forEach(categoryTrigger => {
      categoryTrigger.addEventListener('click', event => {
        event.stopPropagation();
        const category = categoryTrigger.closest('.du-filter-category');
        const supportsHover = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
        const isOpen = category.classList.contains('flyout-active');
        container.querySelectorAll('.du-filter-category').forEach(item => {
          item.classList.remove('flyout-active');
          const itemTrigger = item.querySelector('.du-filter-category-trigger');
          if (itemTrigger) {
            itemTrigger.setAttribute('aria-expanded', 'false');
          }
        });
        const shouldOpen = supportsHover || !isOpen;
        category.classList.toggle('flyout-active', shouldOpen);
        categoryTrigger.setAttribute('aria-expanded', String(shouldOpen));
      });
    });

    container.querySelectorAll('.du-filter-category').forEach(category => {
      category.addEventListener('mouseenter', () => {
        container.querySelectorAll('.du-filter-category').forEach(item => {
          item.classList.toggle('flyout-active', item === category);
          const itemTrigger = item.querySelector('.du-filter-category-trigger');
          if (itemTrigger) {
            itemTrigger.setAttribute('aria-expanded', String(item === category));
          }
        });
      });
      category.addEventListener('mouseleave', () => {
        category.classList.remove('flyout-active');
        const categoryTrigger = category.querySelector('.du-filter-category-trigger');
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

  // Format tanggal hari ini dalam format 'dd MMM yyyy'
  function getTodayFormattedDate() {
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const now = new Date();
    const day = String(now.getDate()).padStart(2, '0');
    const month = months[now.getMonth()];
    const year = now.getFullYear();
    return `${day} ${month} ${year}`;
  }

  // Perbarui badge "Terpilih: N Siswa" dan contextual bulk toolbar
  function updateDuSelectedBadge() {
    const tableBody = document.getElementById('du-table-body');
    const badge = document.getElementById('du-table-selected-badge');
    const bulkBar = document.getElementById('du-bulk-action-bar');
    const bulkCount = document.getElementById('du-bulk-selected-count');
    if (!tableBody || !badge) return;

    const checkboxes = Array.from(tableBody.querySelectorAll('[data-du-select]'));
    const visibleCheckboxes = checkboxes.filter(cb => !cb.closest('tr').hidden && cb.closest('tr').style.display !== 'none');
    const selectedCount = visibleCheckboxes.filter(cb => cb.checked).length;
    badge.textContent = selectedCount;

    if (bulkBar) {
      if (selectedCount > 0) {
        bulkBar.classList.remove('hidden');
        bulkBar.classList.add('flex');
        if (bulkCount) bulkCount.textContent = selectedCount;
        const bulkDeleteBtnText = document.getElementById('du-bulk-delete-btn-text');
        if (bulkDeleteBtnText) {
          const isAllSelected = visibleCheckboxes.length > 0 && selectedCount === visibleCheckboxes.length;
          bulkDeleteBtnText.textContent = isAllSelected ? 'Hapus Semua' : 'Hapus Terpilih';
        }
      } else {
        bulkBar.classList.add('hidden');
        bulkBar.classList.remove('flex');
      }
    }
  }

  function triggerDuBulkDelete() {
    const tableBody = document.getElementById('du-table-body');
    if (!tableBody) return;
    const selectedCheckboxes = Array.from(tableBody.querySelectorAll('[data-du-select]:checked'));
    if (selectedCheckboxes.length === 0) {
      if (typeof showToast === 'function') showToast('Pilih setidaknya satu siswa untuk dihapus.', 'info');
      else alert('Pilih setidaknya satu siswa untuk dihapus.');
      return;
    }
    const regs = Array.from(new Set(selectedCheckboxes.map(cb => cb.value).filter(Boolean)));
    const selectedCount = regs.length;

    const doDelete = async () => {
      try {
        const res = await fetch('{{ route('admin.pendaftaran.destroy-selected') }}', {
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
          throw new Error(data.message || 'Gagal menghapus siswa terpilih.');
        }
        if (typeof showToast === 'function') {
          showToast(data.message || `${selectedCount} siswa berhasil dihapus.`, 'success');
        } else {
          alert(data.message || `${selectedCount} siswa berhasil dihapus.`);
        }
        setTimeout(() => window.location.reload(), 600);
      } catch (err) {
        if (typeof showToast === 'function') {
          showToast(err.message || 'Gagal menghapus siswa terpilih.', 'error');
        } else {
          alert(err.message || 'Gagal menghapus siswa terpilih.');
        }
      }
    };

    if (typeof window.openDeleteConfirmModal === 'function') {
      window.openDeleteConfirmModal({
        title: 'Hapus Siswa Terpilih',
        message: `Hapus <strong>${selectedCount} data siswa terpilih</strong> beserta seluruh berkas dan verifikasinya? Tindakan ini permanen.`,
        confirmText: `Ya, Hapus ${selectedCount} Data`,
        onConfirm: doDelete
      });
    } else {
      if (confirm(`Hapus ${selectedCount} data siswa terpilih secara permanen?`)) {
        doDelete();
      }
    }
  }

  function clearDuSelections() {
    const tableBody = document.getElementById('du-table-body');
    if (!tableBody) return;
    tableBody.querySelectorAll('[data-du-select]').forEach(cb => cb.checked = false);
    const selectAll = document.getElementById('du-select-all');
    if (selectAll) {
      selectAll.checked = false;
      selectAll.indeterminate = false;
    }
    updateDuSelectedBadge();
  }

  async function revertVerification(regNo, name, totalPaid) {
    if (totalPaid > 0) {
      alert(`Calon siswa #${regNo} (${name}) sudah memiliki riwayat pembayaran masuk sebesar Rp ${totalPaid.toLocaleString('id-ID')}.\n\nPembatalan verifikasi dilarang demi keamanan pembukuan kas. Harap lakukan Reset / Void atau Refund pembayaran terlebih dahulu di modul Keuangan.`);
      return;
    }

    if (!confirm(`Batalkan verifikasi untuk calon siswa #${regNo} (${name})?\n\nStatus berkas akan dikembalikan ke 'Menunggu Verifikasi' dan siswa ini akan dipindahkan dari Daftar Ulang ke antrean verifikasi.`)) {
      return;
    }

    try {
      const res = await fetch(`/admin/verifikasi/${regNo}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          status_akhir: 'Menunggu Verifikasi',
          catatan_verifikator: 'Dibatalkan verifikasinya dari modul Daftar Ulang'
        })
      });
      const data = await res.json();
      if (!res.ok || !data.success) {
        alert(data.message || 'Gagal membatalkan verifikasi.');
        return;
      }
      alert('Verifikasi berhasil dibatalkan. Siswa dikembalikan ke status Menunggu Verifikasi.');
      window.location.reload();
    } catch (e) {
      alert('Terjadi kesalahan jaringan.');
    }
  }

  async function bulkMarkSudahDaftarUlang() {
    const tableBody = document.getElementById('du-table-body');
    if (!tableBody) return;
    const selectedCheckboxes = Array.from(tableBody.querySelectorAll('[data-du-select]:checked'));
    if (selectedCheckboxes.length === 0) {
      alert('Pilih minimal 1 siswa terlebih dahulu.');
      return;
    }

    if (!confirm(`Tandai ${selectedCheckboxes.length} siswa terpilih sebagai 'Sudah Daftar Ulang'?`)) {
      return;
    }

    const regs = selectedCheckboxes.map(cb => cb.value);
    let successCount = 0;
    for (const reg of regs) {
      try {
        const res = await fetch('{{ route('admin.daftar-ulang.status') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            no_registrasi: reg,
            status: 'Sudah Daftar Ulang'
          })
        });
        if (res.ok) successCount++;
      } catch (e) {}
    }
    alert(`${successCount} dari ${regs.length} siswa berhasil diperbarui statusnya.`);
    window.location.reload();
  }

  function exportSelectedDaftarUlang() {
    const tableBody = document.getElementById('du-table-body');
    if (!tableBody) return;
    const selectedCheckboxes = Array.from(tableBody.querySelectorAll('[data-du-select]:checked'));
    if (selectedCheckboxes.length === 0) {
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
      'Nama Siswa',
      'NISN',
      'WhatsApp',
      'Jurusan',
      'Status Pembayaran',
      'Status Daftar Ulang'
    ]);

    selectedCheckboxes.forEach(cb => {
      const row = cb.closest('tr');
      if (!row) return;
      const reg = row.dataset.registration;
      const name = row.querySelector('.du-col-name, strong, a')?.textContent?.trim() || '';
      const nisn = row.cells[5]?.textContent?.trim() || '';
      const wa = row.cells[6]?.textContent?.trim() || '';
      const jur = row.cells[8]?.textContent?.trim() || '';
      const pay = row.dataset.paymentStatus || '';
      const du = row.dataset.duStatus || '';

      excelData.push([reg, name, nisn, wa, jur, pay, du]);
    });

    const ws = XLSX.utils.aoa_to_sheet(excelData);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Terpilih');
    XLSX.writeFile(wb, `Daftar_Ulang_Terpilih_${new Date().toISOString().slice(0, 10)}.xlsx`);
  }

  // Perbarui state checkbox "Select All"
  function updateDuSelectAllState() {
    const tableBody = document.getElementById('du-table-body');
    const selectAll = document.getElementById('du-select-all');
    if (!tableBody || !selectAll) return;

    const checkboxes = Array.from(tableBody.querySelectorAll('[data-du-select]'));
    const visibleCheckboxes = checkboxes.filter(cb => {
      const row = cb.closest('tr');
      return row && row.style.display !== 'none' && !row.hidden;
    });
    const selectedVisibleCount = visibleCheckboxes.filter(cb => cb.checked).length;

    selectAll.checked = visibleCheckboxes.length > 0 && selectedVisibleCount === visibleCheckboxes.length;
    selectAll.indeterminate = selectedVisibleCount > 0 && selectedVisibleCount < visibleCheckboxes.length;
  }

  function openDaftarUlangEdit(regNumber) {
    const row = document.getElementById('du-row-' + regNumber);
    const modal = document.getElementById('du-edit-modal');
    if (!row || !modal) {
      return;
    }

    document.getElementById('du-edit-registration').textContent = regNumber;
    document.getElementById('du-edit-form').dataset.registration = regNumber;
    document.getElementById('du-edit-registration-status').value = row.dataset.duStatus || 'Menunggu';
    document.getElementById('du-edit-payment-status').value = row.dataset.paymentStatus || '';
    document.getElementById('du-edit-error').classList.add('hidden');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  function closeDaftarUlangEdit() {
    const modal = document.getElementById('du-edit-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }

  async function saveDaftarUlangEdit(event) {
    event.preventDefault();
    const form = event.currentTarget;
    const submitButton = document.getElementById('du-edit-submit');
    const errorMessage = document.getElementById('du-edit-error');
    const registrationNumber = form.dataset.registration;
    const row = document.getElementById('du-row-' + registrationNumber);
    const section = document.getElementById('section-daftar-ulang');
    const endpoint = section.dataset.statusEndpoint;

    submitButton.disabled = true;
    errorMessage.classList.add('hidden');

    try {
      const response = await fetch(endpoint, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': section.dataset.csrf,
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          no_registrasi: registrationNumber,
          status: document.getElementById('du-edit-registration-status').value,
          status_pembayaran_awal: document.getElementById('du-edit-payment-status').value
        })
      });
      const data = await response.json();
      if (!response.ok || !data.success) {
        const validationMessage = data.errors ? Object.values(data.errors).flat()[0] : data.message;
        throw new Error(validationMessage || 'Status gagal disimpan. Silakan coba lagi.');
      }

      row.dataset.duStatus = data.status;
      row.dataset.paymentStatus = data.status_pembayaran_awal || '';
      const paymentCell = document.getElementById('du-payment-' + registrationNumber);
      if (paymentCell) {
        paymentCell.innerHTML = getDuPaymentBadgeHtml(data.status_pembayaran_awal);
      }

      const dateCell = document.getElementById('du-tgl-' + registrationNumber);
      if (dateCell) {
        dateCell.innerHTML = getDuDateBadgeHtml(data.status, data.tgl_daftar_ulang);
      }

      // Sinkronkan data baris di modul Keuangan secara langsung bila ada di DOM
      const keuRow = document.getElementById('keu-row-' + registrationNumber) || document.querySelector(`.keu-data-row[data-registration="${registrationNumber}"]`);
      if (keuRow && (data.status_pembayaran_awal === 'Lunas' || data.status === 'Sudah Daftar Ulang')) {
        keuRow.setAttribute('data-status', 'Sah Daftar Ulang');
        if (data.status_pembayaran_awal === 'Lunas') {
          keuRow.setAttribute('data-baju', 'Siap Ambil Baju');
          const sisaCell = keuRow.querySelector('.keu-col-sisa');
          if (sisaCell) {
            sisaCell.innerHTML = '<span class="font-bold text-emerald-600 text-xs sm:text-sm">Rp 0</span><span class="text-[10px] font-semibold text-emerald-600">Lunas</span>';
          }
        }
      }

      if (data.log && typeof window.appendAdminActivityLog === 'function') {
        window.appendAdminActivityLog(data.log);
      }
      if (typeof updateDashboardKpis === 'function') {
        updateDashboardKpis();
      }

      closeDaftarUlangEdit();
      filterDaftarUlangTable();
    } catch (error) {
      errorMessage.textContent = error.message;
      errorMessage.classList.remove('hidden');
    } finally {
      submitButton.disabled = false;
    }
  }

  function confirmDuDelete(form) {
    const registrationNumber = form.dataset.registration;
    const ensureRedirectInputs = () => {
      if (!form.querySelector('input[name="redirect_to"]')) {
        const hiddenRedirect = document.createElement('input');
        hiddenRedirect.type = 'hidden';
        hiddenRedirect.name = 'redirect_to';
        hiddenRedirect.value = window.location.href;
        form.appendChild(hiddenRedirect);
      }
      if (!form.querySelector('input[name="halaman"]')) {
        const hiddenHalaman = document.createElement('input');
        hiddenHalaman.type = 'hidden';
        hiddenHalaman.name = 'halaman';
        hiddenHalaman.value = 'daftar-ulang';
        form.appendChild(hiddenHalaman);
      }
    };

    if (typeof window.openDeleteConfirmModal === 'function') {
      window.openDeleteConfirmModal({
        title: 'Hapus Data Pendaftar',
        message: `Hapus data pendaftaran <strong>#${registrationNumber}</strong> beserta seluruh berkas dan data verifikasinya? Tindakan ini permanen.`,
        confirmText: 'Ya, Hapus Data',
        onConfirm: () => {
          ensureRedirectInputs();
          form.submit();
        }
      });
      return false;
    }
    const confirmed = window.confirm(`Hapus data pendaftaran ${registrationNumber} beserta seluruh berkas dan verifikasinya secara permanen?`);
    if (confirmed) {
      ensureRedirectInputs();
      form.submit();
    }
    return false;
  }

  function escapeDuHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function getDuDateBadgeHtml(status, date = '') {
    if (status === 'Sudah Daftar Ulang') {
      const safeDate = escapeDuHtml(date || '-');
      return `<span data-export-value="${safeDate}" class="inline-flex h-8 w-max items-center justify-center rounded-full border border-emerald-200 bg-emerald-50 px-1 text-[11px] font-semibold leading-none text-emerald-700">${safeDate}</span>`;
    }

    return '<span data-export-value="Belum" class="inline-flex h-8 w-max items-center justify-center rounded-full border border-amber-200 bg-amber-50 px-1 text-[11px] font-semibold leading-none text-amber-700">Belum</span>';
  }

  function formatDuShortDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return dateStr;
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const day = String(d.getDate()).padStart(2, '0');
    return `${day} ${months[d.getMonth()]} ${d.getFullYear()}`;
  }

  // Sinkronisasi realtime daftar ulang saat status verifikasi berubah
  window.syncDaftarUlangRealtime = function(item, status) {
    if (!item) return;
    const noReg = String(item.no_registrasi || '');
    if (!noReg) return;

    const row = document.getElementById('du-row-' + noReg);
    const tableBody = document.getElementById('du-table-body');
    const emptyRow = document.getElementById('du-empty-row');

    if (status !== 'Diterima') {
      // Jika statusnya BUKAN Diterima (misal Ditolak, Menunggu Verifikasi, Perbaikan Berkas):
      // Wajib langsung hilang dari tabel daftar ulang!
      if (row) {
        row.remove();
      }
    } else {
      // Jika status Diterima (terverifikasi penuh):
      // Wajib langsung masuk ke tabel daftar ulang jika belum ada
      if (!row && tableBody) {
        const cs = item.calon_siswa || item.calonSiswa || {};
        const nama = cs.nama_lengkap || '-';
        const nisn = cs.nisn || '-';
        const wa = cs.no_whatsapp || '';
        const cleanWa = wa ? wa.replace(/^0/, '62').replace(/[^0-9]/g, '') : '';
        const sekolah = cs.asal_sekolah || '-';
        const paymentStatus = item.status_pembayaran_awal || item.status_pembayaran || '';
        const jur = item.jurusan || {};
        const namaJur = jur.nama_jurusan || '-';
        const shortJur = /kecantikan/i.test(namaJur) ? 'TKC' : (namaJur.split(' ')[0] || '-');
        const tglDaftar = item.tgl_daftar ? formatDuShortDate(item.tgl_daftar) : getTodayFormattedDate();
        const tglVerif = (item.verifikasi && item.verifikasi.tgl_verifikasi) ? formatDuShortDate(item.verifikasi.tgl_verifikasi) : getTodayFormattedDate();
        const deleteEndpoint = document.getElementById('section-daftar-ulang').dataset.deleteEndpoint
          .replace('__NO_REGISTRASI__', encodeURIComponent(noReg));
        const csrfToken = document.getElementById('section-daftar-ulang').dataset.csrf;

        const tr = document.createElement('tr');
        tr.id = 'du-row-' + noReg;
        tr.setAttribute('data-registration', noReg);
        tr.setAttribute('data-du-status', 'Menunggu');
        tr.setAttribute('data-payment-status', item.status_pembayaran_awal || item.status_pembayaran || '');
        tr.setAttribute('data-search-text', `${noReg} ${nama} ${sekolah}`.toLocaleLowerCase('id-ID'));
        tr.className = 'hover:bg-blue-50/30 transition-colors text-slate-700 du-data-row';

        tr.innerHTML = `
          <td class="w-12 px-3 py-3.5 text-center">
            <input
              type="checkbox"
              name="no_registrasi[]"
              value="${noReg}"
              data-du-select
              aria-label="Pilih siswa ${noReg}"
              class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500 cursor-pointer"
            />
          </td>
          <td class="px-4 py-3.5 whitespace-nowrap">
            <button
              type="button"
              onclick="openReviewDrawer('${noReg}')"
              class="font-mono font-bold text-blue-600 hover:text-blue-800 hover:underline text-[13.5px] cursor-pointer inline-block"
              title="Lihat Detail ${noReg}"
            >
              ${noReg}
            </button>
            <span class="block text-[11px] text-slate-400 font-normal mt-0.5">${tglDaftar}</span>
          </td>
          <td class="px-4 py-3.5 whitespace-nowrap text-slate-700 font-mono text-[12px]">${tglVerif}</td>
          <td id="du-tgl-${noReg}" class="px-4 py-3.5 whitespace-nowrap font-mono text-[12px] du-col-tgl-du text-center">${getDuDateBadgeHtml('Menunggu')}</td>
          <td class="px-4 py-3.5">
            <strong class="font-bold text-slate-900 text-[13.5px] break-words">${escapeDuHtml(nama)}</strong>
          </td>
          <td class="px-4 py-3.5 whitespace-nowrap font-mono text-slate-700 text-[12.5px]">
            ${escapeDuHtml(nisn)}
          </td>
          <td class="px-4 py-3.5 whitespace-nowrap text-slate-700 font-medium font-mono text-[12.5px]">
            ${wa ? `<a href="https://wa.me/${cleanWa}" target="_blank" rel="noopener noreferrer" class="hover:text-blue-600 hover:underline" title="Hubungi Siswa via WhatsApp">${escapeDuHtml(wa)}</a>` : '<span class="text-slate-400">-</span>'}
          </td>
          <td id="du-payment-${escapeDuHtml(noReg)}" class="px-4 py-3.5 whitespace-nowrap text-center">${getDuPaymentBadgeHtml(paymentStatus)}</td>
          <td class="px-4 py-3.5 whitespace-nowrap">
            <span title="${escapeDuHtml(namaJur)}" class="inline-block px-2.5 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-200/70 font-bold text-xs">
              ${escapeDuHtml(shortJur)}
            </span>
          </td>
          <td hidden>${tglDaftar}</td>
          <td hidden>${escapeDuHtml(item.status_daftar_ulang || 'Menunggu')}</td>
          <td class="px-4 py-3 text-center whitespace-nowrap">
            <div class="inline-flex items-center justify-center gap-1.5">
              <button type="button" data-du-edit="${escapeDuHtml(noReg)}" title="Edit status ${escapeDuHtml(noReg)}" aria-label="Edit status daftar ulang dan pembayaran ${escapeDuHtml(nama)}" class="w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-600 hover:text-blue-600 hover:bg-blue-50 hover:border-blue-200 transition inline-flex items-center justify-center cursor-pointer active:scale-95">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"></path><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"></path></svg>
              </button>
              <form action="${escapeDuHtml(deleteEndpoint)}" method="POST" data-registration="${escapeDuHtml(noReg)}" onsubmit="return confirmDuDelete(this)" class="inline-block">
                <input type="hidden" name="_token" value="${escapeDuHtml(csrfToken)}">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="halaman" value="daftar-ulang">
                <input type="hidden" name="redirect_to" value="{{ route('admin.daftar-ulang') }}">
                <button type="submit" title="Hapus pendaftar ${escapeDuHtml(noReg)}" aria-label="Hapus pendaftar ${escapeDuHtml(nama)}" class="w-9 h-9 rounded-lg border border-rose-200 bg-white text-rose-600 hover:bg-rose-50 hover:border-rose-300 transition inline-flex items-center justify-center cursor-pointer active:scale-95">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M10 11v6m4-6v6M6 7l1 13h10l1-13M9 7V4h6v3"></path></svg>
                </button>
              </form>
            </div>
          </td>
        `;

        if (emptyRow) {
          tableBody.insertBefore(tr, emptyRow);
        } else {
          tableBody.prepend(tr);
        }
      }
    }

    if (typeof updateDashboardKpis === 'function') {
      updateDashboardKpis();
    } 
    filterDaftarUlangTable();
  };

  // Terapkan filter awal saat halaman dimuat
  document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('du-search-input');
    if (searchInput) {
      searchInput.addEventListener('input', filterDaftarUlangTable);
    }

    filterDaftarUlangTable();

    // Inisialisasi checkbox Select All
    const selectAll = document.getElementById('du-select-all');
    const tableBody = document.getElementById('du-table-body');

    if (selectAll && tableBody) {
      tableBody.addEventListener('click', function(event) {
        const editButton = event.target.closest('[data-du-edit]');
        if (editButton) {
          openDaftarUlangEdit(editButton.dataset.duEdit);
        }
      });

      selectAll.addEventListener('change', function() {
        const checkboxes = Array.from(tableBody.querySelectorAll('[data-du-select]'));
        checkboxes.forEach(cb => {
          const row = cb.closest('tr');
          if (row && row.style.display !== 'none' && !row.hidden) {
            cb.checked = selectAll.checked;
          }
        });
        updateDuSelectedBadge();
      });

      tableBody.addEventListener('change', function(e) {
        if (e.target && e.target.hasAttribute('data-du-select')) {
          updateDuSelectAllState();
          updateDuSelectedBadge();
        }
      });
    }

    const editForm = document.getElementById('du-edit-form');
    const editModal = document.getElementById('du-edit-modal');
    const cancelButton = document.getElementById('du-edit-cancel');
    const paymentSelect = document.getElementById('du-edit-payment-status');
    const regStatusSelect = document.getElementById('du-edit-registration-status');
    if (paymentSelect && regStatusSelect) {
      paymentSelect.addEventListener('change', () => {
        if (paymentSelect.value === 'Lunas') {
          regStatusSelect.value = 'Sudah Daftar Ulang';
        }
      });
    }
    if (editForm) {
      editForm.addEventListener('submit', saveDaftarUlangEdit);
    }
    if (cancelButton) {
      cancelButton.addEventListener('click', closeDaftarUlangEdit);
    }
    if (editModal) {
      editModal.addEventListener('click', event => {
        if (event.target === editModal) {
          closeDaftarUlangEdit();
        }
      });
    }
    document.addEventListener('keydown', event => {
      if (event.key === 'Escape' && editModal && !editModal.classList.contains('hidden')) {
        closeDaftarUlangEdit();
      }
    });
  });
</script>
