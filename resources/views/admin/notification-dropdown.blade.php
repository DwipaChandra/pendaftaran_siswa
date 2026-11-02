@php
  // ==============================================================
  // MASTER PENGELOMPOKAN NOTIFIKASI SPMB BERDASARKAN UMUR WAKTU
  // ==============================================================
  // Periode 1 (0 - 1 hari / unread) : "Baru"
  // Periode 2 (2 - 7 hari lalu)     : "7 Hari Terakhir"
  // Periode 3 (8 - 14 hari lalu)    : "Minggu Lalu" (JANGAN gunakan teks "14 hari terakhir")
  // Periode 4 (15 - 21 hari lalu)   : "3 Minggu Lalu" (JANGAN gunakan teks "21 hari terakhir")
  // Periode 5 (22 - 30 hari lalu)   : "Sebulan Terakhir" (JANGAN gunakan teks "30 hari terakhir")

  $now = \Carbon\Carbon::now();

  $determinePeriod = function(int $diffDays): string {
      if ($diffDays <= 1) {
          return 'baru';
      }
      if ($diffDays <= 7) {
          return '7_hari';
      }
      if ($diffDays <= 14) {
          return 'minggu_lalu';
      }
      if ($diffDays <= 21) {
          return '3_minggu_lalu';
      }
      return 'sebulan_terakhir';
  };

  // Base raw notifications (berasal dari data pendaftar nyata di tabel)
  $rawNotifications = [];
  $dbPendaftaran = collect($pendaftaranList ?? []);

  // Urutkan pendaftaran dari tgl_daftar terbaru ke terlama
  $sortedPendaftaran = $dbPendaftaran->sortByDesc(function ($item) {
      return $item->tgl_daftar ? \Carbon\Carbon::parse($item->tgl_daftar)->timestamp : 0;
  });

  foreach ($sortedPendaftaran as $index => $item) {
      $siswaName = $item->calonSiswa?->nama_lengkap ?? $item->calonSiswa?->user?->name ?? 'Calon Siswa';
      $noReg = $item->no_registrasi ?? ('00' . ($index + 1));
      $rawJurusan = $item->jurusan?->nama_jurusan ?? '';
      $jurusanName = str_contains(strtoupper($rawJurusan), 'KECANTIKAN') ? 'TKC' : ($rawJurusan ? explode(' ', $rawJurusan)[0] : 'SPMB');

      $tglDaftar = $item->tgl_daftar ? \Carbon\Carbon::parse($item->tgl_daftar) : $now;
      $diffDays = (int) abs($now->diffInDays($tglDaftar, false));

      if ($diffDays === 0) {
          $waktuRelatif = 'Hari ini';
      } elseif ($diffDays === 1) {
          $waktuRelatif = '1 hari yang lalu';
      } else {
          $waktuRelatif = "{$diffDays} hari yang lalu";
      }

      $rawNotifications[] = [
          'id' => 'notif-reg-' . $noReg,
          'no_registrasi' => $noReg,
          'nama_siswa' => $siswaName,
          'jurusan_singkat' => $jurusanName,
          'kategori' => 'berkas',
          'aksi' => 'mengunggah berkas pendaftaran',
          'diff_days' => $diffDays,
          'waktu_relatif' => $waktuRelatif,
      ];
  }

  $allNotifications = $rawNotifications;

  // Meta per kategori konten SPMB (ringkas & bersih)
  // Inisialisasi 5 Periode Eksklusif
  $groupedNotifications = [
      'baru' => [
          'key' => 'baru',
          'label' => 'Baru',
          'items' => [],
      ],
      '7_hari' => [
          'key' => '7_hari',
          'label' => '7 Hari Terakhir',
          'items' => [],
      ],
      'minggu_lalu' => [
          'key' => 'minggu_lalu',
          'label' => 'Minggu Lalu',
          'items' => [],
      ],
      '3_minggu_lalu' => [
          'key' => '3_minggu_lalu',
          'label' => '3 Minggu Lalu',
          'items' => [],
      ],
      'sebulan_terakhir' => [
          'key' => 'sebulan_terakhir',
          'label' => 'Sebulan Terakhir',
          'items' => [],
      ],
  ];

  foreach ($allNotifications as $notif) {
      $periodKey = $determinePeriod($notif['diff_days']);
      $groupedNotifications[$periodKey]['items'][] = $notif;
  }
@endphp

<!-- ============================================================== -->
<!-- DROPDOWN NOTIFIKASI PENDAFTAR                                  -->
<!-- ============================================================== -->
<style>
  @keyframes notif-bell-ring {
    0%, 100% {
      transform: rotate(0deg);
    }
    15% {
      transform: rotate(-14deg);
    }
    30% {
      transform: rotate(12deg);
    }
    45% {
      transform: rotate(-8deg);
    }
    60% {
      transform: rotate(6deg);
    }
    75% {
      transform: rotate(-3deg);
    }
  }

  #admin-notification-btn {
    border-radius: 0.875rem !important;
  }

  #admin-notification-btn .notif-bell-icon {
    transform-origin: top center;
  }

  #admin-notification-container.is-open #admin-notification-btn,
  #admin-notification-btn:hover {
    border-color: #cbd5e1 !important;
    background-color: #f8fafc !important;
    color: #1e293b !important;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05) !important;
  }

  #admin-notification-container.is-open #admin-notification-btn .notif-bell-icon,
  #admin-notification-btn:hover .notif-bell-icon {
    color: #1e293b !important;
  }

  #admin-notification-btn:hover .notif-bell-icon {
    animation: notif-bell-ring 0.65s cubic-bezier(0.36, 0.07, 0.19, 0.97) 1 both;
  }

  .notif-filter-scroll {
    scrollbar-width: none;
    -ms-overflow-style: none;
  }
  .notif-filter-scroll::-webkit-scrollbar {
    display: none;
  }
</style>

