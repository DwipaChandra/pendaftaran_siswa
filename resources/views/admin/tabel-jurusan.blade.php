<!-- ============================================================== -->
<!-- 4. HALAMAN TABEL JURUSAN (PROGRAM KEAHLIAN)                    -->
<!-- ============================================================== -->
<section
  id="section-tabel-jurusan"
  data-registration-fingerprint="{{ $registrationTableFingerprint }}"
  class="bg-white rounded-[14px] border border-slate-200/90 no-print transition-all {{ ($activePage ?? 'status-berkas') !== 'tabel-jurusan' ? 'hidden' : '' }}"
>
  <!-- Header Tabel Jurusan -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 sm:p-6 border-b border-slate-200/80">
    <div class="flex items-center gap-3.5">
      <div class="w-11 h-11 rounded-[10px] bg-blue-50 text-blue-600 border border-blue-100/90 flex items-center justify-center shrink-0">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <circle cx="9" cy="8" r="3.5"></circle>
          <path d="M2.5 20c0-3.5 3-6 6.5-6s6.5 2.5 6.5 6M16 4.5a3.5 3.5 0 0 1 0 7M18 14.5c2.2.7 3.5 2.7 3.5 5.5"></path>
        </svg>
      </div>
      <div>
        <h2 class="text-lg sm:text-[20px] font-bold text-slate-900 tracking-tight leading-tight">Tabel Pendaftar Jurusan</h2>
        <p class="text-[13.5px] text-slate-500 mt-0.5">Cari pendaftar lintas jurusan, saring berkas, lalu tinjau.</p>
      </div>
    </div>

    <div class="flex items-center gap-3 self-start sm:self-auto flex-wrap">
      <span class="text-xs font-medium text-slate-500 bg-slate-50 px-3 py-2 rounded-lg border border-slate-200/80">
        Terpilih: <strong id="admin-table-selected-badge" class="font-bold text-slate-800">0</strong> Siswa
      </span>
      <button
        type="button"
        onclick="exportToExcel()"
        title="Ekspor Data Pendaftar (Format Excel)"
        class="h-10 px-4 rounded-lg bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold text-sm inline-flex items-center gap-2 transition cursor-pointer active:scale-[0.98]"
      >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M12 3v12m0 0-4-4m4 4 4-4M4 20h16"></path>
        </svg>
        <span>Ekspor Excel</span>
      </button>
    </div>
  </div>

  @php
    $activeJurusan = request('jurusan', '');
    $activeJalur = request('jalur', '');
    $activeGelombang = request('gelombang', '');
    $activeStatus = request('status', '');

    $activeFilterCount = 0;
    if ($activeJurusan !== '') $activeFilterCount++;
    if ($activeJalur !== '') $activeFilterCount++;
    if ($activeGelombang !== '') $activeFilterCount++;
    if ($activeStatus !== '') $activeFilterCount++;

    $activeJurusanLabel = '';
    if ($activeJurusan !== '') {
        $foundJur = $jurusanList->firstWhere('id_jurusan', $activeJurusan);
        if ($foundJur) {
            $activeJurusanLabel = str_contains(strtoupper($foundJur->nama_jurusan), 'KECANTIKAN') ? 'TKC' : explode(' ', $foundJur->nama_jurusan, 2)[0];
        }
    }

    $activeJalurLabel = '';
    if ($activeJalur !== '') {
        $foundJalur = $jalurList->firstWhere('id_jalur', $activeJalur);
        if ($foundJalur) {
            $activeJalurLabel = $foundJalur->nama_jalur;
        }
    }

    $activeGelombangLabel = '';
    if ($activeGelombang !== '') {
        $foundGel = $gelombangList->firstWhere('id_gelombang', $activeGelombang);
        if ($foundGel) {
            $activeGelombangLabel = $foundGel->nama_gelombang;
        }
    }

    $activeStatusLabel = $activeStatus !== '' ? $activeStatus : '';
    $currentOrder = request('urutan_registrasi', 'terbaru');
    if ($currentOrder !== 'terlama') {
        $currentOrder = 'terbaru';
    }
  @endphp

  <!-- Filter Toolbar Form -->
  <form
    id="admin-filter-form"
    method="GET"
    action="{{ route('admin.dashboard') }}"
    class="flex flex-wrap items-center gap-2.5 px-6 py-4 border-b border-slate-200/80 bg-white"
  >
    <input type="hidden" name="halaman" value="tabel-jurusan" />
    <input type="hidden" name="jurusan" id="filter-input-jurusan" value="{{ $activeJurusan }}" />
    <input type="hidden" name="jalur" id="filter-input-jalur" value="{{ $activeJalur }}" />
    <input type="hidden" name="gelombang" id="filter-input-gelombang" value="{{ $activeGelombang }}" />
    <input type="hidden" name="status" id="filter-input-status" value="{{ $activeStatus }}" />
    <select name="urutan_registrasi_select" onchange="submitAdminFilter(this)" class="hidden" aria-hidden="true">
      <option value="">No. Registrasi</option>
      <option value="terbaru">Terbaru</option>
      <option value="terlama">Terlama</option>
    </select>

    <!-- Toggle Urutan (Terbaru / Terlama) ditaruh di paling kiri dengan penanda tooltip custom -->
    <input type="hidden" name="urutan_registrasi" id="filter-input-urutan" value="{{ $currentOrder }}" />
    <div
      class="inline-flex items-center p-1 bg-slate-100 rounded-lg border border-slate-200/90 h-10 select-none shrink-0 gap-0.5"
      role="group"
      aria-label="Urutan Pendaftar"
    >
      <!-- Tombol Urutan Terbaru (Desc) -->
      <div class="relative group/sort flex items-center">
        <button
          type="button"
          id="sort-btn-terbaru"
          onclick="setRegistrationOrder('terbaru')"
          aria-label="Urutkan Terbaru"
          class="sort-toggle-btn h-8 w-8 inline-flex items-center justify-center rounded-md transition-all cursor-pointer {{ $currentOrder !== 'terlama' ? 'bg-white text-blue-600 font-bold' : 'text-slate-400 hover:text-slate-700 hover:bg-white/60' }}"
        >
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="m3 16 4 4 4-4"></path>
            <path d="M7 20V4"></path>
            <path d="M11 4h10"></path>
            <path d="M11 9h7"></path>
            <path d="M11 14h4"></path>
          </svg>
        </button>

        <!-- Penanda Tooltip Urutkan Terbaru (Sesuai Desain Tooltip di Navbar) -->
        <div
          role="tooltip"
          class="pointer-events-none absolute left-0 top-full mt-2.5 px-2.5 py-1.5 bg-slate-900 text-white text-xs font-semibold rounded-lg shadow-xl border border-slate-700 opacity-0 invisible group-hover/sort:opacity-100 group-hover/sort:visible transition-all duration-150 whitespace-nowrap z-50 flex items-center"
        >
          <span>Urutkan Terbaru</span>
          <div class="absolute -top-1 left-3 w-2 h-2 bg-slate-900 border-l border-t border-slate-700 rotate-45"></div>
        </div>
      </div>

      <!-- Tombol Urutan Terlama (Asc) -->
      <div class="relative group/sort flex items-center">
        <button
          type="button"
          id="sort-btn-terlama"
          onclick="setRegistrationOrder('terlama')"
          aria-label="Urutkan Terlama"
          class="sort-toggle-btn h-8 w-8 inline-flex items-center justify-center rounded-md transition-all cursor-pointer {{ $currentOrder === 'terlama' ? 'bg-white text-blue-600 font-bold' : 'text-slate-400 hover:text-slate-700 hover:bg-white/60' }}"
        >
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="m3 8 4-4 4 4"></path>
            <path d="M7 4v16"></path>
            <path d="M11 4h4"></path>
            <path d="M11 9h7"></path>
            <path d="M11 14h10"></path>
          </svg>
        </button>

        <!-- Penanda Tooltip Urutkan Terlama (Sesuai Desain Tooltip di Navbar) -->
        <div
          role="tooltip"
          class="pointer-events-none absolute left-0 top-full mt-2.5 px-2.5 py-1.5 bg-slate-900 text-white text-xs font-semibold rounded-lg shadow-xl border border-slate-700 opacity-0 invisible group-hover/sort:opacity-100 group-hover/sort:visible transition-all duration-150 whitespace-nowrap z-50 flex items-center"
        >
          <span>Urutkan Terlama</span>
          <div class="absolute -top-1 left-3 w-2 h-2 bg-slate-900 border-l border-t border-slate-700 rotate-45"></div>
        </div>
      </div>
    </div>

    <!-- Search Box (flex-1 agar memanjang penuh setelah toggle) -->
    <div class="relative flex-1 min-w-[240px]">
      <input
        type="search"
        name="q"
        value="{{ request('q') }}"
        placeholder="Cari no. registrasi, nama, atau asal SMP"
        class="w-full h-10 text-sm rounded-lg border border-slate-200/90 bg-white pl-10 pr-3.5 py-2 text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition"
      />
      <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <circle cx="11" cy="11" r="7"></circle>
        <path d="m20 20-3.5-3.5"></path>
      </svg>
    </div>

    <!-- Tombol Utama Filter & Flyout Nested Dropdown -->
    <div class="relative" id="filter-dropdown-container">
      <button
        type="button"
        id="filter-trigger-btn"
        aria-expanded="false"
        aria-haspopup="true"
        class="h-10 px-3.5 rounded-lg border border-slate-200/90 bg-white hover:bg-slate-50 text-slate-700 font-medium text-sm inline-flex items-center gap-2 transition cursor-pointer select-none group {{ $activeFilterCount > 0 ? 'border-blue-300 text-blue-700 bg-blue-50/40' : '' }}"
      >
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-slate-500 group-hover:text-blue-600 transition-colors {{ $activeFilterCount > 0 ? 'text-blue-600' : '' }}">
          <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
        </svg>
        <span>Filter</span>
        <span
          id="filter-badge-count"
          class="px-1.5 py-0.5 text-[11px] font-bold rounded-full bg-blue-600 text-white leading-none {{ $activeFilterCount > 0 ? '' : 'hidden' }}"
        >
          {{ $activeFilterCount }}
        </span>
        <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition-transform duration-150" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="m6 9 6 6 6-6"></path>
        </svg>
      </button>

      @php
        $filterCategories = [
          [
            'key' => 'jurusan',
            'label' => 'Jurusan',
            'activeValue' => $activeJurusan,
            'activeLabel' => $activeJurusanLabel,
            'flyoutClasses' => 'top-0 min-w-[220px] max-w-[270px]',
            'header' => 'Pilih Jurusan',
            'allLabel' => 'Semua Jurusan',
            'options' => $jurusanList->map(fn ($jur) => [
              'value' => (string) $jur->id_jurusan,
              'label' => $jur->nama_jurusan,
              'short' => str_contains(strtoupper($jur->nama_jurusan), 'KECANTIKAN') ? 'TKC' : explode(' ', $jur->nama_jurusan, 2)[0],
              'dotColor' => null,
            ])->all(),
          ],
          [
            'key' => 'jalur',
            'label' => 'Jalur',
            'activeValue' => $activeJalur,
            'activeLabel' => $activeJalurLabel,
            'flyoutClasses' => 'top-0 min-w-[210px] max-w-[260px]',
            'header' => 'Pilih Jalur',
            'allLabel' => 'Semua Jalur',
            'options' => $jalurList->map(fn ($jalur) => [
              'value' => (string) $jalur->id_jalur,
              'label' => $jalur->nama_jalur,
              'short' => $jalur->nama_jalur,
              'dotColor' => null,
            ])->all(),
          ],
          [
            'key' => 'gelombang',
            'label' => 'Gelombang',
            'activeValue' => $activeGelombang,
            'activeLabel' => $activeGelombangLabel,
            'flyoutClasses' => 'bottom-0 min-w-[210px] max-w-[260px]',
            'header' => 'Pilih Gelombang',
            'allLabel' => 'Semua Gelombang',
            'options' => $gelombangList->map(fn ($gelombang) => [
              'value' => (string) $gelombang->id_gelombang,
              'label' => $gelombang->nama_gelombang,
              'short' => $gelombang->nama_gelombang,
              'dotColor' => null,
            ])->all(),
          ],
          [
            'key' => 'status',
            'label' => 'Status',
            'activeValue' => $activeStatus,
            'activeLabel' => $activeStatusLabel,
            'flyoutClasses' => 'bottom-0 min-w-[210px] max-w-[260px]',
            'header' => 'Pilih Status Berkas',
            'allLabel' => 'Semua Status',
            'options' => [
              ['value' => 'Menunggu Verifikasi', 'label' => 'Menunggu Verifikasi', 'short' => 'Menunggu', 'dotColor' => 'bg-blue-500'],
              ['value' => 'Diterima', 'label' => 'Terverifikasi', 'short' => 'Terverifikasi', 'dotColor' => 'bg-emerald-500'],
              ['value' => 'Perbaikan Berkas', 'label' => 'Perbaikan Berkas', 'short' => 'Perbaikan', 'dotColor' => 'bg-amber-500'],
              ['value' => 'Ditolak', 'label' => 'Ditolak', 'short' => 'Ditolak', 'dotColor' => 'bg-rose-500'],
            ],
          ],
        ];
      @endphp

      <!-- Dropdown Level 1 -->
      <div
        id="filter-menu-level1"
        class="hidden absolute top-full mt-2 right-0 w-60 bg-white rounded-xl border border-slate-200/90 shadow-xl p-2 z-40 select-none transition-all duration-150 origin-top-right"
      >
        <div class="space-y-1">
          @foreach ($filterCategories as $cat)
            <div class="filter-category-item relative group/item" data-cat="{{ $cat['key'] }}">
              <button
                type="button"
                class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-[#F3F4F6] hover:text-slate-900 transition-colors cursor-pointer text-left"
              >
                <div class="flex items-center gap-2 min-w-0">
                  <span class="truncate">{{ $cat['label'] }}</span>
                  <span class="cat-active-dot w-2 h-2 rounded-full bg-blue-600 shrink-0 {{ $cat['activeValue'] !== '' ? '' : 'hidden' }}"></span>
                  <span class="cat-active-text text-[11px] font-semibold text-blue-600 truncate max-w-[70px] {{ $cat['activeValue'] !== '' ? '' : 'hidden' }}">
                    {{ $cat['activeLabel'] }}
                  </span>
                </div>
                <svg class="w-4 h-4 text-slate-400 group-hover/item:text-slate-600 group-hover/item:translate-x-0.5 transition shrink-0 ml-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="m9 18 6-6-6-6"></path>
                </svg>
              </button>

              <!-- Sub-Menu Melayang Level 2 -->
              <div
                class="filter-flyout hidden group-hover/item:block absolute right-full mr-2 {{ $cat['flyoutClasses'] }} bg-white rounded-xl border border-slate-200/90 shadow-xl p-1.5 z-50"
              >
                <div class="px-2.5 py-1 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 mb-1">
                  {{ $cat['header'] }}
                </div>
                <button
                  type="button"
                  data-filter-category="{{ $cat['key'] }}"
                  data-filter-value=""
                  data-filter-label=""
                  onclick="selectFilterOption('{{ $cat['key'] }}', '', '', event)"
                  class="filter-option-btn w-full flex items-center justify-between px-2.5 py-2 rounded-lg text-xs transition-colors cursor-pointer text-left {{ $cat['activeValue'] === '' ? 'font-bold text-blue-700 bg-blue-50/70' : 'font-medium text-slate-700 hover:bg-[#F3F4F6] hover:text-slate-900' }}"
                >
                  <span class="truncate">{{ $cat['allLabel'] }}</span>
                  <svg class="cat-check-icon w-4 h-4 text-blue-600 shrink-0 ml-2 {{ $cat['activeValue'] === '' ? '' : 'hidden' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                  </svg>
                </button>

                @foreach ($cat['options'] as $opt)
                  @php
                    $isSelected = (string) $cat['activeValue'] === (string) $opt['value'];
                  @endphp
                  <button
                    type="button"
                    data-filter-category="{{ $cat['key'] }}"
                    data-filter-value="{{ $opt['value'] }}"
                    data-filter-label="{{ $opt['short'] }}"
                    onclick="selectFilterOption('{{ $cat['key'] }}', '{{ $opt['value'] }}', '{{ $opt['short'] }}', event)"
                    class="filter-option-btn w-full flex items-center justify-between px-2.5 py-2 rounded-lg text-xs transition-colors cursor-pointer text-left {{ $isSelected ? 'font-bold text-blue-700 bg-blue-50/70' : 'font-medium text-slate-700 hover:bg-[#F3F4F6] hover:text-slate-900' }}"
                  >
                    @if ($opt['dotColor'])
                      <div class="flex items-center gap-2 truncate">
                        <span class="w-1.5 h-1.5 rounded-full {{ $opt['dotColor'] }} shrink-0"></span>
                        <span class="truncate">{{ $opt['label'] }}</span>
                      </div>
                    @else
                      <span class="truncate" title="{{ $opt['label'] }}">{{ $opt['label'] }}</span>
                    @endif
                    <svg class="cat-check-icon w-4 h-4 text-blue-600 shrink-0 ml-2 {{ $isSelected ? '' : 'hidden' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                      <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                  </button>
                @endforeach
              </div>
            </div>
          @endforeach
        </div>

        <!-- Tombol Reset Filter di Bagian Bawah (jika ada filter aktif) -->
        <div id="filter-reset-divider" class="border-t border-slate-100 my-1.5 {{ $activeFilterCount > 0 ? '' : 'hidden' }}"></div>
        <button
          type="button"
          id="filter-reset-btn"
          onclick="resetAllFilters(event)"
          class="w-full items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition cursor-pointer select-none {{ $activeFilterCount > 0 ? 'flex' : 'hidden' }}"
        >
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
            <path d="M3 3v5h5"></path>
          </svg>
          <span>Reset Filter</span>
        </button>
      </div>
    </div>
  </form>

  <form
    id="bulk-delete-form"
    class="hidden"
    method="POST"
    action="{{ route('admin.pendaftaran.destroy-selected') }}"
    onsubmit="return confirmBulkDelete(event)"
  >
    @csrf
    <input type="hidden" name="halaman" value="tabel-jurusan" />
    <input type="hidden" name="redirect_to" value="{{ route('admin.tabel-jurusan') }}" />
  </form>

  <!-- Contextual Bulk Action Bar -->
  <div id="admin-bulk-action-bar" class="hidden items-center justify-between gap-3 px-5 py-3 bg-blue-50/90 border-b border-blue-200 transition-all duration-200">
    <div class="flex items-center gap-2">
      <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white font-bold text-xs" id="admin-bulk-selected-count">0</span>
      <span class="text-xs font-bold text-blue-900">Siswa Terpilih</span>
      <span class="text-xs text-blue-700 hidden sm:inline">• Tindakan serentak:</span>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
      <button
        type="button"
        onclick="exportToExcel()"
        class="h-8 px-3 rounded-lg bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 font-bold text-xs inline-flex items-center gap-1.5 transition active:scale-95 cursor-pointer"
      >
        <svg class="w-3.5 h-3.5 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v12m0 0-4-4m4 4 4-4M4 20h16"></path></svg>
        <span>Ekspor Excel</span>
      </button>
      <button
        type="button"
        id="admin-bulk-delete-btn"
        onclick="triggerBulkDelete()"
        class="h-8 px-3 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 font-bold text-xs inline-flex items-center gap-1.5 transition active:scale-95 cursor-pointer"
      >
        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"></path><path d="M8 6V4h8v2"></path><path d="m19 6-1 14H6L5 6"></path><path d="M10 11v5"></path><path d="M14 11v5"></path></svg>
        <span id="admin-bulk-delete-btn-text">Hapus Terpilih</span>
      </button>
      <button
        type="button"
        onclick="clearApplicantSelections()"
        class="h-8 px-2.5 rounded-lg text-slate-500 hover:text-slate-700 text-xs font-semibold cursor-pointer"
        title="Batalkan pilihan"
      >
        Batal
      </button>
    </div>
  </div>

  <!-- Table -->
  <div class="overflow-x-auto">
    <table id="admin-applicant-table" class="w-full min-w-[1020px] table-fixed text-sm text-left border-collapse">
      <thead>
        <tr class="bg-slate-50/90 text-slate-600 border-b border-slate-200/80 uppercase tracking-wider text-[11px] font-bold whitespace-nowrap">
          <th scope="col" data-export-ignore class="w-12 px-3 py-3.5 text-center">
            <input
              id="select-all-applicants"
              type="checkbox"
              aria-label="Pilih semua pendaftar"
              class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500 cursor-pointer"
            />
          </th>
          <th scope="col" data-export-path="no_registrasi" class="w-[12%] px-4 py-3.5 text-left">No. Registrasi</th>
          <th scope="col" data-export-path="calon_siswa.nama_lengkap" class="w-[15%] px-4 py-3.5 text-left">Nama</th>
          <th scope="col" data-export-path="calon_siswa.nisn" data-export-fallback-path="calon_siswa.user.email" data-export-format="email-prefix" class="w-[10%] px-4 py-3.5 text-left">NISN</th>
          <th scope="col" data-export-path="calon_siswa.no_whatsapp" class="w-[12%] px-4 py-3.5 text-left">WhatsApp</th>
          <th scope="col" data-export-path="calon_siswa.asal_sekolah" class="w-[13%] px-4 py-3.5 text-left">Asal Sekolah</th>
          <th scope="col" data-export-path="jurusan.nama_jurusan" class="w-[9%] px-4 py-3.5 text-left">Jurusan</th>
          <th scope="col" data-export-paths="jalur.nama_jalur,gelombang.nama_gelombang" class="w-[14%] px-4 py-3.5 text-left">Jalur & Gelombang</th>
          <th scope="col" data-export-path="status_berkas" data-export-format="status-berkas" class="w-[12%] px-4 py-3.5 text-left">Status Berkas</th>
          <th scope="col" data-export-ignore class="w-[10%] px-4 py-3.5 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody
        id="admin-table-body"
        class="divide-y divide-slate-100"
      >
        @foreach ($pendaftaranPaginated as $item)
          <tr
            id="applicant-row-{{ $item->no_registrasi }}"
            data-registration="{{ $item->no_registrasi }}"
            data-total-paid="{{ (int) ($pendaftaranBilling[$item->no_registrasi]['total_sudah_dibayar'] ?? 0) }}"
            class="hover:bg-slate-50/80 transition-colors text-slate-700"
          >
            <td class="w-12 px-3 py-3.5 text-center">
              <input
                type="checkbox"
                value="{{ $item->no_registrasi }}"
                data-total-paid="{{ (int) ($pendaftaranBilling[$item->no_registrasi]['total_sudah_dibayar'] ?? 0) }}"
                data-applicant-select
                aria-label="Pilih pendaftar {{ $item->no_registrasi }}"
                class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500 cursor-pointer"
              />
            </td>
            <td class="px-4 py-3.5 whitespace-nowrap">
              <button
                type="button"
                data-cell="no_registrasi"
                onclick="openReviewDrawer('{{ $item->no_registrasi }}')"
                class="font-mono font-bold text-blue-600 hover:text-blue-800 hover:underline text-[14px] cursor-pointer inline-block"
                title="Buka detail {{ $item->no_registrasi }}"
              >
                {{ $item->no_registrasi }}
              </button>
              <span data-cell="tgl_daftar" class="block text-[12.5px] text-slate-500 font-normal mt-0.5 whitespace-nowrap">
                {{ date('d M Y', strtotime($item->tgl_daftar)) }}
              </span>
            </td>
            <td data-cell="nama" class="px-4 py-3.5">
              <strong class="font-bold text-slate-900 text-[14px] break-words">
                {{ $item->calonSiswa->nama_lengkap ?? '-' }}
              </strong>
            </td>
            <td data-cell="nisn" class="px-4 py-3.5 whitespace-nowrap font-mono text-slate-700 text-[13px]">
              {{ $item->calonSiswa?->nisn ?: ($item->calonSiswa?->user?->email ? explode('@', $item->calonSiswa->user->email, 2)[0] : '-') }}
            </td>
            <td data-cell="no_whatsapp" class="px-4 py-3.5 text-slate-700 text-[14px] whitespace-nowrap">
              @php
                $whatsappNumber = preg_replace('/\D+/', '', (string) ($item->calonSiswa?->no_whatsapp ?? ''));
                if (str_starts_with($whatsappNumber, '0')) {
                    $whatsappNumber = '62'.substr($whatsappNumber, 1);
                } elseif (str_starts_with($whatsappNumber, '8')) {
                    $whatsappNumber = '62'.$whatsappNumber;
                }
              @endphp
              @if ($whatsappNumber !== '')
                <a
                  href="https://wa.me/{{ $whatsappNumber }}"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="hover:text-blue-600 hover:underline"
                  title="Hubungi Siswa via WhatsApp"
                >{{ $item->calonSiswa?->no_whatsapp }}</a>
              @else
                -
              @endif
            </td>
            <td data-cell="asal_sekolah" class="px-4 py-3.5 text-slate-700 text-[14px] break-words">
              {{ $item->calonSiswa->asal_sekolah ?? '-' }}
            </td>
            <td class="px-4 py-3.5 whitespace-nowrap">
              <span
                data-cell="jurusan"
                title="{{ $item->jurusan->nama_jurusan ?? '-' }}"
                class="inline-block px-2.5 py-0.5 rounded-md bg-blue-50/80 text-blue-700 border border-blue-200/60 font-bold text-xs"
              >
                {{ str_contains(strtoupper($item->jurusan->nama_jurusan ?? ''), 'KECANTIKAN') ? 'TKC' : explode(' ', $item->jurusan->nama_jurusan ?? '-', 2)[0] }}
              </span>
            </td>
            <td data-cell="jalur_gelombang" class="px-4 py-3.5">
              <span class="text-slate-800 text-[14px] block">{{ $item->jalur->nama_jalur ?? '-' }}</span>
              <span class="block text-[12.5px] text-slate-500 font-normal mt-0.5">{{ $item->gelombang->nama_gelombang ?? '-' }}</span>
            </td>
            <td data-cell="status_berkas" class="px-4 py-3.5 whitespace-nowrap">
              @if ($item->status_berkas === 'Diterima')
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                  Terverifikasi
                </span>
              @elseif ($item->status_berkas === 'Ditolak')
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200/80">
                  <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>
                  Ditolak
                </span>
              @elseif ($item->status_berkas === 'Perbaikan Berkas')
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200/80">
                  <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>
                  Perbaikan
                </span>
              @else
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200/80">
                  <span class="w-1.5 h-1.5 rounded-full bg-blue-500 shrink-0"></span>
                  Menunggu
                </span>
              @endif
            </td>
            <td data-cell="aksi" class="px-4 py-3.5 text-right whitespace-nowrap">
              <div class="inline-flex items-center justify-end gap-1.5">
                <button
                  type="button"
                  onclick="openReviewDrawer('{{ $item->no_registrasi }}')"
                  title="Lihat detail {{ $item->no_registrasi }}"
                  aria-label="Lihat detail {{ $item->calonSiswa->nama_lengkap ?? $item->no_registrasi }}"
                  class="w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-600 hover:text-blue-600 hover:bg-blue-50 hover:border-blue-200 transition inline-flex items-center justify-center cursor-pointer active:scale-95"
                >
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                </button>
                <form
                  action="{{ route('admin.pendaftaran.destroy', ['no_registrasi' => $item->no_registrasi]) }}"
                  method="POST"
                  data-registration="{{ $item->no_registrasi }}"
                  onsubmit="return confirmDeleteRegistration(this)"
                  class="inline-block"
                >
                  @csrf
                  @method('DELETE')
                  <input type="hidden" name="halaman" value="tabel-jurusan" />
                  <input type="hidden" name="redirect_to" value="{{ route('admin.tabel-jurusan') }}" />
                  <button
                    type="submit"
                    title="Hapus pendaftar {{ $item->no_registrasi }}"
                    aria-label="Hapus pendaftar {{ $item->calonSiswa->nama_lengkap ?? $item->no_registrasi }}"
                    class="w-9 h-9 rounded-lg border border-rose-200 bg-white text-rose-600 hover:bg-rose-50 hover:border-rose-300 transition inline-flex items-center justify-center cursor-pointer active:scale-95"
                  >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M10 11v6m4-6v6M6 7l1 13h10l1-13M9 7V4h6v3"></path></svg>
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @endforeach
        <tr id="admin-search-empty-state" @if ($pendaftaranPaginated->isNotEmpty()) hidden @endif>
          <td colspan="10" class="py-12 text-center text-slate-400 text-sm">
            Belum ada data pendaftar yang sesuai kriteria pencarian.
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <div
    class="flex flex-wrap items-center justify-between gap-3 px-6 py-3.5 bg-white border-t border-slate-200/90 text-xs sm:text-[13px] text-slate-500"
  >
    <span id="admin-table-count">Menampilkan {{ $pendaftaranPaginated->firstItem() ?? 0 }}–{{ $pendaftaranPaginated->lastItem() ?? 0 }} dari {{ $pendaftaranPaginated->total() }} pendaftar</span>

    @if ($pendaftaranPaginated->lastPage() > 1)
      <nav class="flex items-center gap-1.5" aria-label="Navigasi Halaman Tabel">
        {{-- Tombol Sebelumnya --}}
        @if ($pendaftaranPaginated->onFirstPage())
          <span
            class="w-9 h-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-300 cursor-not-allowed select-none"
            aria-disabled="true"
            title="Halaman sebelumnya"
          >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="m15 18-6-6 6-6"></path>
            </svg>
          </span>
        @else
          <a
            href="{{ $pendaftaranPaginated->previousPageUrl() }}"
            class="w-9 h-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:text-blue-600 hover:bg-blue-50 hover:border-blue-200 transition cursor-pointer active:scale-95"
            title="Halaman sebelumnya"
            data-pagination-link
          >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="m15 18-6-6 6-6"></path>
            </svg>
          </a>
        @endif

        {{-- Indikator Halaman --}}
        <span class="px-2.5 py-1 text-xs font-semibold text-slate-600 select-none tabular-nums">
          {{ $pendaftaranPaginated->currentPage() }} / {{ $pendaftaranPaginated->lastPage() }}
        </span>

        {{-- Tombol Selanjutnya --}}
        @if ($pendaftaranPaginated->hasMorePages())
          <a
            href="{{ $pendaftaranPaginated->nextPageUrl() }}"
            class="w-9 h-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:text-blue-600 hover:bg-blue-50 hover:border-blue-200 transition cursor-pointer active:scale-95"
            title="Halaman selanjutnya"
            data-pagination-link
          >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="m9 18 6-6-6-6"></path>
            </svg>
          </a>
        @else
          <span
            class="w-9 h-9 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-300 cursor-not-allowed select-none"
            aria-disabled="true"
            title="Halaman selanjutnya"
          >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="m9 18 6-6-6-6"></path>
            </svg>
          </span>
        @endif
      </nav>
    @endif
  </div>

  <style>
    /* Hover Bridge antara Dropdown Menu Utama (Level 1) dan Sub-menu Melayang (Level 2) */
    .filter-flyout::before {
      content: '';
      position: absolute;
      top: -8px;
      bottom: -8px;
      right: -14px;
      width: 16px;
      background: transparent;
      pointer-events: auto;
    }

    /* Memastikan Sub-menu Level 2 tampil dengan mulus saat di-hover maupun saat fokus */
    .filter-category-item:hover > .filter-flyout,
    .filter-category-item:focus-within > .filter-flyout,
    .filter-category-item.flyout-active > .filter-flyout {
      display: block !important;
    }
  </style>

  <script>
    (function initFilterFlyoutDropdown() {
      const container = document.getElementById('filter-dropdown-container');
      const triggerBtn = document.getElementById('filter-trigger-btn');
      const menuLevel1 = document.getElementById('filter-menu-level1');
      if (!container || !triggerBtn || !menuLevel1) return;

      let closeTimer = null;

      function openDropdown() {
        clearTimeout(closeTimer);
        menuLevel1.classList.remove('hidden');
        triggerBtn.setAttribute('aria-expanded', 'true');
      }

      function closeDropdown(immediate = false) {
        if (immediate) {
          clearTimeout(closeTimer);
          menuLevel1.classList.add('hidden');
          triggerBtn.setAttribute('aria-expanded', 'false');
        } else {
          clearTimeout(closeTimer);
          closeTimer = setTimeout(() => {
            menuLevel1.classList.add('hidden');
            triggerBtn.setAttribute('aria-expanded', 'false');
          }, 220);
        }
      }

      function toggleDropdown() {
        if (menuLevel1.classList.contains('hidden')) {
          openDropdown();
        } else {
          closeDropdown(true);
        }
      }

      triggerBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleDropdown();
      });

      container.addEventListener('mouseenter', openDropdown);
      container.addEventListener('mouseleave', () => closeDropdown(false));

      // Tutup menu jika klik di luar komponen filter
      document.addEventListener('click', (e) => {
        if (!container.contains(e.target)) {
          closeDropdown(true);
        }
      });

      // Tutup menu jika tombol Escape ditekan
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          closeDropdown(true);
        }
      });

      // Dukungan interaksi hover & sentuh pada item kategori Level 1
      const catItems = container.querySelectorAll('.filter-category-item');
      catItems.forEach(item => {
        item.addEventListener('mouseenter', () => {
          catItems.forEach(other => {
            if (other !== item) other.classList.remove('flyout-active');
          });
          item.classList.add('flyout-active');
        });
        item.addEventListener('mouseleave', () => {
          item.classList.remove('flyout-active');
        });
      });
    })();

    // Fungsi memilih salah satu opsi dalam Sub-menu Level 2 (tidak menutup menu utama)
    function selectFilterOption(category, value, label, event) {
      if (event) {
        event.preventDefault();
        event.stopPropagation(); // Mencegah dropdown tertutup saat opsi diklik
      }

      // 1. Simpan nilai ke hidden input yang bersangkutan
      const inputEl = document.getElementById(`filter-input-${category}`);
      if (inputEl) {
        inputEl.value = value;
      }

      // 2. Perbarui indikator visual centang dan teks tebal di Level 2
      const catItem = document.querySelector(`.filter-category-item[data-cat="${category}"]`);
      if (catItem) {
        const flyout = catItem.querySelector('.filter-flyout');
        if (flyout) {
          const buttons = flyout.querySelectorAll('.filter-option-btn');
          buttons.forEach(btn => {
            const isSelected = btn.dataset.filterValue === String(value);
            const checkIcon = btn.querySelector('.cat-check-icon');
            if (isSelected) {
              btn.classList.add('font-bold', 'text-blue-700', 'bg-blue-50/70');
              btn.classList.remove('font-medium', 'text-slate-700');
              if (checkIcon) checkIcon.classList.remove('hidden');
            } else {
              btn.classList.remove('font-bold', 'text-blue-700', 'bg-blue-50/70');
              btn.classList.add('font-medium', 'text-slate-700');
              if (checkIcon) checkIcon.classList.add('hidden');
            }
          });
        }

        // 3. Perbarui indikator titik biru dan teks ringkas di Level 1
        const dot = catItem.querySelector('.cat-active-dot');
        const textLabel = catItem.querySelector('.cat-active-text');
        if (value !== '') {
          if (dot) dot.classList.remove('hidden');
          if (textLabel) {
            textLabel.textContent = label;
            textLabel.classList.remove('hidden');
          }
        } else {
          if (dot) dot.classList.add('hidden');
          if (textLabel) {
            textLabel.textContent = '';
            textLabel.classList.add('hidden');
          }
        }
      }

      // 4. Perbarui angka badge tombol utama dan visibilitas tombol Reset Filter
      updateFilterStateIndicators();

      // 5. Muat data tabel terbaru secara asinkron tanpa refresh layar penuh
      applyAdminFiltersAsync();
    }

    // Fungsi memperbarui badge angka Filter (X) dan tombol Reset Filter
    function updateFilterStateIndicators() {
      const categories = ['jurusan', 'jalur', 'gelombang', 'status'];
      let activeCount = 0;

      categories.forEach(cat => {
        const val = document.getElementById(`filter-input-${cat}`)?.value || '';
        if (val !== '') {
          activeCount++;
        }
      });

      const badge = document.getElementById('filter-badge-count');
      const triggerBtn = document.getElementById('filter-trigger-btn');
      if (badge) {
        badge.textContent = activeCount;
        if (activeCount > 0) {
          badge.classList.remove('hidden');
        } else {
          badge.classList.add('hidden');
        }
      }
      if (triggerBtn) {
        if (activeCount > 0) {
          triggerBtn.classList.add('border-blue-300', 'text-blue-700', 'bg-blue-50/40');
        } else {
          triggerBtn.classList.remove('border-blue-300', 'text-blue-700', 'bg-blue-50/40');
        }
      }

      const resetBtn = document.getElementById('filter-reset-btn');
      const resetDivider = document.getElementById('filter-reset-divider');
      if (resetBtn) {
        resetBtn.classList.toggle('hidden', activeCount === 0);
        resetBtn.classList.toggle('flex', activeCount > 0);
      }
      if (resetDivider) {
        resetDivider.classList.toggle('hidden', activeCount === 0);
      }
    }

    // Fungsi reset seluruh 4 filter ke kondisi "Semua"
    function resetAllFilters(event) {
      if (event) {
        event.preventDefault();
        event.stopPropagation();
      }

      const categories = ['jurusan', 'jalur', 'gelombang', 'status'];
      categories.forEach(cat => {
        const inputEl = document.getElementById(`filter-input-${cat}`);
        if (inputEl) inputEl.value = '';

        // Kembalikan tanda centang Level 2 ke opsi "Semua"
        const catItem = document.querySelector(`.filter-category-item[data-cat="${cat}"]`);
        if (catItem) {
          const flyout = catItem.querySelector('.filter-flyout');
          if (flyout) {
            const buttons = flyout.querySelectorAll('.filter-option-btn');
            buttons.forEach(btn => {
              const isAllOption = btn.dataset.filterValue === '';
              const checkIcon = btn.querySelector('.cat-check-icon');
              if (isAllOption) {
                btn.classList.add('font-bold', 'text-blue-700', 'bg-blue-50/70');
                btn.classList.remove('font-medium', 'text-slate-700');
                if (checkIcon) checkIcon.classList.remove('hidden');
              } else {
                btn.classList.remove('font-bold', 'text-blue-700', 'bg-blue-50/70');
                btn.classList.add('font-medium', 'text-slate-700');
                if (checkIcon) checkIcon.classList.add('hidden');
              }
            });
          }

          // Hilangkan titik indikator dan label ringkas Level 1
          const dot = catItem.querySelector('.cat-active-dot');
          const textLabel = catItem.querySelector('.cat-active-text');
          if (dot) dot.classList.add('hidden');
          if (textLabel) {
            textLabel.textContent = '';
            textLabel.classList.add('hidden');
          }
        }
      });

      updateFilterStateIndicators();
      applyAdminFiltersAsync();
    }

    // Fungsi mengatur urutan pendaftaran (Terbaru / Terlama) via tombol toggle ikon
    window.setRegistrationOrder = function(order) {
      const inputEl = document.getElementById('filter-input-urutan');
      if (inputEl) {
        inputEl.value = order;
      }

      const btnTerbaru = document.getElementById('sort-btn-terbaru');
      const btnTerlama = document.getElementById('sort-btn-terlama');

      if (order === 'terlama') {
        if (btnTerbaru) {
          btnTerbaru.classList.remove('bg-white', 'text-blue-600', 'font-bold');
          btnTerbaru.classList.add('text-slate-400', 'hover:text-slate-700', 'hover:bg-white/60');
        }
        if (btnTerlama) {
          btnTerlama.classList.add('bg-white', 'text-blue-600', 'font-bold');
          btnTerlama.classList.remove('text-slate-400', 'hover:text-slate-700', 'hover:bg-white/60');
        }
      } else {
        if (btnTerbaru) {
          btnTerbaru.classList.add('bg-white', 'text-blue-600', 'font-bold');
          btnTerbaru.classList.remove('text-slate-400', 'hover:text-slate-700', 'hover:bg-white/60');
        }
        if (btnTerlama) {
          btnTerlama.classList.remove('bg-white', 'text-blue-600', 'font-bold');
          btnTerlama.classList.add('text-slate-400', 'hover:text-slate-700', 'hover:bg-white/60');
        }
      }

      applyAdminFiltersAsync();
    };

    // Pengambilan data filter tabel secara mulus tanpa menutup dropdown dan tanpa refresh
    let filterDebounceTimer = null;
    window.applyAdminFiltersAsync = function() {
      clearTimeout(filterDebounceTimer);
      filterDebounceTimer = setTimeout(async () => {
        const form = document.getElementById('admin-filter-form');
        if (!form) return;

        const formData = new FormData(form);
        const params = new URLSearchParams();
        for (const [key, val] of formData.entries()) {
          if (val !== '') {
            params.append(key, val);
          }
        }
        // Reset ke halaman 1 saat filter berubah
        params.delete('page');

        const fetchUrl = `${form.action}?${params.toString()}`;
        const tableBody = document.getElementById('admin-table-body');
        if (tableBody) {
          tableBody.style.opacity = '0.55';
          tableBody.style.transition = 'opacity 0.15s ease';
        }

        try {
          const response = await fetch(fetchUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
          });
          if (!response.ok) throw new Error('Network error saat memuat filter');
          const html = await response.text();
          const parser = new DOMParser();
          const doc = parser.parseFromString(html, 'text/html');

          // Sinkronisasi isi baris tabel
          const newBody = doc.getElementById('admin-table-body');
          if (newBody && tableBody) {
            tableBody.innerHTML = newBody.innerHTML;
          }
          const newTableSection = doc.getElementById('section-tabel-jurusan');
          const currentTableSection = document.getElementById('section-tabel-jurusan');
          if (newTableSection && currentTableSection) {
            currentTableSection.dataset.registrationFingerprint = newTableSection.dataset.registrationFingerprint || '';
          }

          // Sinkronisasi teks jumlah pendaftar dan navigasi paginasi
          const newFooter = doc.querySelector('#section-tabel-jurusan > .overflow-x-auto + div');
          const currentFooter = document.querySelector('#section-tabel-jurusan > .overflow-x-auto + div');
          if (newFooter && currentFooter) {
            currentFooter.innerHTML = newFooter.innerHTML;
            initPaginationLinks(currentFooter);
          }

          // Sinkronisasi data JSON pendaftar untuk ekspor CSV
          const newScriptData = doc.getElementById('admin-pendaftaran-data');
          if (newScriptData) {
            try {
              window.pendaftaranData = JSON.parse(newScriptData.textContent);
              const currentScript = document.getElementById('admin-pendaftaran-data');
              if (currentScript) currentScript.textContent = newScriptData.textContent;
            } catch (e) {}
          }

          // Perbarui parameter di URL browser tanpa reload halaman
          const urlQuery = params.toString() ? `?${params.toString()}` : '';
          window.history.replaceState(null, '', `${window.location.pathname}${urlQuery}#tabel-jurusan`);

          // Terapkan ulang pencarian teks lokal jika admin sedang mengetik
          if (typeof filterApplicantRows === 'function') {
            filterApplicantRows();
          }
          if (typeof updateApplicantSelectionUi === 'function') {
            updateApplicantSelectionUi();
          }
        } catch (err) {
          console.error('Filter async error:', err);
        } finally {
          if (tableBody) {
            tableBody.style.opacity = '1';
          }
        }
      }, 120);
    };

    // Navigasi paginasi secara asinkron (tanpa reload halaman penuh)
    function initPaginationLinks(container) {
      const root = container || document.getElementById('section-tabel-jurusan');
      if (!root) return;
      root.querySelectorAll('[data-pagination-link]').forEach(link => {
        link.addEventListener('click', function(e) {
          e.preventDefault();
          loadPaginationPage(this.href);
        });
      });
    }

    async function loadPaginationPage(url, shouldScroll = true) {
      const tableBody = document.getElementById('admin-table-body');
      if (tableBody) {
        tableBody.style.opacity = '0.55';
        tableBody.style.transition = 'opacity 0.15s ease';
      }

      try {
        const response = await fetch(url, {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!response.ok) throw new Error('Network error saat memuat halaman');
        const html = await response.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        // Sinkronisasi isi baris tabel
        const newBody = doc.getElementById('admin-table-body');
        if (newBody && tableBody) {
          tableBody.innerHTML = newBody.innerHTML;
        }
        const newTableSection = doc.getElementById('section-tabel-jurusan');
        const currentTableSection = document.getElementById('section-tabel-jurusan');
        if (newTableSection && currentTableSection) {
          currentTableSection.dataset.registrationFingerprint = newTableSection.dataset.registrationFingerprint || '';
        }

        // Sinkronisasi footer paginasi
        const newFooter = doc.querySelector('#section-tabel-jurusan > .overflow-x-auto + div');
        const currentFooter = document.querySelector('#section-tabel-jurusan > .overflow-x-auto + div');
        if (newFooter && currentFooter) {
          currentFooter.innerHTML = newFooter.innerHTML;
          initPaginationLinks(currentFooter);
        }

        const newScriptData = doc.getElementById('admin-pendaftaran-data');
        const currentScriptData = document.getElementById('admin-pendaftaran-data');
        if (newScriptData && currentScriptData) {
          try {
            window.pendaftaranData = JSON.parse(newScriptData.textContent);
            currentScriptData.textContent = newScriptData.textContent;
          } catch (error) {
            console.error('Gagal memperbarui data pendaftar untuk ekspor:', error);
          }
        }

        // Perbarui URL browser
        window.history.replaceState(null, '', url.replace(window.location.origin, ''));

        // Scroll ke atas tabel
        const tableSection = document.getElementById('section-tabel-jurusan');
        if (tableSection && shouldScroll) {
          tableSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        if (typeof filterApplicantRows === 'function') {
          filterApplicantRows();
        }
        if (typeof updateApplicantSelectionUi === 'function') {
          updateApplicantSelectionUi();
        }
      } catch (err) {
        console.error('Pagination async error:', err);
      } finally {
        if (tableBody) {
          tableBody.style.opacity = '1';
        }
      }
    }

    // Inisialisasi pagination links pada halaman pertama
    initPaginationLinks();

    const applicantTableSection = document.getElementById('section-tabel-jurusan');
    const applicantTablePollUrl = "{{ route('admin.pendaftaran.poll') }}";
    let applicantTablePollInProgress = false;

    async function pollApplicantTable() {
      if (applicantTablePollInProgress || !applicantTableSection || applicantTableSection.classList.contains('hidden') || document.visibilityState !== 'visible') {
        return;
      }

      const activeModalIds = ['drawer-backdrop', 'doc-modal', 'delete-confirm-modal'];
      if (activeModalIds.some(id => {
        const modal = document.getElementById(id);
        return modal && !modal.classList.contains('hidden');
      })) {
        return;
      }

      applicantTablePollInProgress = true;
      try {
        const response = await fetch(applicantTablePollUrl, {
          headers: { 'Accept': 'application/json' },
          cache: 'no-store',
        });
        if (!response.ok) {
          throw new Error(`Polling tabel pendaftar gagal dengan status ${response.status}.`);
        }

        const data = await response.json();
        if (typeof data.fingerprint !== 'string') {
          throw new Error('Respons polling tabel pendaftar tidak memiliki fingerprint yang valid.');
        }

        if (data.fingerprint === applicantTableSection.dataset.registrationFingerprint) {
          return;
        }

        const refreshUrl = new URL(window.location.href);
        refreshUrl.searchParams.delete('page');
        refreshUrl.hash = 'tabel-jurusan';
        await loadPaginationPage(refreshUrl.href, false);
      } catch (error) {
        console.error('Gagal memperbarui tabel pendaftar secara realtime:', error);
      } finally {
        applicantTablePollInProgress = false;
      }
    }

    pollApplicantTable();
    window.setInterval(pollApplicantTable, 10000);
  </script>
</section>
