<!-- SIDEBAR COMPONENT (DESKTOP FIXED + MOBILE DRAWER) -->
<aside
  id="admin-sidebar"
  aria-label="Sidebar Navigasi Panitia"
  class="fixed inset-y-0 left-0 z-40 w-64 bg-[#0f172a] border-r border-slate-800 flex flex-col justify-between transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0 no-print shadow-[4px_0_24px_-2px_rgba(15,23,42,0.06),_1px_0_6px_-1px_rgba(15,23,42,0.04)]"
>
  <!-- Top Portion: Brand & Nav -->
  <div class="flex-1 flex flex-col min-h-0">
    <!-- Sidebar Brand Header (No background, No hr line) -->
    <div class="px-3.5 py-4 flex items-center justify-between gap-1.5 h-16 shrink-0">
      <!-- Left: School Logo & School Name (Matching landing page style) -->
      <a
        href="{{ route('admin.dashboard') }}"
        onclick="handleAdminNavClick(event, 'beranda')"
        class="flex items-center gap-2.5 group min-w-0"
        title="Dasbor SPMB SMK TI Bali Global Badung"
      >
        <div class="w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center shrink-0 transition-transform duration-200 group-hover:scale-105">
          <img
            src="{{ asset('assets/img/alogo.png') }}"
            alt="Logo SMK TI Bali Global Badung"
            class="w-full h-full object-contain"
          />
        </div>
        <div class="flex flex-col leading-none">
          <span
            class="tracking-widest text-xs font-extrabold text-[#00A8E8] whitespace-nowrap"
            style="line-height: 1.1"
          >
            SMK TI
          </span>
          <span
            class="text-[11px] sm:text-xs font-bold text-white -mt-0.5 whitespace-nowrap"
            style="line-height: 1.15"
          >
            Bali Global Badung
          </span>
        </div>
      </a>

      <!-- Right: Close Sidebar Action -->
      <div class="flex items-center shrink-0">
        <!-- Desktop Close Button with Tooltip (hidden on mobile) -->
        <div class="relative group hidden lg:flex items-center">
          <button
            type="button"
            id="admin-sidebar-collapse-btn"
            onclick="toggleAdminSidebar(false)"
            aria-label="Tutup Sidebar"
            class="w-8 h-8 flex items-center justify-center text-slate-200 hover:text-white hover:bg-white/10 rounded-full transition cursor-pointer outline-none focus:ring-0"
          >
            <!-- Panel Left Close SVG -->
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect width="18" height="18" x="3" y="3" rx="2" />
              <path d="M9 3v18" />
              <path d="m16 15-3-3 3-3" />
            </svg>
          </button>

          <!-- Custom Tooltip "Tutup Sidebar" to the left -->
          <div
            role="tooltip"
            class="pointer-events-none absolute right-full mr-2.5 top-1/2 -translate-y-1/2 px-2.5 py-1.5 bg-slate-900 text-white text-xs font-semibold rounded-lg shadow-xl border border-slate-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 whitespace-nowrap z-50 flex items-center"
          >
            <span>Tutup Sidebar</span>
            <div class="absolute -right-1 top-1/2 -translate-y-1/2 w-2 h-2 bg-slate-900 border-r border-t border-slate-700 rotate-45"></div>
          </div>
        </div>

        <!-- Mobile Close Button (Hidden on Desktop) -->
        <button
          type="button"
          id="admin-sidebar-close-btn"
          onclick="toggleAdminSidebar(false)"
          aria-label="Tutup Menu Sidebar"
          title="Tutup Menu Sidebar"
          class="lg:hidden w-8 h-8 flex items-center justify-center text-slate-200 hover:text-white hover:bg-white/10 rounded-full transition cursor-pointer outline-none focus:ring-0"
        >
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>
    </div>

    <!-- Scrollable Nav Portion -->
    <div class="flex-1 flex flex-col min-h-0 overflow-y-auto sidebar-nav-container">
      <!-- Navigation Links -->
      <nav class="p-3.5 space-y-1.5" aria-label="Menu Dasbor">
        <div class="px-3.5 pt-2 pb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-500">
          Halaman Dasbor
        </div>

        @php
          $currentPage = $activePage ?? 'beranda';
          if ($currentPage === 'gelombang') {
            $currentPage = 'office';
          }

          $sidebarNavItems = [
            [
              'view' => 'beranda',
              'href' => route('admin.beranda'),
              'label' => 'Beranda',
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" /><polyline stroke-linecap="round" stroke-linejoin="round" points="9 22 9 12 15 12 15 22" />',
            ],
            [
              'view' => 'office',
              'href' => route('admin.office'),
              'label' => 'Gelombang',
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />',
            ],
            [
              'view' => 'status-berkas',
              'href' => route('admin.status-berkas'),
              'label' => 'Status Berkas',
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
            ],
            [
              'view' => 'kuota-rombel',
              'href' => route('admin.kuota-rombel'),
              'label' => 'Kuota Rombel',
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M22 10v6M2 10l10-5 10 5-10 5z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 12v5c3 3 9 3 12 0v-5" />',
            ],
            [
              'view' => 'tabel-jurusan',
              'href' => route('admin.tabel-jurusan'),
              'label' => 'Tabel Pendaftar',
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" /><path stroke-linecap="round" stroke-linejoin="round" d="M22 21v-2a4 4 0 0 0-3-3.87" /><path stroke-linecap="round" stroke-linejoin="round" d="M16 3.13a4 4 0 0 1 0 7.75" />',
            ],
            [
              'view' => 'daftar-ulang',
              'href' => route('admin.daftar-ulang'),
              'label' => 'Daftar Ulang',
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-6 9 2 2 4-4" />',
            ],
            [
              'view' => 'keuangan',
              'href' => route('admin.keuangan'),
              'label' => 'Keuangan',
              'icon' => '<rect width="20" height="14" x="2" y="5" rx="2" /><line x1="2" x2="22" y1="10" y2="10" />',
            ],
            [
              'view' => 'log-aktivitas',
              'href' => route('admin.log-aktivitas'),
              'label' => 'Log Aktivitas',
              'icon' => '<circle cx="12" cy="12" r="10" /><polyline points="12 6 12 12 16 14" />',
            ],
          ];
        @endphp

        @foreach ($sidebarNavItems as $item)
          @php $isActive = $currentPage === $item['view']; @endphp
          <a
            href="{{ $item['href'] }}"
            onclick="handleAdminNavClick(event, '{{ $item['view'] }}')"
            data-admin-view-btn="{{ $item['view'] }}"
            @class([
              'sidebar-nav-btn relative w-full flex items-center px-3.5 py-2.5 rounded-xl text-sm transition-all duration-200 group cursor-pointer',
              'bg-blue-500/20 text-white font-semibold' => $isActive,
              'text-slate-400 hover:text-white hover:bg-white/5 font-medium' => ! $isActive,
            ])
          >
            <!-- Active Indicator Pill on left edge of sidebar -->
            <span @class([
              'sidebar-active-indicator absolute -left-3.5 top-1/2 -translate-y-1/2 w-1 sm:w-1.5 h-5 rounded-r-full bg-blue-500 transition-all duration-200',
              'opacity-100' => $isActive,
              'opacity-0 pointer-events-none' => ! $isActive,
            ])></span>

            <div class="flex items-center gap-3 min-w-0">
              <svg @class([
                'sidebar-icon w-4 h-4 fill-none stroke-current shrink-0 transition-transform duration-200 group-hover:scale-110',
                'text-blue-500' => $isActive,
                'text-slate-400 group-hover:text-slate-200' => ! $isActive,
              ]) viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                {!! $item['icon'] !!}
              </svg>
              <span @class([
                'sidebar-label truncate',
                'text-white font-semibold' => $isActive,
                'text-slate-300 group-hover:text-white font-medium' => ! $isActive,
              ])>{{ $item['label'] }}</span>
            </div>
          </a>
        @endforeach
      </nav>
    </div>
  </div>

  <!-- Bottom Portion: Settings Trigger -->
  <div class="p-3 border-t border-slate-800/80 shrink-0 bg-black/20 flex flex-col gap-1">
    <button
      type="button"
      id="admin-settings-trigger-btn"
      onclick="openAdminThemeModal()"
      class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-white/10 transition-all duration-200 group cursor-pointer border border-transparent hover:border-slate-700/50"
      title="Buka Pengaturan Tampilan & Tema Admin"
      aria-haspopup="dialog"
    >
      <div class="flex items-center gap-2.5 min-w-0">
        <div class="w-7 h-7 rounded-lg bg-blue-500/15 text-blue-400 flex items-center justify-center shrink-0 group-hover:bg-blue-500 group-hover:text-white transition-all duration-200">
          <svg class="w-4 h-4 group-hover:rotate-45 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>
            <circle cx="12" cy="12" r="3"/>
          </svg>
        </div>
        <div class="flex flex-col text-left leading-tight truncate">
          <span class="text-xs font-semibold text-slate-200 group-hover:text-white">Pengaturan</span>
          <span class="text-[10px] text-slate-400 truncate" id="admin-settings-btn-status">Tema & Tampilan</span>
        </div>
      </div>

      <div class="flex items-center gap-1.5 shrink-0 pl-1">
        <span id="admin-settings-btn-badge" class="px-1.5 py-0.5 rounded-md text-[10px] font-semibold bg-slate-800 text-slate-300 border border-slate-700/60 flex items-center gap-1">
          <span id="admin-settings-theme-dot" class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
          <span id="admin-settings-mode-text">Dark</span>
        </span>
      </div>
    </button>
  </div>

</aside>

<!-- Mobile Backdrop -->
<div
  id="admin-sidebar-backdrop"
  onclick="toggleAdminSidebar(false)"
  class="fixed inset-0 z-30 bg-slate-900/50 transition-opacity duration-300 opacity-0 pointer-events-none lg:hidden no-print"
  aria-hidden="true"
></div>
