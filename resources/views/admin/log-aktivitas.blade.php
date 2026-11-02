<!-- ============================================================== -->
<!-- 6. HALAMAN KHUSUS LOG AKTIVITAS ADMIN                          -->
<!-- ============================================================== -->
@php
  $logsList = $adminActivityLogs ?? collect();
  $totalLogs = $logsList->count();
  $verifikasiLogsCount = $logsList->filter(fn ($l) => $l->action_type === 'verifikasi')->count();
  $daftarUlangLogsCount = $logsList->filter(fn ($l) => $l->action_type === 'daftar_ulang')->count();
  $otherLogsCount = $totalLogs - $verifikasiLogsCount - $daftarUlangLogsCount;
@endphp

<section
  id="section-log-aktivitas"
  data-csrf="{{ csrf_token() }}"
  class="no-print transition-all py-1 sm:py-2 {{ ($activePage ?? 'beranda') !== 'log-aktivitas' ? 'hidden' : '' }}"
>
  <!-- 1. STATS / KPI SUMMARY CARDS -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-5">
    <!-- Card 1: Total Log -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/90 flex items-center justify-between">
      <div>
        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">Total Log</span>
        <p id="stat-log-total" class="text-2xl sm:text-3xl font-black text-slate-900 mt-1 tabular-nums">{{ $totalLogs }}</p>
        <span class="text-[11px] text-slate-400 mt-0.5 block">Seluruh riwayat aksi</span>
      </div>
      <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10" />
          <polyline points="12 6 12 12 16 14" />
        </svg>
      </div>
    </div>

    <!-- Card 2: Verifikasi Berkas -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/90 flex items-center justify-between">
      <div>
        <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700 block">Verifikasi</span>
        <p id="stat-log-verifikasi" class="text-2xl sm:text-3xl font-black text-slate-900 mt-1 tabular-nums">{{ $verifikasiLogsCount }}</p>
        <span class="text-[11px] text-emerald-600 font-medium mt-0.5 block">Audit berkas siswa</span>
      </div>
      <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
        </svg>
      </div>
    </div>

    <!-- Card 3: Daftar Ulang -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/90 flex items-center justify-between">
      <div>
        <span class="text-[11px] font-bold uppercase tracking-wider text-blue-700 block">Daftar Ulang</span>
        <p id="stat-log-daftar-ulang" class="text-2xl sm:text-3xl font-black text-slate-900 mt-1 tabular-nums">{{ $daftarUlangLogsCount }}</p>
        <span class="text-[11px] text-blue-600 font-medium mt-0.5 block">Status daftar ulang</span>
      </div>
      <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-6 9 2 2 4-4" />
        </svg>
      </div>
    </div>

    <!-- Card 4: Perubahan Data -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/90 flex items-center justify-between">
      <div>
        <span class="text-[11px] font-bold uppercase tracking-wider text-amber-700 block">Ubah & Hapus</span>
        <p id="stat-log-other" class="text-2xl sm:text-3xl font-black text-slate-900 mt-1 tabular-nums">{{ $otherLogsCount }}</p>
        <span class="text-[11px] text-amber-600 font-medium mt-0.5 block">Formulir / Kuota</span>
      </div>
      <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
        </svg>
      </div>
    </div>
  </div>

  <!-- 2. MAIN LOG CARD CONTAINER -->
  <div class="bg-white rounded-[14px] border border-slate-200/90 overflow-hidden">
    <div class="flex flex-col gap-4 p-5 sm:p-6 border-b border-slate-200/80 sm:flex-row sm:items-center sm:justify-between">
      <div class="flex items-center gap-3.5">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-[10px] border border-blue-100/90 bg-blue-50 text-blue-600">
          <svg class="h-[22px] w-[22px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
          </svg>
        </div>
        <div>
          <h2 class="text-lg font-bold leading-tight tracking-tight text-slate-900 sm:text-[20px]">Log Aktivitas Admin</h2>
          <p class="mt-0.5 text-[13.5px] text-slate-500">Riwayat tindakan admin dan panitia secara realtime.</p>
        </div>
      </div>
      <div class="flex items-center gap-2 flex-wrap">
        <span id="log-selected-badge" class="hidden rounded-lg bg-blue-50 px-2.5 py-1.5 text-xs font-semibold text-blue-700 border border-blue-200">
          Terpilih: <strong id="log-selected-count">0</strong>
        </span>
        <button
          type="button"
          id="log-delete-selected"
          onclick="deleteSelectedActivityLogs()"
          class="hidden cursor-pointer items-center gap-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 px-3 py-1.5 text-xs font-semibold text-white transition active:scale-95"
          title="Hapus riwayat log terpilih"
        >
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
          <span id="log-delete-selected-text">Hapus Terpilih</span>
        </button>
        <button
          type="button"
          id="log-delete-all"
          onclick="deleteAllActivityLogs()"
          class="cursor-pointer inline-flex items-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 px-3 py-1.5 text-xs font-bold transition active:scale-95"
          title="Hapus seluruh riwayat log aktivitas dari database"
        >
          <svg class="w-3.5 h-3.5 text-rose-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
          <span>Hapus Semua Log</span>
        </button>
        <div class="inline-flex w-fit shrink-0 items-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50 px-2.5 py-1.5 text-[11px] font-bold text-emerald-700" title="Waktu dan aktivitas log berjalan otomatis tanpa reload">
          <span class="h-2 w-2 shrink-0 animate-pulse rounded-full bg-emerald-500"></span>
          <span>Live Realtime</span>
        </div>
      </div>
    </div>

    <div class="flex flex-wrap items-center gap-2.5 border-b border-slate-200/80 bg-white p-4 sm:p-5">
      <div class="inline-flex h-10 shrink-0 items-center gap-0.5 rounded-lg border border-slate-200/90 bg-slate-100 p-1" role="group" aria-label="Urutan log aktivitas">
        <button type="button" id="log-sort-newest" aria-label="Urutkan log terbaru" aria-pressed="true" onclick="setLogSortOrder('desc')" class="log-sort-button inline-flex h-8 w-8 cursor-pointer items-center justify-center rounded-md bg-white font-bold text-blue-600 transition">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 16 4 4 4-4"></path><path d="M7 20V4"></path><path d="M11 4h10"></path><path d="M11 9h7"></path><path d="M11 14h4"></path></svg>
        </button>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 16 4 4 4-4"></path><path d="M7 20V4"></path><path d="M11 4h10"></path><path d="M11 9h7"></path><path d="M11 14h4"></path></svg>
        </button>
        <button type="button" id="log-sort-oldest" aria-label="Urutkan log terlama" aria-pressed="false" onclick="setLogSortOrder('asc')" class="log-sort-button inline-flex h-8 w-8 cursor-pointer items-center justify-center rounded-md text-slate-400 transition hover:bg-white/60 hover:text-slate-700">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 8 4-4 4 4"></path><path d="M7 4v16"></path><path d="M11 4h4"></path><path d="M11 9h7"></path><path d="M11 14h10"></path></svg>
        </button>
      </div>

      <div class="relative min-w-0 flex-1">
        <input
          type="search"
          id="log-search-input"
          oninput="handleLogSearch(this.value)"
          placeholder="Cari aksi, admin, atau no. registrasi"
          aria-label="Cari log aktivitas"
          class="h-10 w-full rounded-lg border border-slate-200/90 bg-white py-2 pl-10 pr-3.5 text-sm text-slate-800 transition placeholder:text-slate-400 focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-100"
        />
        <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <circle cx="11" cy="11" r="7"></circle>
          <path d="m20 20-3.5-3.5"></path>
        </svg>
      </div>


      <div class="relative shrink-0" id="log-filter-container">
        <button type="button" id="log-filter-trigger" aria-expanded="false" aria-haspopup="true" aria-controls="log-filter-menu" class="inline-flex h-10 cursor-pointer select-none items-center gap-2 rounded-lg border border-slate-200/90 bg-white px-3.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
          <span>Filter</span>
          <span id="log-filter-badge" class="hidden rounded-full bg-blue-600 px-1.5 py-0.5 text-[11px] font-bold leading-none text-white">0</span>
          <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"></path></svg>
        </button>

        <div id="log-filter-menu" class="hidden absolute right-0 top-full z-40 mt-2 w-60 rounded-xl border border-slate-200/90 bg-white p-2 shadow-xl">
          <div class="log-filter-category relative" data-category="action">
            <button type="button" class="log-filter-category-trigger flex w-full cursor-pointer items-center justify-between rounded-lg px-3 py-2.5 text-left text-sm font-medium text-slate-700 transition-colors hover:bg-slate-100" aria-expanded="false">
              <span class="flex min-w-0 items-center gap-2">
                <span class="truncate">Jenis Aktivitas</span>
                <span class="log-filter-active-dot hidden h-2 w-2 shrink-0 rounded-full bg-blue-600"></span>
                <span class="log-filter-active-label hidden max-w-[80px] truncate text-[11px] font-semibold text-blue-600"></span>
              </span>
              <svg class="ml-1.5 h-4 w-4 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg>
            </button>
            <div class="log-filter-flyout hidden absolute right-full top-0 z-50 mr-2 min-w-[220px] rounded-xl border border-slate-200/90 bg-white p-1.5 shadow-xl">
              <div class="mb-1 border-b border-slate-100 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider text-slate-400">Pilih Jenis Aktivitas</div>
              <button type="button" data-log-filter-option="semua" data-filter-label="Semua" onclick="selectLogFilter('semua', 'Semua', event)" class="log-filter-option flex w-full cursor-pointer items-center justify-between rounded-lg bg-blue-50/70 px-2.5 py-2 text-left text-xs font-bold text-blue-700">
                <span>Semua Aksi</span><svg class="log-filter-check h-4 w-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </button>
              <button type="button" data-log-filter-option="verifikasi" data-filter-label="Verifikasi" onclick="selectLogFilter('verifikasi', 'Verifikasi', event)" class="log-filter-option flex w-full cursor-pointer items-center justify-between rounded-lg px-2.5 py-2 text-left text-xs font-medium text-slate-700 hover:bg-slate-100">
                <span>Verifikasi Berkas</span><svg class="log-filter-check hidden h-4 w-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </button>
              <button type="button" data-log-filter-option="daftar_ulang" data-filter-label="Daftar Ulang" onclick="selectLogFilter('daftar_ulang', 'Daftar Ulang', event)" class="log-filter-option flex w-full cursor-pointer items-center justify-between rounded-lg px-2.5 py-2 text-left text-xs font-medium text-slate-700 hover:bg-slate-100">
                <span>Daftar Ulang</span><svg class="log-filter-check hidden h-4 w-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </button>
              <button type="button" data-log-filter-option="edit_data" data-filter-label="Perubahan Data" onclick="selectLogFilter('edit_data', 'Perubahan Data', event)" class="log-filter-option flex w-full cursor-pointer items-center justify-between rounded-lg px-2.5 py-2 text-left text-xs font-medium text-slate-700 hover:bg-slate-100">
                <span>Perubahan Data</span><svg class="log-filter-check hidden h-4 w-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </button>
            </div>
          </div>
          <div id="log-filter-reset-divider" class="my-1.5 hidden border-t border-slate-100"></div>
          <button type="button" id="log-filter-reset-btn" onclick="resetLogFilters(event)" class="hidden w-full cursor-pointer select-none items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-50 hover:text-rose-700">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path></svg>
            <span>Reset Filter</span>
          </button>
        </div>
      </div>
    </div>

    <!-- 3. TABLE OF LOG ENTRIES -->
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-slate-50/90 text-slate-600 border-b border-slate-200 uppercase tracking-wider text-[11px] font-bold">
            <th scope="col" class="w-10 px-3 py-3.5 text-center">
              <input type="checkbox" id="log-select-all" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer" aria-label="Pilih semua log">
            </th>
            <th scope="col" class="w-12 px-3.5 py-3.5 text-center">No</th>
            <th scope="col" class="px-4 py-3.5 whitespace-nowrap">Waktu & Tanggal</th>
            <th scope="col" class="px-4 py-3.5 whitespace-nowrap">Admin / Pelaksana</th>
            <th scope="col" class="px-4 py-3.5 whitespace-nowrap">Tipe Tindakan</th>
            <th scope="col" class="px-4 py-3.5">Rincian Aktivitas</th>
            <th scope="col" class="px-4 py-3.5 whitespace-nowrap text-center">No. Registrasi</th>
            <th scope="col" class="w-16 px-3.5 py-3.5 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody id="dedicated-log-tbody" class="divide-y divide-slate-100">
          @forelse ($logsList as $log)
            @php
              $badgeThemes = [
                'emerald' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'amber' => 'bg-amber-50 text-amber-700 border-amber-200',
                'rose' => 'bg-rose-50 text-rose-700 border-rose-200',
                'sky' => 'bg-sky-50 text-sky-700 border-sky-200',
                'blue' => 'bg-blue-50 text-blue-700 border-blue-200',
              ];
              $themeClass = $badgeThemes[$log->badge_color] ?? $badgeThemes['blue'];
            @endphp
            <tr
              class="dedicated-log-row hover:bg-blue-50/20 transition-colors text-slate-700"
              data-log-id="{{ $log->id }}"
              data-action-type="{{ $log->action_type }}"
              data-search-text="{{ strtolower(($log->admin_name ?? '') . ' ' . ($log->title ?? '') . ' ' . ($log->description ?? '') . ' ' . ($log->no_registrasi ?? '')) }}"
            >
              <!-- Kolom Checkbox -->
              <td class="w-10 px-3 py-3.5 text-center">
                <input
                  type="checkbox"
                  data-log-select="{{ $log->id }}"
                  class="log-row-checkbox h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
                  aria-label="Pilih log #{{ $log->id }}"
                >
              </td>

              <!-- Kolom No -->
              <td class="w-12 px-3.5 py-3.5 text-center font-mono text-slate-400 font-semibold log-col-number">
                {{ $loop->iteration }}
              </td>

              <!-- Kolom Waktu & Tanggal Lengkap -->
              <td class="px-4 py-3.5 whitespace-nowrap">
                <div class="font-mono text-slate-900 font-bold text-[12.5px]">
                  {{ $log->created_at ? $log->created_at->translatedFormat('d M Y') : '-' }}
                </div>
                <div class="text-[11px] text-slate-500 font-mono mt-0.5 flex items-center gap-1.5">
                  <span class="font-semibold text-slate-700">{{ $log->created_at ? $log->created_at->format('H:i:s') . ' WITA' : '-' }}</span>
                  <span class="text-slate-300">•</span>
                  <span
                    class="text-slate-500 log-relative-time"
                    data-timestamp="{{ $log->created_at ? $log->created_at->getTimestamp() * 1000 : '' }}"
                  >{{ $log->created_at ? $log->created_at->diffForHumans() : '-' }}</span>
                </div>
              </td>

              <!-- Kolom Admin / Pelaksana -->
              <td class="px-4 py-3.5 whitespace-nowrap">
                <div class="flex items-center gap-2">
                  <div class="w-7 h-7 rounded-full bg-blue-50 border border-blue-200/80 flex items-center justify-center text-blue-700 font-bold text-xs uppercase shrink-0">
                    {{ strtoupper(substr($log->admin_name ?: 'A', 0, 1)) }}
                  </div>
                  <div>
                    <span class="font-bold text-slate-800 block text-xs">{{ $log->admin_name ?: 'Admin SPMB' }}</span>
                    <span class="text-[10px] text-slate-400 font-medium">Petugas SPMB</span>
                  </div>
                </div>
              </td>

              <!-- Kolom Tipe Tindakan -->
              <td class="px-4 py-3.5 whitespace-nowrap">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $themeClass }}">
                  @if ($log->icon === 'check' || $log->icon === 'check-circle')
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                  @elseif ($log->icon === 'alert-triangle')
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>
                  @elseif ($log->icon === 'x-circle')
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>
                  @else
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 shrink-0"></span>
                  @endif
                  {{ $log->badge_label ?: $log->title }}
                </span>
              </td>

              <!-- Kolom Rincian Deskripsi -->
              <td class="px-4 py-3.5 text-slate-700 leading-relaxed text-[12.5px] max-w-md break-words">
                <strong class="text-slate-900 font-semibold block text-xs">{{ $log->title }}</strong>
                <span class="text-slate-600 block mt-0.5">{{ $log->description ?: '-' }}</span>
              </td>

              <!-- Kolom No. Registrasi -->
              <td class="px-4 py-3.5 whitespace-nowrap text-center">
                @if ($log->no_registrasi)
                  <button
                    type="button"
                    onclick="openReviewDrawer('{{ $log->no_registrasi }}')"
                    class="font-mono font-bold text-blue-600 hover:text-blue-800 hover:underline text-xs cursor-pointer px-2 py-0.5 bg-blue-50 border border-blue-200/60 rounded-md"
                    title="Lihat Pendaftaran {{ $log->no_registrasi }}"
                  >
                    {{ $log->no_registrasi }}
                  </button>
                @else
                  <span class="text-slate-300 font-mono">-</span>
                @endif
              </td>

              <!-- Kolom Aksi -->
              <td class="w-16 px-3.5 py-3.5 text-center">
                <button
                  type="button"
                  onclick="deleteSingleActivityLog({{ $log->id }})"
                  class="cursor-pointer inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition"
                  title="Hapus riwayat log ini"
                >
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    <line x1="10" y1="11" x2="10" y2="17"></line>
                    <line x1="14" y1="11" x2="14" y2="17"></line>
                  </svg>
                </button>
              </td>
            </tr>
          @empty
            <tr id="dedicated-log-empty-row">
              <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                <div class="flex flex-col items-center justify-center">
                  <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center mb-2.5">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <circle cx="12" cy="12" r="10" />
                      <polyline points="12 6 12 12 16 14" />
                    </svg>
                  </div>
                  <p class="font-bold text-slate-700 text-sm">Belum Ada Riwayat Aktivitas</p>
                  <p class="text-xs text-slate-500 mt-0.5">Seluruh tindakan panitia seperti verifikasi dan daftar ulang akan tercatat di sini.</p>
                </div>
              </td>
            </tr>
          @endforelse
          <tr id="dedicated-log-empty-search" class="hidden">
            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
              <p class="text-sm font-semibold text-slate-700">Tidak ada riwayat aktivitas yang sesuai.</p>
              <p class="text-xs text-slate-500 mt-0.5">Coba gunakan kata kunci pencarian yang berbeda.</p>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200/90 bg-white px-6 py-3.5 text-xs text-slate-500 sm:text-[13px]">
      <span id="log-count-container">
        @if ($totalLogs === 0)
          Menampilkan <strong class="font-bold tabular-nums text-slate-800">0</strong> aktivitas
        @elseif ($totalLogs <= 10)
          Menampilkan <strong class="font-bold tabular-nums text-slate-800">{{ $totalLogs }}</strong> aktivitas
        @else
          Menampilkan <strong class="font-bold tabular-nums text-slate-800">1–{{ min(10, $totalLogs) }}</strong> dari <strong class="font-bold tabular-nums text-slate-800">{{ $totalLogs }}</strong> aktivitas
        @endif
      </span>
      <nav id="log-pagination-nav" class="flex items-center gap-1.5 {{ $totalLogs <= 10 ? 'hidden' : '' }}" aria-label="Navigasi halaman log aktivitas">
        <button type="button" id="log-page-prev" onclick="changeLogPage(currentLogPage - 1)" title="Halaman sebelumnya" class="inline-flex h-9 w-9 cursor-not-allowed items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-300 transition" disabled>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>
        </button>
        <span id="log-page-indicator" class="select-none px-2.5 py-1 text-xs font-semibold tabular-nums text-slate-600">1 / {{ max(1, (int) ceil($totalLogs / 10)) }}</span>
        <button type="button" id="log-page-next" onclick="changeLogPage(currentLogPage + 1)" title="Halaman selanjutnya" class="inline-flex h-9 w-9 cursor-not-allowed items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-300 transition" disabled>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg>
        </button>
      </nav>
    </div>
  </div>