<div class="relative" id="admin-notification-container">
  <!-- Tombol Lonceng Notifikasi (Squircle Sesuai Desain Baru) -->
  <button
    type="button"
    id="admin-notification-btn"
    class="relative inline-flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-white text-slate-600 hover:text-slate-900 border border-slate-200/90 hover:border-slate-300 transition-all duration-200 cursor-pointer active:scale-95 focus:outline-none"
    title="Notifikasi Aktivitas SPMB"
    aria-label="Notifikasi Aktivitas SPMB"
    aria-expanded="false"
  >
    <!-- Bell Icon -->
    <svg class="notif-bell-icon w-4 h-4 transition-colors duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
    </svg>

    <!-- Titik Merah Notifikasi (Muncul jika ada notifikasi baru) -->
    <span
      id="notification-badge"
      class="hidden absolute top-2 right-2 w-2 h-2 rounded-full bg-rose-500 border border-white text-[0px] pointer-events-none transition-transform scale-100"
    ></span>
  </button>

  <!-- Invisible hover bridge agar kursor tidak terputus saat berpindah dari tombol ke panel -->
  <div class="absolute -bottom-3 left-0 right-0 h-4 z-40 pointer-events-auto"></div>

  <!-- Panel Dropdown Notifikasi (Desain Kartu Ringkas & Compact) -->
  <div
    id="admin-notification-dropdown"
    class="hidden absolute right-0 top-full mt-2 w-[calc(100vw-2rem)] sm:w-[440px] max-w-lg bg-white rounded-2xl border border-slate-200/90 shadow-xl z-50 overflow-hidden transform origin-top-right transition-all duration-200"
    role="region"
    aria-label="Daftar Notifikasi SPMB"
  >
    <!-- Header Notifikasi & Action Bar (Lebih Ringkas & Compact) -->
    <div class="p-2.5 sm:p-3 bg-slate-50/80 border-b border-slate-100 rounded-t-2xl space-y-2">
      <div class="flex items-center justify-between gap-2">
        <div class="flex items-center gap-1.5">
          <h4 class="text-xs sm:text-[13px] font-bold text-slate-900 tracking-tight">Notifikasi SPMB</h4>
          <span id="notif-header-count-badge" class="px-1.5 py-0.5 rounded-full text-[9px] sm:text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200/60">
            <span id="notif-count-display">0</span> notifikasi
          </span>
        </div>
        <button
          type="button"
          id="notif-close-btn"
          class="text-slate-400 hover:text-slate-600 p-1 rounded-md hover:bg-slate-200/50 transition cursor-pointer"
          title="Tutup Notifikasi"
          aria-label="Tutup Notifikasi"
        >
          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Filter Periode Waktu (Semua, Baru, 7 Hari Terakhir, Minggu Lalu, dll) -->
      <div
        id="notif-filter-bar"
        class="notif-filter-scroll flex items-center gap-1 overflow-x-auto py-0.5"
        role="tablist"
        aria-label="Filter Periode Notifikasi"
      >
        <!-- Pill 'Semua' (Default Aktif) -->
        <button
          type="button"
          class="notif-filter-pill active px-2 py-0.5 rounded-full text-[10px] sm:text-[11px] font-bold tracking-tight whitespace-nowrap transition-all duration-150 cursor-pointer bg-blue-600 text-white"
          data-period-filter="all"
        >
          Semua
        </button>

        @foreach ($groupedNotifications as $periodKey => $group)
          @if (!empty($group['items']) && count($group['items']) > 0)
            <button
              type="button"
              class="notif-filter-pill px-2 py-0.5 rounded-full text-[10px] sm:text-[11px] font-semibold tracking-tight whitespace-nowrap transition-all duration-150 cursor-pointer bg-white text-slate-600 border border-slate-200/90 hover:bg-slate-100 hover:text-slate-900"
              data-period-filter="{{ $periodKey }}"
            >
              {{ $group['label'] }}
            </button>
          @endif
        @endforeach
      </div>

      <!-- Header Action: Checkbox 'Select All' & Tombol Aksi Hapus -->
      <div class="flex items-center justify-between gap-2 pt-1.5 border-t border-slate-200/60">
        <label class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-slate-600 cursor-pointer select-none hover:text-slate-900 shrink-0">
          <input
            type="checkbox"
            id="notif-select-all"
            class="w-3.5 h-3.5 rounded text-blue-600 border-slate-300 focus:ring-blue-500 cursor-pointer"
          />
          <span>Pilih Semua</span>
        </label>

        <div class="flex items-center gap-1.5 shrink-0 flex-wrap justify-end">
          <!-- Tombol Hapus Data (Hapus permanen dari database, dengan konfirmasi) -->
          <button
            type="button"
            id="notif-bulk-delete-data-btn"
            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200/80 transition disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer shrink-0 active:scale-95"
            disabled
            title="Hapus data pendaftaran terpilih secara permanen dari sistem & database"
            aria-label="Hapus data pendaftaran terpilih secara permanen"
          >
            <svg class="w-3 h-3 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            <span>Hapus Data</span>
            <span
              id="notif-bulk-delete-count"
              class="hidden px-1 min-w-[13px] h-[13px] rounded-full bg-rose-600 text-white text-[8px] font-black tracking-tight items-center justify-center"
            ></span>
          </button>
        </div>
      </div>
    </div>

    <!-- Isi Daftar Notifikasi Dikelompokkan Berdasarkan Periode Waktu (Scrollable max-h-80) -->
    <div
      id="notif-list-container"
      class="max-h-80 overflow-y-auto overscroll-contain"
    >
      @foreach ($groupedNotifications as $periodKey => $group)
        {{-- CONDITIONAL RENDERING: Hanya render header & group jika memiliki data notifikasi --}}
        @if (!empty($group['items']) && count($group['items']) > 0)
          <div class="notif-period-group" data-period="{{ $periodKey }}">
            <!-- Header Periode Waktu (Teks Bersih, Padding Atas Bawah Ramping) -->
            <div class="sticky top-0 z-10 px-3 py-0.5 bg-slate-50/95 border-y border-slate-100/80 text-[10px] font-bold uppercase tracking-wider text-slate-500">
              <span>{{ $group['label'] }}</span>
            </div>

            <!-- Daftar Item Notifikasi dalam Periode (Desain Bersih & Ringkas) -->
            <div class="notif-period-items divide-y divide-slate-100">
              @foreach ($group['items'] as $notif)
                <div
                  class="notif-item group relative flex items-center justify-between gap-2 py-1.5 px-2.5 sm:px-3 hover:bg-slate-50 transition-colors cursor-pointer"
                  data-notif-id="{{ $notif['id'] }}"
                  data-period="{{ $periodKey }}"
                  data-reg="{{ $notif['no_registrasi'] }}"
                  data-diff-days="{{ $notif['diff_days'] }}"
                >
                  <div class="flex items-center gap-2 min-w-0 flex-1">
                    <!-- Checkbox Satuan (Kecil & Ramping) -->
                    <input
                      type="checkbox"
                      class="notif-item-checkbox w-3.5 h-3.5 rounded text-blue-600 border-slate-300 focus:ring-blue-500 cursor-pointer shrink-0"
                      data-id="{{ $notif['id'] }}"
                      aria-label="Pilih notifikasi pendaftar {{ $notif['no_registrasi'] }}"
                    />

                    <!-- Konten: Fokus Utama adalah ID Siswa -->
                    <div class="min-w-0 flex-1">
                      <div class="flex items-center gap-1.5 flex-wrap">
                        <!-- ID Siswa (Fokus Utama: Menonjol, Bold, Font Mono Ramping) -->
                        <span class="font-mono font-extrabold text-blue-700 bg-blue-50/90 px-1.5 py-0.5 rounded-md border border-blue-200/70 text-[10px] sm:text-[11px] tracking-wide shrink-0">
                          #{{ $notif['no_registrasi'] }}
                        </span>

                        <!-- Nama Siswa (Ukuran Ringkas & Compact) -->
                        <span class="notif-nama text-xs font-bold text-slate-900 truncate leading-tight group-hover:text-blue-600 transition-colors" title="{{ $notif['nama_siswa'] }}">
                          {{ $notif['nama_siswa'] }}
                        </span>

                        <!-- Titik Biru Status Belum Dibaca (Unread) -->
                        <span class="notif-unread-dot hidden w-1.5 h-1.5 rounded-full bg-blue-600 shrink-0" title="Belum dibuka admin"></span>
                      </div>

                      <!-- Sub-informasi: Jurusan & Waktu Relatif (Lebih Ramping) -->
                      <div class="flex items-center gap-1.5 text-[10px] text-slate-400 mt-0.5 font-medium">
                        <span class="notif-jurusan inline-flex text-[9px] font-bold px-1 py-0.5 rounded bg-slate-100 text-slate-700 shrink-0">
                          {{ $notif['jurusan_singkat'] }}
                        </span>
                        <span>•</span>
                        <span class="notif-waktu">{{ $notif['waktu_relatif'] }}</span>
                      </div>
                    </div>
                  </div>

                  <!-- Tombol Aksi: Buka Drawer Persis Seperti Tombol di Admin (Ukuran Ramping & Compact) -->
                  <button
                    type="button"
                    class="notif-open-edit-btn opacity-0 group-hover:opacity-100 focus:opacity-100 inline-flex items-center justify-center w-6 h-6 rounded-md text-slate-700 hover:text-white bg-slate-100 hover:bg-slate-900 border border-slate-200 hover:border-slate-900 transition-all duration-200 shrink-0 cursor-pointer active:scale-95"
                    data-reg="{{ $notif['no_registrasi'] }}"
                    title="Buka detail pendaftaran & verifikasi"
                    aria-label="Buka detail pendaftaran {{ $notif['nama_siswa'] }}"
                  >
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>
                </div>
              @endforeach
            </div>
          </div>
        @endif
      @endforeach

      <!-- Empty State Khusus Periode: Tampil jika filter aktif tidak memiliki notifikasi -->
      <div
        id="notif-period-empty-state"
        class="hidden py-6 px-3 text-center"
      >
        <p class="text-xs font-bold text-slate-700">Tidak ada notifikasi pada periode ini</p>
        <p class="text-[11px] text-slate-400 mt-0.5">Pilih periode lain atau "Semua" untuk melihat notifikasi</p>
      </div>

      <!-- Empty State: Tampil jika seluruh notifikasi telah dihapus -->
      <div
        id="notif-empty-state"
        class="hidden py-6 px-3 text-center"
      >
        <div class="w-10 h-10 mx-auto mb-2 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
          </svg>
        </div>
        <p class="text-xs font-bold text-slate-700">Tidak ada notifikasi</p>
        <p class="text-[11px] text-slate-400 mt-0.5">Semua notifikasi aktivitas pendaftaran SPMB telah dibersihkan</p>
      </div>
    </div>
  </div>