</section>

<style>
  #log-filter-menu::before {
    content: '';
    position: absolute;
    top: -10px;
    right: 0;
    left: 0;
    height: 12px;
    background: transparent;
  }

  .log-filter-flyout::before {
    content: '';
    position: absolute;
    top: -8px;
    right: -14px;
    bottom: -8px;
    width: 16px;
    background: transparent;
  }

  .log-filter-category:hover > .log-filter-flyout,
  .log-filter-category:focus-within > .log-filter-flyout,
  .log-filter-category.flyout-active > .log-filter-flyout {
    display: block;
  }
</style>

<!-- JAVASCRIPT LOGIC UNTUK HALAMAN KHUSUS LOG AKTIVITAS -->
<script>
  let currentLogFilter = 'semua';
  let currentLogSearchQuery = '';
  let currentLogSortOrder = 'desc';
  let currentLogPage = 1;
  const logPerPage = 10;
  let latestLogId = {{ $logsList->max('id') ?? 0 }};

  function escapeLogHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function filterDedicatedLog(filterType) {
    currentLogFilter = filterType;
    currentLogPage = 1;
    updateLogFilterIndicators();
    applyDedicatedLogFilters();
  }

  function handleLogSearch(query) {
    currentLogSearchQuery = (query || '').toLowerCase().trim();
    currentLogPage = 1;
    applyDedicatedLogFilters();
  }

  function updateLogFilterIndicators() {
    document.querySelectorAll('.log-filter-option').forEach(option => {
      const isSelected = option.dataset.logFilterOption === currentLogFilter;
      const check = option.querySelector('.log-filter-check');
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

    const selectedOption = document.querySelector(`.log-filter-option[data-log-filter-option="${currentLogFilter}"]`);
    const category = document.querySelector('.log-filter-category[data-category="action"]');
    const activeDot = category?.querySelector('.log-filter-active-dot');
    const activeLabel = category?.querySelector('.log-filter-active-label');
    const filterBadge = document.getElementById('log-filter-badge');
    const filterTrigger = document.getElementById('log-filter-trigger');
    const resetButton = document.getElementById('log-filter-reset-btn');
    const resetDivider = document.getElementById('log-filter-reset-divider');
    const isFiltered = currentLogFilter !== 'semua';

    if (activeDot) {
      activeDot.classList.toggle('hidden', !isFiltered);
    }
    if (activeLabel) {
      activeLabel.textContent = isFiltered ? (selectedOption?.dataset.filterLabel || '') : '';
      activeLabel.classList.toggle('hidden', !isFiltered);
    }
    if (filterBadge) {
      filterBadge.textContent = isFiltered ? '1' : '0';
      filterBadge.classList.toggle('hidden', !isFiltered);
    }
    if (filterTrigger) {
      filterTrigger.classList.toggle('border-blue-300', isFiltered);
      filterTrigger.classList.toggle('text-blue-700', isFiltered);
      filterTrigger.classList.toggle('bg-blue-50/40', isFiltered);
    }
    if (resetButton) {
      resetButton.classList.toggle('hidden', !isFiltered);
      resetButton.classList.toggle('flex', isFiltered);
    }
    if (resetDivider) {
      resetDivider.classList.toggle('hidden', !isFiltered);
    }
  }

  function selectLogFilter(filterType, label, event) {
    if (event) {
      event.preventDefault();
      event.stopPropagation();
    }
    filterDedicatedLog(filterType);
  }

  function resetLogFilters(event) {
    if (event) {
      event.preventDefault();
      event.stopPropagation();
    }
    filterDedicatedLog('semua');
  }

  function setLogSortOrder(order) {
    currentLogSortOrder = order;
    const newestButton = document.getElementById('log-sort-newest');
    const oldestButton = document.getElementById('log-sort-oldest');

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

    currentLogPage = 1;
    applyDedicatedLogFilters();
  }

  function changeLogPage(page) {
    currentLogPage = page;
    applyDedicatedLogFilters();
  }

  function updateLogPaginationButton(button, isEnabled) {
    if (!button) {
      return;
    }
    button.disabled = !isEnabled;
    button.classList.toggle('cursor-not-allowed', !isEnabled);
    button.classList.toggle('text-slate-300', !isEnabled);
    button.classList.toggle('cursor-pointer', isEnabled);
    button.classList.toggle('text-slate-600', isEnabled);
    button.classList.toggle('hover:bg-slate-100', isEnabled);
    button.classList.toggle('hover:text-slate-900', isEnabled);
  }

  function applyDedicatedLogFilters() {
    const rows = Array.from(document.querySelectorAll('.dedicated-log-row'));
    const filteredRows = rows.filter(row => {
      const rowType = row.dataset.actionType || '';
      const rowSearch = row.dataset.searchText || '';
      const matchType = (currentLogFilter === 'semua') || (rowType === currentLogFilter);
      const matchSearch = !currentLogSearchQuery || rowSearch.includes(currentLogSearchQuery);
      return matchType && matchSearch;
    });

    filteredRows.sort((firstRow, secondRow) => {
      const comparison = Number(firstRow.dataset.logId || 0) - Number(secondRow.dataset.logId || 0);
      return currentLogSortOrder === 'desc' ? -comparison : comparison;
    });

    const totalPages = Math.max(1, Math.ceil(filteredRows.length / logPerPage));
    currentLogPage = Math.min(Math.max(currentLogPage, 1), totalPages);
    const startIndex = (currentLogPage - 1) * logPerPage;
    const endIndex = Math.min(startIndex + logPerPage, filteredRows.length);

    rows.forEach(row => row.classList.add('hidden'));
    const tbody = document.getElementById('dedicated-log-tbody');
    const emptyRow = document.getElementById('dedicated-log-empty-row');
    const searchEmpty = document.getElementById('dedicated-log-empty-search');
    const emptyAnchor = emptyRow || searchEmpty || null;
    filteredRows.forEach((row, index) => {
      if (tbody) {
        tbody.insertBefore(row, emptyAnchor);
      }
      if (index >= startIndex && index < endIndex) {
        row.classList.remove('hidden');
      }
      const numberCell = row.querySelector('.log-col-number');
      if (numberCell) {
        numberCell.textContent = String(index + 1);
      }
    });

    if (emptyRow) {
      emptyRow.classList.toggle('hidden', rows.length > 0);
    }
    if (searchEmpty) {
      searchEmpty.classList.toggle('hidden', filteredRows.length > 0 || rows.length === 0);
    }

    const countContainer = document.getElementById('log-count-container');
    const paginationNav = document.getElementById('log-pagination-nav');
    const pageIndicator = document.getElementById('log-page-indicator');

    if (countContainer) {
      if (filteredRows.length === 0) {
        countContainer.innerHTML = 'Menampilkan <strong class="font-bold tabular-nums text-slate-800">0</strong> aktivitas';
      } else if (filteredRows.length <= logPerPage) {
        countContainer.innerHTML = `Menampilkan <strong class="font-bold tabular-nums text-slate-800">${filteredRows.length}</strong> aktivitas`;
      } else {
        const rangeText = (startIndex + 1 === endIndex)
          ? `${endIndex}`
          : `${startIndex + 1}–${endIndex}`;
        countContainer.innerHTML = `Menampilkan <strong class="font-bold tabular-nums text-slate-800">${rangeText}</strong> dari <strong class="font-bold tabular-nums text-slate-800">${filteredRows.length}</strong> aktivitas`;
      }
    }

    if (paginationNav) {
      paginationNav.classList.toggle('hidden', totalPages <= 1);
    }
    if (pageIndicator) pageIndicator.textContent = `${currentLogPage} / ${totalPages}`;

    updateLogPaginationButton(document.getElementById('log-page-prev'), currentLogPage > 1);
    updateLogPaginationButton(document.getElementById('log-page-next'), currentLogPage < totalPages);
    updateLogSelectionUi();
  }

  // ==============================================================
  // SELECTION & PENGHAPUSAN LOG AKTIVITAS (SINGLE & BULK DELETE)
  // ==============================================================
  function updateLogSelectionUi() {
    const checkboxes = Array.from(document.querySelectorAll('.log-row-checkbox'));
    const visibleCheckboxes = checkboxes.filter(cb => {
      const row = cb.closest('tr');
      return row && !row.classList.contains('hidden');
    });
    const selectedCount = visibleCheckboxes.filter(cb => cb.checked).length;

    const selectAll = document.getElementById('log-select-all');
    if (selectAll) {
      selectAll.checked = visibleCheckboxes.length > 0 && selectedCount === visibleCheckboxes.length;
      selectAll.indeterminate = selectedCount > 0 && selectedCount < visibleCheckboxes.length;
    }

    const badge = document.getElementById('log-selected-badge');
    const countEl = document.getElementById('log-selected-count');
    const deleteBtn = document.getElementById('log-delete-selected');

    if (badge && countEl && deleteBtn) {
      if (selectedCount > 0) {
        badge.classList.remove('hidden');
        deleteBtn.classList.remove('hidden');
        deleteBtn.classList.add('inline-flex');
        countEl.textContent = String(selectedCount);
      } else {
        badge.classList.add('hidden');
        deleteBtn.classList.add('hidden');
        deleteBtn.classList.remove('inline-flex');
        countEl.textContent = '0';
      }
    }
  }

  async function deleteSingleActivityLog(id) {
    if (!id) return;
    if (!confirm('Apakah Anda yakin ingin menghapus catatan log aktivitas ini?')) {
      return;
    }

    try {
      const res = await fetch(`{{ url('admin/log-aktivitas') }}/${id}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        }
      });
      const data = await res.json();
      if (!res.ok || !data.success) {
        alert(data.message || 'Gagal menghapus log aktivitas.');
        return;
      }

      const row = document.querySelector(`.dedicated-log-row[data-log-id="${id}"]`);
      if (row) {
        row.remove();
      }

      const statTotal = document.getElementById('stat-log-total');
      if (statTotal) {
        const cur = parseInt(statTotal.textContent.trim()) || 0;
        statTotal.textContent = String(Math.max(0, cur - 1));
      }

      applyDedicatedLogFilters();
      updateLogSelectionUi();
      if (typeof showToast === 'function') {
        showToast('Log aktivitas berhasil dihapus.', 'success');
      }
    } catch {
      alert('Terjadi kesalahan jaringan.');
    }
  }

  async function deleteSelectedActivityLogs() {
    const checked = Array.from(document.querySelectorAll('.log-row-checkbox:checked'));
    const ids = checked.map(cb => parseInt(cb.dataset.logSelect)).filter(Boolean);

    if (ids.length === 0) {
      alert('Pilih setidaknya satu log aktivitas untuk dihapus.');
      return;
    }

    if (!confirm(`Hapus ${ids.length} log aktivitas terpilih secara permanen?`)) {
      return;
    }

    const btn = document.getElementById('log-delete-selected');
    if (btn) btn.disabled = true;

    try {
      const res = await fetch('{{ route('admin.log-aktivitas.destroy-selected') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ ids: ids })
      });
      const data = await res.json();
      if (!res.ok || !data.success) {
        alert(data.message || 'Gagal menghapus log terpilih.');
        if (btn) btn.disabled = false;
        return;
      }

      ids.forEach(id => {
        const row = document.querySelector(`.dedicated-log-row[data-log-id="${id}"]`);
        if (row) row.remove();
      });

      const statTotal = document.getElementById('stat-log-total');
      if (statTotal) {
        const cur = parseInt(statTotal.textContent.trim()) || 0;
        statTotal.textContent = String(Math.max(0, cur - ids.length));
      }

      applyDedicatedLogFilters();
      updateLogSelectionUi();
      if (btn) btn.disabled = false;

      if (typeof showToast === 'function') {
        showToast(`${ids.length} log aktivitas berhasil dihapus.`, 'success');
      }
    } catch {
      alert('Terjadi kesalahan jaringan.');
      if (btn) btn.disabled = false;
    }
  }

  async function deleteAllActivityLogs() {
    const totalEl = document.getElementById('stat-log-total');
    const totalCount = parseInt(totalEl?.textContent?.trim() || '0', 10);
    const existingRows = document.querySelectorAll('.dedicated-log-row').length;

    if (totalCount === 0 && existingRows === 0) {
      if (typeof showToast === 'function') showToast('Tidak ada riwayat log yang dapat dihapus.', 'info');
      else alert('Tidak ada riwayat log yang dapat dihapus.');
      return;
    }

    const doDeleteAll = async () => {
      const btn = document.getElementById('log-delete-all');
      if (btn) btn.disabled = true;

      try {
        const res = await fetch('{{ route('admin.log-aktivitas.destroy-all') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
          }
        });
        const data = await res.json();
        if (!res.ok || !data.success) {
          throw new Error(data.message || 'Gagal membersihkan seluruh log.');
        }

        // Hapus seluruh baris log dari DOM
        document.querySelectorAll('.dedicated-log-row').forEach(row => row.remove());

        // Reset semua counter statistik KPI
        if (document.getElementById('stat-log-total')) document.getElementById('stat-log-total').textContent = '0';
        if (document.getElementById('stat-log-verifikasi')) document.getElementById('stat-log-verifikasi').textContent = '0';
        if (document.getElementById('stat-log-daftar-ulang')) document.getElementById('stat-log-daftar-ulang').textContent = '0';
        if (document.getElementById('stat-log-other')) document.getElementById('stat-log-other').textContent = '0';
        if (document.getElementById('log-count-container')) {
          document.getElementById('log-count-container').innerHTML = 'Menampilkan <strong class="font-bold tabular-nums text-slate-800">0</strong> aktivitas';
        }
        if (document.getElementById('log-pagination-nav')) {
          document.getElementById('log-pagination-nav').classList.add('hidden');
        }

        // Tampilkan empty state row jika belum ada di tabel
        const tableBody = document.getElementById('dedicated-log-tbody');
        let emptyRow = document.getElementById('dedicated-log-empty-row');
        if (!emptyRow && tableBody) {
          emptyRow = document.createElement('tr');
          emptyRow.id = 'dedicated-log-empty-row';
          emptyRow.innerHTML = `
            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
              <div class="flex flex-col items-center justify-center">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center mb-2.5">
                  <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                  </svg>
                </div>
                <p class="font-bold text-slate-700 text-sm">Belum Ada Riwayat Aktivitas</p>
                <p class="text-xs text-slate-500 mt-0.5">Seluruh tindakan panitia seperti verifikasi dan daftar ulang akan tercatat di sini.</p>
              </div>
            </td>
          `;
          tableBody.appendChild(emptyRow);
        } else if (emptyRow) {
          emptyRow.classList.remove('hidden');
        }

        updateLogSelectionUi();
        if (btn) btn.disabled = false;

        if (typeof showToast === 'function') {
          showToast(data.message || 'Seluruh log aktivitas berhasil dibersihkan.', 'success');
        } else {
          alert(data.message || 'Seluruh log aktivitas berhasil dibersihkan.');
        }
      } catch (err) {
        if (btn) btn.disabled = false;
        if (typeof showToast === 'function') {
          showToast(err.message || 'Gagal menghapus log.', 'error');
        } else {
          alert(err.message || 'Gagal menghapus log.');
        }
      }
    };

    if (typeof window.openDeleteConfirmModal === 'function') {
      window.openDeleteConfirmModal({
        title: 'Hapus Seluruh Riwayat Log',
        message: 'Apakah Anda yakin ingin menghapus <strong>seluruh riwayat log aktivitas</strong> secara permanen dari database? Tindakan ini tidak dapat dibatalkan.',
        confirmText: 'Ya, Hapus Semua Log',
        onConfirm: doDeleteAll
      });
    } else {
      if (confirm('Hapus seluruh riwayat log aktivitas secara permanen? Tindakan ini tidak dapat dibatalkan.')) {
        doDeleteAll();
      }
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    const selectAll = document.getElementById('log-select-all');
    if (selectAll) {
      selectAll.addEventListener('change', () => {
        document.querySelectorAll('.log-row-checkbox').forEach(cb => {
          const row = cb.closest('tr');
          if (row && !row.classList.contains('hidden')) {
            cb.checked = selectAll.checked;
          }
        });
        updateLogSelectionUi();
      });
    }

    const logTbody = document.getElementById('dedicated-log-tbody');
    if (logTbody) {
      logTbody.addEventListener('change', (e) => {
        if (e.target.matches('.log-row-checkbox')) {
          updateLogSelectionUi();
        }
      });
    }
  });

  (function initLogFilterDropdown() {
    const container = document.getElementById('log-filter-container');
    const trigger = document.getElementById('log-filter-trigger');
    const menu = document.getElementById('log-filter-menu');
    if (!container || !trigger || !menu) {
      return;
    }

    let closeTimer = null;

    function closeDropdown() {
      clearTimeout(closeTimer);
      menu.classList.add('hidden');
      trigger.setAttribute('aria-expanded', 'false');
      container.querySelectorAll('.log-filter-category').forEach(categoryItem => {
        categoryItem.classList.remove('flyout-active');
        const categoryTrigger = categoryItem.querySelector('.log-filter-category-trigger');
        if (categoryTrigger) categoryTrigger.setAttribute('aria-expanded', 'false');
      });
    }

    function openDropdown() {
      clearTimeout(closeTimer);
      menu.classList.remove('hidden');
      trigger.setAttribute('aria-expanded', 'true');
    }

    function scheduleCloseDropdown() {
      clearTimeout(closeTimer);
      closeTimer = setTimeout(closeDropdown, 180);
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

    const category = container.querySelector('.log-filter-category');
    const categoryTrigger = category?.querySelector('.log-filter-category-trigger');
    if (category && categoryTrigger) {
      category.addEventListener('mouseenter', () => {
        category.classList.add('flyout-active');
        categoryTrigger.setAttribute('aria-expanded', 'true');
      });
      category.addEventListener('mouseleave', () => {
        category.classList.remove('flyout-active');
        categoryTrigger.setAttribute('aria-expanded', 'false');
      });
      categoryTrigger.addEventListener('click', event => {
        event.stopPropagation();
        const supportsHover = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
        const shouldOpen = supportsHover || !category.classList.contains('flyout-active');
        category.classList.toggle('flyout-active', shouldOpen);
        categoryTrigger.setAttribute('aria-expanded', String(shouldOpen));
      });
    }

    document.addEventListener('click', event => {
      if (!container.contains(event.target)) closeDropdown();
    });
    document.addEventListener('keydown', event => {
      if (event.key === 'Escape') closeDropdown();
    });
  })();

  updateLogFilterIndicators();
  applyDedicatedLogFilters();

  // ==============================================================
  // REALTIME TIME TICKER (Otomatis Jalan Setiap Detik Tanpa Lag)
  // ==============================================================
  function formatRelativeTimeId(timestampMs) {
    if (!timestampMs || isNaN(timestampMs)) return '-';
    const now = Date.now();
    const elapsedSec = Math.max(0, Math.floor((now - timestampMs) / 1000));

    if (elapsedSec < 10) {
      return 'Baru saja';
    }
    if (elapsedSec < 60) {
      return `${elapsedSec} detik yang lalu`;
    }
    const minutes = Math.floor(elapsedSec / 60);
    if (minutes < 60) {
      return `${minutes} menit yang lalu`;
    }
    const hours = Math.floor(minutes / 60);
    if (hours < 24) {
      return `${hours} jam yang lalu`;
    }
    const days = Math.floor(hours / 24);
    if (days < 7) {
      return `${days} hari yang lalu`;
    }
    const weeks = Math.floor(days / 7);
    if (weeks < 5) {
      return `${weeks} minggu yang lalu`;
    }
    const months = Math.floor(days / 30);
    if (months < 12) {
      return `${months} bulan yang lalu`;
    }
    const years = Math.floor(days / 365);
    return `${years} tahun yang lalu`;
  }

  function updateLogRelativeTimes() {
    if (document.hidden) return;

    const timeElements = document.querySelectorAll('.log-relative-time');
    if (!timeElements || timeElements.length === 0) return;

    const now = Date.now();

    for (let i = 0; i < timeElements.length; i++) {
      const el = timeElements[i];
      const rawTs = el.getAttribute('data-timestamp');
      if (!rawTs) continue;

      const ts = Number(rawTs);
      if (isNaN(ts) || ts <= 0) continue;

      const elapsedSec = Math.max(0, Math.floor((now - ts) / 1000));
      const newText = formatRelativeTimeId(ts);

      // Dirty-checking: HANYA ubah text jika ada perubahan string (0% repaint lag)
      if (el.textContent !== newText) {
        el.textContent = newText;
      }

      // Transisi warna halus: setelah lewat 60 detik, ubah hijau emerald menjadi abu-abu slate
      if (elapsedSec >= 60 && el.classList.contains('text-emerald-600')) {
        el.classList.remove('text-emerald-600', 'font-bold');
        el.classList.add('text-slate-500');
      }
    }
  }

  // Ticker otomatis berjalan setiap detik (1000ms), ultra ringan di memori
  setInterval(updateLogRelativeTimes, 1000);

  // Perbarui seketika saat user kembali membuka tab browser
  document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
      updateLogRelativeTimes();
    }
  });

  // ==============================================================
  // REALTIME APPENDING KE TABEL LOG HALAMAN KHUSUS
  // ==============================================================
  window.appendAdminActivityLog = function(log) {
    if (!log) return;
    const tbody = document.getElementById('dedicated-log-tbody');
    const emptyRow = document.getElementById('dedicated-log-empty-row');
    if (emptyRow) emptyRow.remove();

    // Mencegah duplikasi baris jika id log sudah ada di tabel
    if (log.id && document.querySelector(`.dedicated-log-row[data-log-id="${log.id}"]`)) {
      return;
    }

    if (log.id && Number(log.id) > latestLogId) {
      latestLogId = Number(log.id);
    }

    // Update counter KPI
    const statTotal = document.getElementById('stat-log-total');
    if (statTotal) statTotal.textContent = (parseInt(statTotal.textContent.trim()) || 0) + 1;

    if (log.action_type === 'verifikasi') {
      const statVerif = document.getElementById('stat-log-verifikasi');
      if (statVerif) statVerif.textContent = (parseInt(statVerif.textContent.trim()) || 0) + 1;
    } else if (log.action_type === 'daftar_ulang') {
      const statDu = document.getElementById('stat-log-daftar-ulang');
      if (statDu) statDu.textContent = (parseInt(statDu.textContent.trim()) || 0) + 1;
    } else {
      const statOther = document.getElementById('stat-log-other');
      if (statOther) statOther.textContent = (parseInt(statOther.textContent.trim()) || 0) + 1;
    }

    const badgeThemes = {
      'emerald': 'bg-emerald-50 text-emerald-700 border-emerald-200',
      'amber': 'bg-amber-50 text-amber-700 border-amber-200',
      'rose': 'bg-rose-50 text-rose-700 border-rose-200',
      'sky': 'bg-sky-50 text-sky-700 border-sky-200',
      'blue': 'bg-blue-50 text-blue-700 border-blue-200',
    };
    const themeClass = badgeThemes[log.badge_color] || badgeThemes['blue'];

    let dotColor = 'bg-blue-500';
    if (log.icon === 'check' || log.icon === 'check-circle') dotColor = 'bg-emerald-500';
    else if (log.icon === 'alert-triangle') dotColor = 'bg-amber-500';
    else if (log.icon === 'x-circle') dotColor = 'bg-rose-500';

    const createdTs = log.created_at_ts || Date.now();
    const now = new Date(createdTs);
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const dateFormatted = log.date_only || `${String(now.getDate()).padStart(2, '0')} ${monthNames[now.getMonth()]} ${now.getFullYear()}`;
    const timeFormatted = log.time_formatted || `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}:${String(now.getSeconds()).padStart(2, '0')} WITA`;

    const tr = document.createElement('tr');
    tr.className = 'dedicated-log-row hover:bg-blue-50/20 transition-colors text-slate-700 bg-blue-50/40';
    if (log.id) tr.dataset.logId = log.id;
    tr.dataset.actionType = log.action_type || '';
    tr.dataset.searchText = `${log.admin_name || ''} ${log.title || ''} ${log.description || ''} ${log.no_registrasi || ''}`.toLowerCase();

    const regNoHtml = log.no_registrasi ? `
      <button
        type="button"
        onclick="openReviewDrawer('${escapeLogHtml(log.no_registrasi)}')"
        class="font-mono font-bold text-blue-600 hover:text-blue-800 hover:underline text-xs cursor-pointer px-2 py-0.5 bg-blue-50 border border-blue-200/60 rounded-md"
        title="Lihat Pendaftaran ${escapeLogHtml(log.no_registrasi)}"
      >
        ${escapeLogHtml(log.no_registrasi)}
      </button>
    ` : '<span class="text-slate-300 font-mono">-</span>';

    const adminInitial = escapeLogHtml((log.admin_name || 'A').charAt(0).toUpperCase());
    const adminName = escapeLogHtml(log.admin_name || 'Admin SPMB');

    tr.innerHTML = `
      <td class="w-10 px-3 py-3.5 text-center">
        <input
          type="checkbox"
          data-log-select="${escapeLogHtml(log.id || '')}"
          class="log-row-checkbox h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
          aria-label="Pilih log #${escapeLogHtml(log.id || '')}"
        >
      </td>
      <td class="w-12 px-3.5 py-3.5 text-center font-mono text-slate-400 font-semibold log-col-number">1</td>
      <td class="px-4 py-3.5 whitespace-nowrap">
        <div class="font-mono text-slate-900 font-bold text-[12.5px]">${escapeLogHtml(dateFormatted)}</div>
        <div class="text-[11px] text-slate-500 font-mono mt-0.5 flex items-center gap-1.5">
          <span class="font-semibold text-slate-700">${escapeLogHtml(timeFormatted)}</span>
          <span class="text-slate-300">•</span>
          <span
            class="text-emerald-600 font-bold log-relative-time"
            data-timestamp="${createdTs}"
          >${escapeLogHtml(formatRelativeTimeId(createdTs))}</span>
        </div>
      </td>
      <td class="px-4 py-3.5 whitespace-nowrap">
        <div class="flex items-center gap-2">
          <div class="w-7 h-7 rounded-full bg-blue-50 border border-blue-200/80 flex items-center justify-center text-blue-700 font-bold text-xs uppercase shrink-0">
            ${adminInitial}
          </div>
          <div>
            <span class="font-bold text-slate-800 block text-xs">${adminName}</span>
            <span class="text-[10px] text-slate-400 font-medium">Petugas SPMB</span>
          </div>
        </div>
      </td>
      <td class="px-4 py-3.5 whitespace-nowrap">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border ${themeClass}">
          <span class="w-1.5 h-1.5 rounded-full ${dotColor} shrink-0"></span>
          ${escapeLogHtml(log.badge_label || log.title)}
        </span>
      </td>
      <td class="px-4 py-3.5 text-slate-700 leading-relaxed text-[12.5px] max-w-md break-words">
        <strong class="text-slate-900 font-semibold block text-xs">${escapeLogHtml(log.title)}</strong>
        <span class="text-slate-600 block mt-0.5">${escapeLogHtml(log.description || '-')}</span>
      </td>
      <td class="px-4 py-3.5 whitespace-nowrap text-center">
        ${regNoHtml}
      </td>
      <td class="w-16 px-3.5 py-3.5 text-center">
        <button
          type="button"
          onclick="deleteSingleActivityLog(${log.id || 0})"
          class="cursor-pointer inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition"
          title="Hapus riwayat log ini"
        >
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
            <line x1="10" y1="11" x2="10" y2="17"></line>
            <line x1="14" y1="11" x2="14" y2="17"></line>
          </svg>
        </button>
      </td>
    `;

    if (tbody) {
      tbody.prepend(tr);
      setTimeout(() => {
        tr.classList.remove('bg-blue-50/40');
      }, 2500);
      applyDedicatedLogFilters();
    }
  };

  // ==============================================================
  // BACKGROUND SYNC / POLLING LOG BARU DARI ADMIN / TAB LAIN
  // ==============================================================
  async function pollNewAdminLogs() {
    if (document.hidden) return;

    try {
      const url = `{{ route('admin.log-aktivitas.poll') }}?after_id=${latestLogId}`;
      const res = await fetch(url, {
        headers: { 'Accept': 'application/json' },
        cache: 'no-store'
      });

      if (!res.ok) return;
      const data = await res.json();

      if (data && data.success && Array.isArray(data.logs) && data.logs.length > 0) {
        data.logs.forEach(log => {
          if (log.id && Number(log.id) > latestLogId) {
            latestLogId = Number(log.id);
          }
          window.appendAdminActivityLog(log);
        });
      }
    } catch {
      // Menghandle koneksi bermasalah secara senyap tanpa lag atau alert
    }
  }

  // Polling data log baru setiap 10 detik secara senyap di background
  setInterval(pollNewAdminLogs, 10000);
</script>