</div>

<script>
  window.ppdbCsrf = "{{ csrf_token() }}";
  window.ppdbDestroySelectedUrl = "{{ route('admin.pendaftaran.destroy-selected') }}";
</script>

<script>
  (function initNotificationDropdown() {
    const container = document.getElementById('admin-notification-container');
    const toggleBtn = document.getElementById('admin-notification-btn');
    const dropdown = document.getElementById('admin-notification-dropdown');
    const closeBtn = document.getElementById('notif-close-btn');
    const badge = document.getElementById('notification-badge');
    const headerCountBadge = document.getElementById('notif-header-count-badge');
    const selectAllCheckbox = document.getElementById('notif-select-all');
    const bulkDeleteDataBtn = document.getElementById('notif-bulk-delete-data-btn');
    const bulkDeleteCount = document.getElementById('notif-bulk-delete-count');
    const listContainer = document.getElementById('notif-list-container');
    const emptyState = document.getElementById('notif-empty-state');
    const filterBar = document.getElementById('notif-filter-bar');
    const periodEmptyState = document.getElementById('notif-period-empty-state');

    if (!container || !toggleBtn || !dropdown || !listContainer) return;

    let currentFilter = 'all';

    // Storage untuk notifikasi yang dihapus
    const dismissedStorageKey = 'admin_dismissed_notifications_v2';
    let dismissedIds = [];
    try {
      dismissedIds = JSON.parse(localStorage.getItem(dismissedStorageKey) || '[]');
      if (!Array.isArray(dismissedIds)) dismissedIds = [];
    } catch (e) {
      dismissedIds = [];
    }

    // Filter out previously dismissed notifications
    if (dismissedIds.length > 0) {
      dismissedIds.forEach(id => {
        const item = listContainer.querySelector(`.notif-item[data-notif-id="${id}"]`);
        if (item) item.remove();
      });
    }

    function saveDismissed(ids) {
      try {
        const unique = Array.from(new Set([...dismissedIds, ...ids]));
        dismissedIds = unique;
        localStorage.setItem(dismissedStorageKey, JSON.stringify(unique));
      } catch (e) {
        console.error('Failed to save dismissed notifications:', e);
      }
    }

    // Storage untuk notifikasi yang sudah dibuka/dilihat oleh admin
    const readStorageKey = 'admin_read_notifications_v2';
    let readIds = [];
    try {
      readIds = JSON.parse(localStorage.getItem(readStorageKey) || '[]');
      if (!Array.isArray(readIds)) readIds = [];
    } catch (e) {
      readIds = [];
    }

    // Inisialisasi awal: jika baru pertama kali, tandai notifikasi yang ada sebagai sudah dibuka
    // agar ikon lonceng tidak langsung diisi angka secara default
    if (localStorage.getItem('admin_notif_initialized_v2') === null) {
      const initialIds = Array.from(listContainer.querySelectorAll('.notif-item'))
        .map(item => item.dataset.notifId)
        .filter(Boolean);
      readIds = initialIds;
      try {
        localStorage.setItem(readStorageKey, JSON.stringify(readIds));
        localStorage.setItem('admin_notif_initialized_v2', 'true');
      } catch (e) {}
    }

    // CONDITIONAL RENDERING DINAMIS:
    // Hapus header & container periode jika semua notifikasi di dalamnya telah dihapus
    function checkEmptyPeriodGroups() {
      const groups = listContainer.querySelectorAll('.notif-period-group');
      groups.forEach(group => {
        const items = group.querySelectorAll('.notif-item');
        if (items.length === 0) {
          const periodKey = group.dataset.period;
          group.remove();

          // Sembunyikan/hapus pill filter jika periode ini sudah kosong
          const matchingPill = filterBar?.querySelector(`.notif-filter-pill[data-period-filter="${periodKey}"]`);
          if (matchingPill) {
            matchingPill.remove();
          }
        }
      });

      // Jika filter aktif adalah periode yang barusan dihapus, reset kembali ke 'all'
      if (currentFilter !== 'all') {
        const activeGroup = listContainer.querySelector(`.notif-period-group[data-period="${currentFilter}"]`);
        if (!activeGroup) {
          applyFilter('all');
        }
      }
    }

    // Filter notifikasi berdasarkan periode waktu (Semua, Baru, 7 Hari, dll)
    function applyFilter(filterKey) {
      currentFilter = filterKey;

      // Update style tombol pill filter
      const pills = document.querySelectorAll('.notif-filter-pill');
      pills.forEach(pill => {
        const isCurrent = pill.dataset.periodFilter === filterKey;
        if (isCurrent) {
          pill.classList.add('active', 'bg-blue-600', 'text-white', 'border-blue-600', 'font-bold');
          pill.classList.remove('bg-white', 'text-slate-600', 'border-slate-200/90', 'hover:bg-slate-100', 'hover:text-slate-900', 'font-semibold');
        } else {
          pill.classList.remove('active', 'bg-blue-600', 'text-white', 'border-blue-600', 'font-bold');
          pill.classList.add('bg-white', 'text-slate-600', 'border', 'border-slate-200/90', 'hover:bg-slate-100', 'hover:text-slate-900', 'font-semibold');
        }
      });

      // Filter grup periode dalam list
      const groups = listContainer.querySelectorAll('.notif-period-group');
      let visibleItemsCount = 0;

      groups.forEach(group => {
        const groupPeriod = group.dataset.period;
        if (filterKey === 'all' || groupPeriod === filterKey) {
          group.classList.remove('hidden');
          visibleItemsCount += group.querySelectorAll('.notif-item').length;
        } else {
          group.classList.add('hidden');
        }
      });

      // Handle empty state khusus periode terfilter
      const totalItems = listContainer.querySelectorAll('.notif-item').length;
      if (periodEmptyState) {
        if (totalItems > 0 && visibleItemsCount === 0) {
          periodEmptyState.classList.remove('hidden');
        } else {
          periodEmptyState.classList.add('hidden');
        }
      }

      updateState();
    }

    // Hitung jumlah notifikasi baru yang belum dibuka admin
    function getUnreadCount() {
      const items = Array.from(listContainer.querySelectorAll('.notif-item'));
      return items.filter(item => {
        const id = item.dataset.notifId;
        return id && !readIds.includes(id);
      }).length;
    }

    // Update status badge: HANYA isi angka jika ada notifikasi baru yang belum dibuka admin
    function updateBadge() {
      const unreadCount = getUnreadCount();
      if (badge) {
        if (unreadCount > 0) {
          badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
          badge.classList.remove('hidden');
        } else {
          badge.textContent = '';
          badge.classList.add('hidden');
        }
      }

      if (headerCountBadge) {
        const totalCount = listContainer.querySelectorAll('.notif-item').length;
        if (unreadCount > 0) {
          headerCountBadge.innerHTML = `<span id="notif-count-display">${unreadCount}</span> baru`;
          headerCountBadge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200/60';
        } else {
          headerCountBadge.innerHTML = `<span id="notif-count-display">${totalCount}</span> notifikasi`;
          headerCountBadge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200/60';
        }
      }
    }

    // Render titik biru unread
    function renderUnreadIndicators() {
      const items = listContainer.querySelectorAll('.notif-item');
      items.forEach(item => {
        const id = item.dataset.notifId;
        const dot = item.querySelector('.notif-unread-dot');
        if (dot) {
          if (id && !readIds.includes(id)) {
            dot.classList.remove('hidden');
          } else {
            dot.classList.add('hidden');
          }
        }
      });
    }

    // Tandai semua notifikasi saat ini sebagai sudah dibuka oleh admin
    function markAllAsRead() {
      const items = Array.from(listContainer.querySelectorAll('.notif-item'));
      const currentIds = items.map(item => item.dataset.notifId).filter(Boolean);
      if (currentIds.length > 0) {
        const unique = Array.from(new Set([...readIds, ...currentIds]));
        readIds = unique;
        try {
          localStorage.setItem(readStorageKey, JSON.stringify(readIds));
        } catch (e) {}
      }

      // Angka pada badge lonceng langsung hilang
      updateBadge();

      // Transisi hilangkan titik penanda 'baru'
      listContainer.querySelectorAll('.notif-unread-dot').forEach(dot => {
        dot.style.transition = 'opacity 0.3s ease';
        dot.style.opacity = '0';
        setTimeout(() => dot.classList.add('hidden'), 300);
      });
    }

    function updateState() {
      checkEmptyPeriodGroups();

      const allItems = Array.from(listContainer.querySelectorAll('.notif-item'));
      const totalCount = allItems.length;

      // Item & checkbox yang sedang tampak (sesuai filter aktif)
      const visibleItems = Array.from(listContainer.querySelectorAll('.notif-period-group:not(.hidden) .notif-item'));
      const visibleCheckboxes = Array.from(listContainer.querySelectorAll('.notif-period-group:not(.hidden) .notif-item-checkbox'));
      const visibleCheckedCount = visibleCheckboxes.filter(cb => cb.checked).length;

      // Total checkbox yang dicentang di seluruh notifikasi
      const allCheckedBoxes = Array.from(listContainer.querySelectorAll('.notif-item-checkbox:checked'));
      const totalCheckedCount = allCheckedBoxes.length;

      // Sembunyikan filter bar jika tidak ada notifikasi sama sekali
      if (filterBar) {
        if (totalCount === 0) {
          filterBar.classList.add('hidden');
        } else {
          filterBar.classList.remove('hidden');
        }
      }

      // Update badge unread vs total
      updateBadge();

      // Empty states
      if (totalCount === 0) {
        if (emptyState) emptyState.classList.remove('hidden');
        if (periodEmptyState) periodEmptyState.classList.add('hidden');
        if (selectAllCheckbox) {
          selectAllCheckbox.checked = false;
          selectAllCheckbox.indeterminate = false;
          selectAllCheckbox.disabled = true;
        }
        if (bulkDeleteDataBtn) {
          bulkDeleteDataBtn.disabled = true;
          bulkDeleteDataBtn.setAttribute('title', 'Hapus data pendaftaran terpilih secara permanen');
        }
        if (bulkDeleteCount) {
          bulkDeleteCount.textContent = '';
          bulkDeleteCount.classList.add('hidden');
          bulkDeleteCount.classList.remove('inline-flex');
        }
      } else {
        if (emptyState) emptyState.classList.add('hidden');

        if (periodEmptyState) {
          if (visibleItems.length === 0) {
            periodEmptyState.classList.remove('hidden');
          } else {
            periodEmptyState.classList.add('hidden');
          }
        }

        if (selectAllCheckbox) {
          if (visibleItems.length === 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.disabled = true;
          } else {
            selectAllCheckbox.disabled = false;
            if (visibleCheckedCount === visibleItems.length && visibleItems.length > 0) {
              selectAllCheckbox.checked = true;
              selectAllCheckbox.indeterminate = false;
            } else if (visibleCheckedCount > 0) {
              selectAllCheckbox.checked = false;
              selectAllCheckbox.indeterminate = true;
            } else {
              selectAllCheckbox.checked = false;
              selectAllCheckbox.indeterminate = false;
            }
          }
        }

        // Tombol Aksi: Hapus Data
        if (totalCheckedCount > 0) {
          if (bulkDeleteDataBtn) {
            bulkDeleteDataBtn.disabled = false;
            bulkDeleteDataBtn.setAttribute('title', `Hapus ${totalCheckedCount} data pendaftar terpilih secara permanen dari database`);
          }
          if (bulkDeleteCount) {
            bulkDeleteCount.textContent = totalCheckedCount;
            bulkDeleteCount.classList.remove('hidden');
            bulkDeleteCount.classList.add('inline-flex');
          }
        } else {
          if (bulkDeleteDataBtn) {
            bulkDeleteDataBtn.disabled = true;
            bulkDeleteDataBtn.setAttribute('title', 'Hapus data pendaftaran terpilih secara permanen');
          }
          if (bulkDeleteCount) {
            bulkDeleteCount.textContent = '';
            bulkDeleteCount.classList.add('hidden');
            bulkDeleteCount.classList.remove('inline-flex');
          }
        }
      }
    }

    let closeTimeout = null;
    let isManuallyClosed = false;

    // Open dropdown immediately
    function openDropdown() {
      if (isManuallyClosed) return;
      if (closeTimeout) {
        clearTimeout(closeTimeout);
        closeTimeout = null;
      }
      dropdown.classList.remove('hidden');
      container.classList.add('is-open');
      toggleBtn.setAttribute('aria-expanded', 'true');

      // Sinkronisasi otomatis dari tabel pendaftar ke notifikasi
      syncTableToNotifications(true);

      // Admin sudah membuka notifikasi -> tandai sebagai sudah dibuka & hilangkan angka
      markAllAsRead();
    }

    // Close dropdown (immediate or with smooth grace delay)
    function closeDropdown(immediate = false) {
      if (closeTimeout) {
        clearTimeout(closeTimeout);
        closeTimeout = null;
      }
      if (immediate) {
        dropdown.classList.add('hidden');
        container.classList.remove('is-open');
        toggleBtn.setAttribute('aria-expanded', 'false');
      } else {
        closeTimeout = setTimeout(() => {
          dropdown.classList.add('hidden');
          container.classList.remove('is-open');
          toggleBtn.setAttribute('aria-expanded', 'false');
          isManuallyClosed = false;
        }, 220);
      }
    }

    // Toggle dropdown open/close on click
    function toggleDropdown() {
      if (dropdown.classList.contains('hidden')) {
        isManuallyClosed = false;
        openDropdown();
      } else {
        isManuallyClosed = true;
        closeDropdown(true);
      }
    }

    // Hover events: langsung muncul dropdown saat hover
    container.addEventListener('mouseenter', () => {
      openDropdown();
    });

    container.addEventListener('mouseleave', () => {
      closeDropdown(false);
      isManuallyClosed = false;
    });

    toggleBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      toggleDropdown();
    });

    if (closeBtn) {
      closeBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        isManuallyClosed = true;
        closeDropdown(true);
      });
    }

    // Close on outside click
    document.addEventListener('click', (e) => {
      if (!container.contains(e.target)) {
        isManuallyClosed = false;
        closeDropdown(true);
      }
    });

    // Close on Escape
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        isManuallyClosed = true;
        closeDropdown(true);
      }
    });

    // Prevent clicks inside dropdown from closing it
    dropdown.addEventListener('click', (e) => {
      e.stopPropagation();
    });

    // Select all checkbox change: hanya centang notifikasi yang sedang tampak sesuai filter aktif
    if (selectAllCheckbox) {
      selectAllCheckbox.addEventListener('change', () => {
        const isChecked = selectAllCheckbox.checked;
        const visibleCheckboxes = listContainer.querySelectorAll('.notif-period-group:not(.hidden) .notif-item-checkbox');
        visibleCheckboxes.forEach(cb => {
          cb.checked = isChecked;
        });
        updateState();
      });
    }

    // Filter pill click listener
    if (filterBar) {
      filterBar.addEventListener('click', (e) => {
        const pill = e.target.closest('.notif-filter-pill');
        if (!pill) return;
        e.preventDefault();
        e.stopPropagation();
        applyFilter(pill.dataset.periodFilter || 'all');
      });
    }

    // Item checkboxes change (event delegation on listContainer)
    listContainer.addEventListener('change', (e) => {
      if (e.target.classList.contains('notif-item-checkbox')) {
        updateState();
      }
    });

    // Fungsi untuk navigasi langsung ke baris tabel dan membuka form edit data
    function navigateToTableAndEdit(noReg) {
      if (!noReg) return;

      // 1. Tutup panel dropdown notifikasi
      closeDropdown(true);

      // Pindah ke tampilan Tabel Jurusan jika sedang di tab/halaman lain
      if (typeof switchAdminView === 'function') {
        switchAdminView('tabel-jurusan');
      }

      // 2. Cari baris pendaftar di tabel
      const targetRow = document.getElementById(`applicant-row-${noReg}`) ||
        document.querySelector(`tr[data-registration="${noReg}"]`);

      if (targetRow) {
        // Tampilkan jika baris tersembunyi
        targetRow.hidden = false;

        // Scroll mulus ke baris tabel
        targetRow.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Highlight baris di tabel dengan warna biru cerah & border ring
        targetRow.classList.add('bg-blue-100/90', 'ring-2', 'ring-blue-400');
        setTimeout(() => {
          targetRow.classList.remove('bg-blue-100/90', 'ring-2', 'ring-blue-400');
        }, 2500);
      } else {
        // Fallback: scroll ke kontainer tabel pendaftaran
        const tableContainer = document.getElementById('admin-table-body')?.closest('.overflow-x-auto') ||
          document.getElementById('admin-table-body');
        if (tableContainer) {
          tableContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      }

      // 3. Pastikan noReg valid untuk drawer
      let targetNoReg = String(noReg);
      if (typeof pendaftaranData !== 'undefined' && Array.isArray(pendaftaranData)) {
        const itemExists = pendaftaranData.some(p => String(p.no_registrasi) === targetNoReg);
        if (!itemExists && pendaftaranData.length > 0) {
          // Fallback ke pendaftar pertama jika noReg demo tidak ada di database lokal
          targetNoReg = String(pendaftaranData[0].no_registrasi);
        }
      }

      // 4. Buka drawer verifikasi persis seperti tombol aksi di tabel admin
      if (typeof openReviewDrawer === 'function') {
        openReviewDrawer(targetNoReg);
      }
    }

    // Klik tombol aksi atau baris notifikasi untuk langsung ke tabel & buka drawer admin
    listContainer.addEventListener('click', (e) => {
      // Abaikan jika klik terjadi pada checkbox seleksi
      if (e.target.closest('.notif-item-checkbox')) return;

      const editBtn = e.target.closest('.notif-open-edit-btn');
      const itemRow = e.target.closest('.notif-item');

      if (editBtn) {
        e.preventDefault();
        e.stopPropagation();
        navigateToTableAndEdit(editBtn.dataset.reg);
      } else if (itemRow) {
        const noReg = itemRow.dataset.reg || itemRow.querySelector('.notif-open-edit-btn')?.dataset.reg;
        if (noReg) {
          navigateToTableAndEdit(noReg);
        }
      }
    });

    // Tombol Aksi: Hapus Data Pendaftar (Hapus permanen dari database, dengan konfirmasi)
    if (bulkDeleteDataBtn) {
      bulkDeleteDataBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();

        const checkedBoxes = Array.from(listContainer.querySelectorAll('.notif-item-checkbox:checked'));
        if (checkedBoxes.length === 0) return;

        const selectedItems = checkedBoxes.map(cb => cb.closest('.notif-item')).filter(Boolean);
        const selectedRegs = Array.from(new Set(selectedItems.map(item => item.dataset.reg).filter(Boolean)));

        if (selectedRegs.length === 0) {
          // Jika hanya ada notifikasi dummy tanpa no registrasi database
          const count = checkedBoxes.length;
          const idsToRemove = checkedBoxes.map(cb => cb.dataset.id).filter(Boolean);
          selectedItems.forEach(item => item.remove());
          saveDismissed(idsToRemove);
          checkEmptyPeriodGroups();
          renderUnreadIndicators();
          updateState();
          if (typeof window.showToast === 'function') {
            window.showToast(`${count} notifikasi dibersihkan.`, 'info');
          }
          return;
        }

        const msgText = selectedRegs.length === 1
          ? `Hapus data pendaftaran <strong>#${selectedRegs[0]}</strong> beserta seluruh berkas dan data verifikasinya dari database? Tindakan ini permanen dan tidak dapat dibatalkan.`
          : `Hapus <strong>${selectedRegs.length} data pendaftaran terpilih</strong> (#${selectedRegs.join(', #')}) beserta seluruh berkas dan data verifikasinya dari database? Tindakan ini permanen dan tidak dapat dibatalkan.`;

        const doDeleteData = async () => {
          const res = await fetch(window.ppdbDestroySelectedUrl, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': window.ppdbCsrf,
              'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
              no_registrasi: selectedRegs
            })
          });

          if (!res.ok) {
            const errData = await res.json().catch(() => ({}));
            throw new Error(errData.message || 'Gagal menghapus data di server.');
          }

          // 1. Hapus notifikasi dari dropdown
          const idsToRemove = checkedBoxes.map(cb => cb.dataset.id).filter(Boolean);
          saveDismissed(idsToRemove);
          selectedItems.forEach(item => item.remove());

          // 2. Hapus baris dari tabel pendaftar di admin
          selectedRegs.forEach(reg => {
            const tableRow = document.getElementById(`applicant-row-${reg}`) ||
                             document.querySelector(`tr[data-registration="${reg}"]`);
            if (tableRow) tableRow.remove();

            if (typeof window.pendaftaranData !== 'undefined' && Array.isArray(window.pendaftaranData)) {
              window.pendaftaranData = window.pendaftaranData.filter(p => String(p.no_registrasi) !== String(reg));
            }
          });

          // 3. Update status, filter & counters
          checkEmptyPeriodGroups();
          renderUnreadIndicators();
          updateState();

          if (typeof updateDashboardKpis === 'function') updateDashboardKpis();
          if (typeof updateJurusanCards === 'function') updateJurusanCards();

          if (typeof window.showToast === 'function') {
            window.showToast(`${selectedRegs.length} data pendaftar berhasil dihapus permanen.`, 'success');
          }
        };

        if (typeof window.openDeleteConfirmModal === 'function') {
          window.openDeleteConfirmModal({
            title: 'Hapus Data Pendaftar',
            message: msgText,
            confirmText: `Ya, Hapus ${selectedRegs.length} Data`,
            onConfirm: doDeleteData
          });
        } else {
          if (window.confirm(`Hapus ${selectedRegs.length} data pendaftaran terpilih beserta berkas dari database?`)) {
            doDeleteData();
          }
        }
      });
    }

    // Definisi urutan periode waktu secara ketat dari TERBARU ke TERLAMA
    const periodOrder = ['baru', '7_hari', 'minggu_lalu', '3_minggu_lalu', 'sebulan_terakhir'];

    const periodLabels = {
      'baru': 'Baru',
      '7_hari': '7 Hari Terakhir',
      'minggu_lalu': 'Minggu Lalu',
      '3_minggu_lalu': '3 Minggu Lalu',
      'sebulan_terakhir': 'Sebulan Terakhir'
    };

    function getOrCreatePeriodGroup(periodKey) {
      let group = listContainer.querySelector(`.notif-period-group[data-period="${periodKey}"]`);
      if (!group) {
        group = document.createElement('div');
        group.className = 'notif-period-group';
        group.dataset.period = periodKey;
        group.innerHTML = `
          <div class="sticky top-0 z-10 px-3 py-0.5 bg-slate-50/95 border-y border-slate-100/80 text-[10px] font-bold uppercase tracking-wider text-slate-500">
            <span>${periodLabels[periodKey] || periodKey}</span>
          </div>
          <div class="notif-period-items divide-y divide-slate-100"></div>
        `;

        const targetBefore = listContainer.querySelector('#notif-period-empty-state') || listContainer.querySelector('#notif-empty-state') || null;
        listContainer.insertBefore(group, targetBefore);

        // Tambahkan pill filter jika belum ada di filter bar
        if (filterBar && !filterBar.querySelector(`.notif-filter-pill[data-period-filter="${periodKey}"]`)) {
          const newPill = document.createElement('button');
          newPill.type = 'button';
          newPill.className = 'notif-filter-pill px-2 py-0.5 rounded-full text-[10px] sm:text-[11px] font-semibold tracking-tight whitespace-nowrap transition-all duration-150 cursor-pointer bg-white text-slate-600 border border-slate-200/90 hover:bg-slate-100 hover:text-slate-900';
          newPill.dataset.periodFilter = periodKey;
          newPill.textContent = periodLabels[periodKey] || periodKey;
          filterBar.appendChild(newPill);
        }
      }
      return group;
    }

    // Urutkan grup periode dan seluruh notifikasi di dalamnya strictly dari TERBARU ke TERLAMA
    function sortPeriodGroupsAndItems() {
      const targetBefore = listContainer.querySelector('#notif-period-empty-state') || listContainer.querySelector('#notif-empty-state') || null;

      // 1. Urutkan grup periode berdasarkan periodOrder ('baru' -> '7_hari' -> 'minggu_lalu' -> '3_minggu_lalu' -> 'sebulan_terakhir')
      const currentGroups = Array.from(listContainer.querySelectorAll('.notif-period-group'));
      currentGroups.sort((a, b) => {
        const idxA = periodOrder.indexOf(a.dataset.period);
        const idxB = periodOrder.indexOf(b.dataset.period);
        return (idxA === -1 ? 999 : idxA) - (idxB === -1 ? 999 : idxB);
      });

      currentGroups.forEach(group => {
        listContainer.insertBefore(group, targetBefore);

        // 2. Di dalam setiap grup periode, urutkan item notifikasi dari TERBARU ke TERLAMA
        const itemsContainer = group.querySelector('.notif-period-items') || group;
        const items = Array.from(itemsContainer.querySelectorAll('.notif-item'));
        items.sort((a, b) => {
          const diffA = parseFloat(a.dataset.diffDays || '0');
          const diffB = parseFloat(b.dataset.diffDays || '0');
          if (diffA !== diffB) {
            // Semakin kecil diffDays = semakin baru
            return diffA - diffB;
          }
          // Tie-break: nomor registrasi descending
          return String(b.dataset.reg || '').localeCompare(String(a.dataset.reg || ''), undefined, { numeric: true });
        });

        items.forEach(item => itemsContainer.appendChild(item));
      });

      // 3. Pastikan urutan pill filter di header juga terurut secara kronologis dari terbaru ke terlama
      if (filterBar) {
        const otherPills = Array.from(filterBar.querySelectorAll('.notif-filter-pill:not([data-period-filter="all"])'));
        otherPills.sort((a, b) => {
          const idxA = periodOrder.indexOf(a.dataset.periodFilter);
          const idxB = periodOrder.indexOf(b.dataset.periodFilter);
          return (idxA === -1 ? 999 : idxA) - (idxB === -1 ? 999 : idxB);
        });
        otherPills.forEach(pill => filterBar.appendChild(pill));
      }
    }

    // Logika Sinkronisasi Data Otomatis dari Tabel ke Notifikasi (Hanya ambil dari tabel & bersihkan yang tidak ada di tabel)
    function syncTableToNotifications(silent = true) {
      try {
        // 1. Ambil data pendaftar dari tabel (sumber data tunggal yang sah)
        let tableApplicants = null;
        if (typeof window.pendaftaranData !== 'undefined' && Array.isArray(window.pendaftaranData)) {
          tableApplicants = window.pendaftaranData;
        } else if (typeof pendaftaranData !== 'undefined' && Array.isArray(pendaftaranData)) {
          tableApplicants = pendaftaranData;
        } else {
          const raw = document.getElementById('admin-pendaftaran-data')?.textContent;
          try {
            if (raw) tableApplicants = JSON.parse(raw);
          } catch (e) {
            tableApplicants = null;
          }
        }

        if (!tableApplicants || tableApplicants.length === 0) {
          // Fallback jika pendaftaranData belum siap: baca langsung baris DOM dari tabel admin
          const tableRows = Array.from(document.querySelectorAll('#admin-table-body tr[data-registration]'));
          tableApplicants = tableRows.map(row => {
            const noReg = row.dataset.registration;
            const nama = row.querySelector('[data-cell="nama"]')?.textContent?.trim() || 'Calon Siswa';
            const jur = row.querySelector('[data-cell="jurusan"]')?.textContent?.trim() || 'SPMB';
            const tgl = row.querySelector('[data-cell="tgl_daftar"]')?.textContent?.trim() || '';
            return {
              no_registrasi: noReg,
              calon_siswa: { nama_lengkap: nama },
              jurusan: { nama_jurusan: jur },
              tgl_daftar: tgl
            };
          });
        }

        if (!Array.isArray(tableApplicants)) {
          tableApplicants = [];
        }

        const normalizeReg = val => String(val || '').trim();

        // Buat peta registrasi pendaftar yang ada di tabel saat ini
        const tableRegMap = new Map();
        tableApplicants.forEach(applicant => {
          const reg = normalizeReg(applicant.no_registrasi);
          if (reg) {
            tableRegMap.set(reg, applicant);
          }
        });

        // 2. HAPUS SEMUA NOTIFIKASI YANG TIDAK ADA DI TABEL
        const currentNotifItems = Array.from(listContainer.querySelectorAll('.notif-item'));
        let deletedCount = 0;
        const newlyDismissed = [];

        currentNotifItems.forEach(item => {
          const itemReg = normalizeReg(item.dataset.reg);
          if (!tableRegMap.has(itemReg)) {
            // Notifikasi ini tidak memiliki data di tabel pendaftar -> Hapus!
            const notifId = item.dataset.notifId;
            if (notifId) newlyDismissed.push(notifId);
            item.remove();
            deletedCount++;
          }
        });

        if (newlyDismissed.length > 0) {
          saveDismissed(newlyDismissed);
        }

        // Un-dismiss notifikasi siswa yang ada di tabel jika sebelumnya sempat dihapus
        const tableNotifIds = Array.from(tableRegMap.keys()).map(reg => `notif-reg-${reg}`);
        if (dismissedIds.length > 0 && tableNotifIds.length > 0) {
          dismissedIds = dismissedIds.filter(id => !tableNotifIds.includes(id));
          try {
            localStorage.setItem(dismissedStorageKey, JSON.stringify(dismissedIds));
          } catch (e) {}
        }

        const now = new Date();

        // 3. Urutkan data pendaftar dari TERBARU ke TERLAMA sebelum diproses
        const sortedApplicants = [...tableApplicants].sort((a, b) => {
          const timeA = a.tgl_daftar ? new Date(a.tgl_daftar).getTime() : 0;
          const timeB = b.tgl_daftar ? new Date(b.tgl_daftar).getTime() : 0;
          if (timeB !== timeA) return timeB - timeA;
          return String(b.no_registrasi || '').localeCompare(String(a.no_registrasi || ''), undefined, { numeric: true });
        });

        // 4. Iterasi siswa dari tabel: update atau buat notifikasi baru
        sortedApplicants.forEach(applicant => {
          const noReg = normalizeReg(applicant.no_registrasi);
          if (!noReg) return;

          const namaSiswa = applicant.calon_siswa?.nama_lengkap || applicant.calonSiswa?.nama_lengkap || applicant.nama_siswa || 'Calon Siswa';
          const rawJurusan = applicant.jurusan?.nama_jurusan || applicant.jurusan_singkat || 'SPMB';
          const jurSingkat = String(rawJurusan).split(' ')[0] || 'SPMB';

          // Hitung periode umur notifikasi berdasarkan tgl_daftar
          let diffDays = 0;
          if (applicant.tgl_daftar) {
            const tgl = new Date(applicant.tgl_daftar);
            if (!isNaN(tgl.getTime())) {
              diffDays = Math.max(0, Math.floor((now - tgl) / (1000 * 60 * 60 * 24)));
            }
          }

          let periodKey = 'baru';
          if (diffDays <= 1) {
            periodKey = 'baru';
          } else if (diffDays <= 7) {
            periodKey = '7_hari';
          } else if (diffDays <= 14) {
            periodKey = 'minggu_lalu';
          } else if (diffDays <= 21) {
            periodKey = '3_minggu_lalu';
          } else {
            periodKey = 'sebulan_terakhir';
          }

          let waktuRelatif = 'Baru saja';
          if (diffDays === 0) {
            waktuRelatif = 'Hari ini';
          } else if (diffDays === 1) {
            waktuRelatif = '1 hari yang lalu';
          } else {
            waktuRelatif = `${diffDays} hari yang lalu`;
          }

          const notifId = `notif-reg-${noReg}`;

          // Periksa apakah notifikasi untuk siswa ini sudah ada di daftar notifikasi
          const existingItem = listContainer.querySelector(`.notif-item[data-reg="${noReg}"]`);

          if (existingItem) {
            // --- KASUS A: REPLACE / UPDATE DATA NOTIFIKASI DENGAN DATA TABEL ---
            const nameEl = existingItem.querySelector('.notif-nama');
            if (nameEl) {
              nameEl.textContent = namaSiswa;
              nameEl.title = namaSiswa;
            }

            const jurEl = existingItem.querySelector('.notif-jurusan');
            if (jurEl) {
              jurEl.textContent = jurSingkat;
            }

            const waktuEl = existingItem.querySelector('.notif-waktu');
            if (waktuEl) {
              waktuEl.textContent = waktuRelatif;
            }

            existingItem.dataset.diffDays = String(diffDays);

            // Jika periode notifikasi berubah, pindahkan ke grup periode yang sesuai
            if (existingItem.dataset.period !== periodKey) {
              existingItem.dataset.period = periodKey;
              const targetGroup = getOrCreatePeriodGroup(periodKey);
              const itemsContainer = targetGroup.querySelector('.notif-period-items') || targetGroup;
              itemsContainer.appendChild(existingItem);
            }
          } else {
            // --- KASUS B: TAMBAH DATA DARI TABEL KE NOTIFIKASI ---
            const group = getOrCreatePeriodGroup(periodKey);
            const itemsContainer = group.querySelector('.notif-period-items') || group;

            const newNotif = document.createElement('div');
            newNotif.className = 'notif-item group relative flex items-center justify-between gap-2 py-1.5 px-2.5 sm:px-3 hover:bg-slate-50 transition-colors cursor-pointer';
            newNotif.dataset.notifId = notifId;
            newNotif.dataset.period = periodKey;
            newNotif.dataset.reg = noReg;
            newNotif.dataset.diffDays = String(diffDays);

            newNotif.innerHTML = `
              <div class="flex items-center gap-2 min-w-0 flex-1">
                <input
                  type="checkbox"
                  class="notif-item-checkbox w-3.5 h-3.5 rounded text-blue-600 border-slate-300 focus:ring-blue-500 cursor-pointer shrink-0"
                  data-id="${notifId}"
                  aria-label="Pilih notifikasi pendaftar ${noReg}"
                />
                <div class="min-w-0 flex-1">
                  <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="font-mono font-extrabold text-blue-700 bg-blue-50/90 px-1.5 py-0.5 rounded-md border border-blue-200/70 text-[10px] sm:text-[11px] tracking-wide shrink-0">
                      #${noReg}
                    </span>
                    <span class="notif-nama text-xs font-bold text-slate-900 truncate leading-tight group-hover:text-blue-600 transition-colors" title="${namaSiswa}">
                      ${namaSiswa}
                    </span>
                    <span class="notif-unread-dot w-1.5 h-1.5 rounded-full bg-blue-600 shrink-0" title="Belum dibuka admin"></span>
                  </div>
                  <div class="flex items-center gap-1.5 text-[10px] text-slate-400 mt-0.5 font-medium">
                    <span class="notif-jurusan inline-flex text-[9px] font-bold px-1 py-0.5 rounded bg-slate-100 text-slate-700 shrink-0">
                      ${jurSingkat}
                    </span>
                    <span>•</span>
                    <span class="notif-waktu">${waktuRelatif}</span>
                  </div>
                </div>
              </div>
              <button
                type="button"
                class="notif-open-edit-btn opacity-0 group-hover:opacity-100 focus:opacity-100 inline-flex items-center justify-center w-6 h-6 rounded-md text-slate-700 hover:text-white bg-slate-100 hover:bg-slate-900 border border-slate-200 hover:border-slate-900 transition-all duration-200 shrink-0 cursor-pointer active:scale-95"
                data-reg="${noReg}"
                title="Buka detail pendaftaran & verifikasi"
                aria-label="Buka detail pendaftaran ${namaSiswa}"
              >
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
              </button>
            `;

            itemsContainer.appendChild(newNotif);
          }
        });

        // 5. Urutkan seluruh grup periode dan item strictly dari TERBARU ke TERLAMA
        sortPeriodGroupsAndItems();

        // 6. Bersihkan grup periode yang kosong setelah sinkronisasi
        checkEmptyPeriodGroups();

        // Update state notifikasi (badge, checkbox select all, empty states, filter)
        renderUnreadIndicators();
        updateState();
        applyFilter(currentFilter);

        // Tampilkan toast konfirmasi hanya jika silent = false
        if (!silent) {
          const totalSync = tableRegMap.size;
          const toastMsg = `Notifikasi disinkronkan: ${totalSync} data sesuai tabel` + (deletedCount > 0 ? `, ${deletedCount} dihapus` : '');
          try {
            if (typeof window.showToast === 'function') {
              window.showToast(toastMsg, 'success');
            }
          } catch (toastErr) {}
        }
      } catch (err) {
        console.error('Error saat sinkronisasi otomatis notifikasi:', err);
      }
    }

    // Expose sinkronisasi global agar bisa dipanggil saat data pendaftar diubah di tabel/drawer
    window.syncNotificationsWithTable = function(silent = true) {
      syncTableToNotifications(silent);
    };

    // Jalankan sinkronisasi awal secara otomatis saat halaman dimuat
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', () => syncTableToNotifications(true));
    } else {
      setTimeout(() => syncTableToNotifications(true), 50);
    }

    // Initial state: urutkan dari terbaru ke terlama
    sortPeriodGroupsAndItems();
    checkEmptyPeriodGroups();
    renderUnreadIndicators();
    applyFilter('all');
  })();
</script>
