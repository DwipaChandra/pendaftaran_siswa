<!-- ============================================================== -->
<!-- PENGATURAN TAMPILAN ADMIN (POPUP MODAL & THEME ENGINE)        -->
<!-- Tanpa Database, Disimpan Lokal di Browser (localStorage)       -->
<!-- ============================================================== -->

<style id="admin-theme-styles">
  /* 1. CSS Custom Properties Default */
  :root {
    --admin-sidebar-bg: #0f172a;
    --admin-bg-color: #f8fafc;
    --admin-card-bg: #ffffff;
    --admin-header-bg: #ffffff;
    --admin-border-color: #e2e8f0;
    --admin-text-main: #0f172a;
    --admin-text-muted: #64748b;
    --admin-surface-muted: #f8fafc;
    --admin-surface-subtle: #f1f5f9;
    --admin-input-bg: #ffffff;
  }

  /* Target Sidebar Background */
  #admin-sidebar {
    background-color: var(--admin-sidebar-bg, #0f172a) !important;
  }

  /* Target Main Background */
  .min-h-screen.bg-slate-50,
  #admin-main-content {
    background-color: var(--admin-bg-color, #f8fafc) !important;
  }

  /* 2. DARK MODE STYLES (SCOPED TO ADMIN) */
  html.admin-dark {
    --admin-bg-color: #111827;
    --admin-card-bg: #1b2635;
    --admin-header-bg: #182332;
    --admin-border-color: #334155;
    --admin-text-main: #f1f5f9;
    --admin-text-muted: #a3afbf;
    --admin-surface-muted: #182332;
    --admin-surface-subtle: #243244;
    --admin-input-bg: #131d2b;
    color-scheme: dark;
  }

  html.admin-dark body,
  html.admin-dark .min-h-screen.bg-slate-50,
  html.admin-dark #admin-main-content {
    background-color: var(--admin-bg-color, #0b0f19) !important;
    color: var(--admin-text-main);
  }

  /* Header & Top Bar */
  html.admin-dark header.bg-white,
  html.admin-dark #admin-main-content > header {
    background-color: var(--admin-header-bg, #111827) !important;
    border-color: var(--admin-border-color, #1e293b) !important;
  }

  /* Cards, Panels, & Surfaces */
  html.admin-dark .bg-white:not([data-keep-white]):not(.no-dark-override):not(#printable-id-card):not(#printable-id-card *) {
    background-color: var(--admin-card-bg, #111827) !important;
    border-color: var(--admin-border-color, #1e293b) !important;
  }

  html.admin-dark [class*="bg-slate-50"]:not(#admin-sidebar):not(.no-dark-override),
  html.admin-dark [class*="bg-gray-50"]:not(.no-dark-override) {
    background-color: var(--admin-surface-muted) !important;
  }

  html.admin-dark [class*="bg-slate-100"]:not(.no-dark-override),
  html.admin-dark [class*="bg-gray-100"]:not(.no-dark-override),
  html.admin-dark [class*="bg-slate-200"]:not(.no-dark-override),
  html.admin-dark [class*="bg-gray-200"]:not(.no-dark-override) {
    background-color: var(--admin-surface-subtle) !important;
  }

  html.admin-dark [class*="bg-blue-50"],
  html.admin-dark [class*="bg-sky-50"],
  html.admin-dark [class*="bg-cyan-50"],
  html.admin-dark [class*="bg-blue-100"],
  html.admin-dark [class*="bg-sky-100"],
  html.admin-dark [class*="bg-cyan-100"] {
    background-color: #1b3045 !important;
  }

  html.admin-dark [class*="bg-indigo-50"],
  html.admin-dark [class*="bg-violet-50"],
  html.admin-dark [class*="bg-purple-50"],
  html.admin-dark [class*="bg-indigo-100"],
  html.admin-dark [class*="bg-violet-100"],
  html.admin-dark [class*="bg-purple-100"] {
    background-color: #29283f !important;
  }

  html.admin-dark [class*="bg-emerald-50"],
  html.admin-dark [class*="bg-green-50"],
  html.admin-dark [class*="bg-teal-50"],
  html.admin-dark [class*="bg-lime-50"],
  html.admin-dark [class*="bg-emerald-100"],
  html.admin-dark [class*="bg-green-100"],
  html.admin-dark [class*="bg-teal-100"],
  html.admin-dark [class*="bg-lime-100"] {
    background-color: #1c352f !important;
  }

  html.admin-dark [class*="bg-rose-50"],
  html.admin-dark [class*="bg-red-50"],
  html.admin-dark [class*="bg-pink-50"],
  html.admin-dark [class*="bg-rose-100"],
  html.admin-dark [class*="bg-red-100"],
  html.admin-dark [class*="bg-pink-100"] {
    background-color: #3a2930 !important;
  }

  html.admin-dark [class*="bg-amber-50"],
  html.admin-dark [class*="bg-yellow-50"],
  html.admin-dark [class*="bg-orange-50"],
  html.admin-dark [class*="bg-amber-100"],
  html.admin-dark [class*="bg-yellow-100"],
  html.admin-dark [class*="bg-orange-100"] {
    background-color: #3a3326 !important;
  }

  /* Borders & Dividers */
  html.admin-dark [class*="border-slate-200"],
  html.admin-dark [class*="border-slate-100"],
  html.admin-dark [class*="border-slate-300"],
  html.admin-dark [class*="border-gray-200"],
  html.admin-dark [class*="border-gray-100"],
  html.admin-dark [class*="border-sky-100"],
  html.admin-dark [class*="border-cyan-100"],
  html.admin-dark [class*="border-blue-100"],
  html.admin-dark [class*="border-indigo-100"],
  html.admin-dark [class*="border-violet-100"],
  html.admin-dark [class*="border-purple-100"],
  html.admin-dark [class*="border-emerald-100"],
  html.admin-dark [class*="border-green-100"],
  html.admin-dark [class*="border-teal-100"],
  html.admin-dark [class*="border-lime-100"],
  html.admin-dark [class*="border-rose-100"],
  html.admin-dark [class*="border-red-100"],
  html.admin-dark [class*="border-pink-100"],
  html.admin-dark [class*="border-amber-100"],
  html.admin-dark [class*="border-yellow-100"],
  html.admin-dark [class*="border-orange-100"] {
    border-color: var(--admin-border-color, #1e293b) !important;
  }

  html.admin-dark .divide-slate-200 > :not([hidden]) ~ :not([hidden]),
  html.admin-dark .divide-slate-100 > :not([hidden]) ~ :not([hidden]) {
    border-color: var(--admin-border-color, #1e293b) !important;
  }

  /* Typography */
  html.admin-dark .text-slate-900,
  html.admin-dark .text-slate-800,
  html.admin-dark .text-slate-950,
  html.admin-dark .text-gray-900,
  html.admin-dark .text-gray-800 {
    color: var(--admin-text-main) !important;
  }

  html.admin-dark .text-slate-700,
  html.admin-dark .text-slate-600 {
    color: #d2dae5 !important;
  }

  html.admin-dark .text-slate-500,
  html.admin-dark .text-slate-400 {
    color: var(--admin-text-muted) !important;
  }

  /* Form Inputs, Selects, & Textareas */
  html.admin-dark input:not([type="checkbox"]):not([type="radio"]):not([type="color"]):not([type="file"]),
  html.admin-dark select,
  html.admin-dark textarea {
    background-color: var(--admin-input-bg) !important;
    border-color: var(--admin-border-color) !important;
    color: var(--admin-text-main) !important;
  }

  html.admin-dark input::placeholder,
  html.admin-dark textarea::placeholder {
    color: #8b98aa !important;
  }

  html.admin-dark [class*="hover:bg-slate-50"]:hover,
  html.admin-dark [class*="hover:bg-gray-50"]:hover {
    background-color: var(--admin-surface-subtle) !important;
  }

  /* Tables in Dark Mode */
  html.admin-dark table thead,
  html.admin-dark table thead th {
    background-color: #202c3b !important;
    color: var(--admin-text-muted) !important;
    border-color: var(--admin-border-color, #1e293b) !important;
  }

  html.admin-dark table tbody tr {
    background-color: var(--admin-card-bg, #1b2635) !important;
    border-color: var(--admin-border-color, #1e293b) !important;
  }

  html.admin-dark table tbody tr:hover {
    background-color: #263448 !important;
  }

  /* Dropdowns & Popovers */
  html.admin-dark #admin-nav-user-dropdown,
  html.admin-dark #admin-notification-panel,
  html.admin-dark [data-admin-dropdown] {
    background-color: var(--admin-card-bg) !important;
    border-color: var(--admin-border-color) !important;
    color: var(--admin-text-main) !important;
  }

  /* Status Work / Break Text in Dark Mode */
  html.admin-dark #nav-work-status-text.status-work {
    color: #34d399 !important;
  }

  html.admin-dark #nav-work-status-text.status-break {
    color: #fbbf24 !important;
  }

  /* Drawers & Backdrops in Dark Mode */
  html.admin-dark #admin-student-drawer,
  html.admin-dark #admin-student-drawer > div {
    background-color: #182332 !important;
    color: var(--admin-text-main) !important;
  }

  html.admin-dark #admin-student-drawer .drawer-data-card {
    background-color: #243244 !important;
    border-color: var(--admin-border-color) !important;
  }

  /* Modal Settings Popup in Dark Mode */
  html.admin-dark #admin-theme-modal-box {
    background-color: var(--admin-card-bg) !important;
    border-color: var(--admin-border-color) !important;
    color: var(--admin-text-main) !important;
  }

  html.admin-dark #admin-theme-modal-box .theme-section-card {
    background-color: #182332 !important;
    border-color: var(--admin-border-color) !important;
  }

  html.admin-dark .kpi-card-front,
  html.admin-dark .kpi-card-back {
    background-image: none !important;
    color: var(--admin-text-main);
  }

  html.admin-dark .kpi-card-front[data-kpi-tone="blue"],
  html.admin-dark .kpi-card-back[data-kpi-tone="blue"] {
    background-color: #1c2b3d !important;
    border-color: #344a63 !important;
  }

  html.admin-dark .kpi-card-front[data-kpi-tone="amber"],
  html.admin-dark .kpi-card-back[data-kpi-tone="amber"] {
    background-color: #332d22 !important;
    border-color: #55472f !important;
  }

  html.admin-dark .kpi-card-front[data-kpi-tone="sky"],
  html.admin-dark .kpi-card-back[data-kpi-tone="sky"] {
    background-color: #1c3039 !important;
    border-color: #34515d !important;
  }

  html.admin-dark .kpi-card-front[data-kpi-tone="orange"],
  html.admin-dark .kpi-card-back[data-kpi-tone="orange"] {
    background-color: #352a22 !important;
    border-color: #594331 !important;
  }

  html.admin-dark .kpi-card-front[data-kpi-tone="rose"],
  html.admin-dark .kpi-card-back[data-kpi-tone="rose"] {
    background-color: #35242c !important;
    border-color: #573742 !important;
  }

  html.admin-dark .kpi-card-front[data-kpi-tone="emerald"],
  html.admin-dark .kpi-card-back[data-kpi-tone="emerald"] {
    background-color: #1d302b !important;
    border-color: #35534a !important;
  }

  html.admin-dark .kpi-card-front > svg,
  html.admin-dark .kpi-card-back > svg {
    opacity: 0.07 !important;
  }

  html.admin-dark .kpi-card-front [class*="text-blue-"],
  html.admin-dark .kpi-card-back [class*="text-blue-"] {
    color: #93c5fd !important;
  }

  html.admin-dark .kpi-card-front [class*="text-amber-"],
  html.admin-dark .kpi-card-back [class*="text-amber-"] {
    color: #fcd34d !important;
  }

  html.admin-dark .kpi-card-front [class*="text-sky-"],
  html.admin-dark .kpi-card-back [class*="text-sky-"] {
    color: #7dd3fc !important;
  }

  html.admin-dark .kpi-card-front [class*="text-orange-"],
  html.admin-dark .kpi-card-back [class*="text-orange-"] {
    color: #fdba74 !important;
  }

  html.admin-dark .kpi-card-front [class*="text-rose-"],
  html.admin-dark .kpi-card-back [class*="text-rose-"] {
    color: #fda4af !important;
  }

  html.admin-dark .kpi-card-front [class*="text-emerald-"],
  html.admin-dark .kpi-card-back [class*="text-emerald-"] {
    color: #6ee7b7 !important;
  }

  html.admin-dark .kpi-card-front [class*="bg-blue-100"],
  html.admin-dark .kpi-card-back [class*="bg-blue-100"] {
    background-color: #263e57 !important;
    border-color: #405c7a !important;
  }

  html.admin-dark .kpi-card-front [class*="bg-amber-100"],
  html.admin-dark .kpi-card-back [class*="bg-amber-100"] {
    background-color: #493b25 !important;
    border-color: #665334 !important;
  }

  html.admin-dark .kpi-card-front [class*="bg-sky-100"],
  html.admin-dark .kpi-card-back [class*="bg-sky-100"] {
    background-color: #25424d !important;
    border-color: #3b5d6a !important;
  }

  html.admin-dark .kpi-card-front [class*="bg-orange-100"],
  html.admin-dark .kpi-card-back [class*="bg-orange-100"] {
    background-color: #4b3625 !important;
    border-color: #694a31 !important;
  }

  html.admin-dark .kpi-card-front [class*="bg-rose-100"],
  html.admin-dark .kpi-card-back [class*="bg-rose-100"] {
    background-color: #4a2e39 !important;
    border-color: #684151 !important;
  }

  html.admin-dark .kpi-card-front [class*="bg-emerald-100"],
  html.admin-dark .kpi-card-back [class*="bg-emerald-100"] {
    background-color: #26453b !important;
    border-color: #3b6555 !important;
  }

  /* 3. COMPACT DENSITY STYLES (SCOPED TO ADMIN) */
  html.admin-compact table th,
  html.admin-compact table td {
    padding-top: 0.45rem !important;
    padding-bottom: 0.45rem !important;
    padding-left: 0.75rem !important;
    padding-right: 0.75rem !important;
    font-size: 0.8125rem !important;
  }

  html.admin-compact .admin-stat-card,
  html.admin-compact .admin-compact-p-shrink {
    padding: 0.875rem 1rem !important;
  }

  html.admin-compact .space-y-6 > :not([hidden]) ~ :not([hidden]) {
    margin-top: 1rem !important;
  }

  html.admin-compact .gap-6 {
    gap: 1rem !important;
  }

  html.admin-compact input:not([type="checkbox"]):not([type="radio"]):not([type="color"]),
  html.admin-compact select {
    height: 2.25rem !important;
    font-size: 0.8125rem !important;
    padding-top: 0.25rem !important;
    padding-bottom: 0.25rem !important;
  }

  /* 4. MODAL ANIMATION */
  #admin-theme-modal.is-open {
    display: flex !important;
    opacity: 1 !important;
    pointer-events: auto !important;
  }

  #admin-theme-modal.is-open #admin-theme-modal-box {
    transform: scale(1) !important;
    opacity: 1 !important;
  }
</style>

<!-- POPUP MODAL DIALOG -->
<div
  id="admin-theme-modal"
  class="fixed inset-0 z-[9999] bg-slate-950/60 hidden opacity-0 pointer-events-none items-center justify-center p-3 sm:p-4 overflow-y-auto transition-all duration-200"
  role="dialog"
  aria-modal="true"
  aria-labelledby="admin-theme-modal-title"
  onclick="handleThemeBackdropClick(event)"
>
  <div
    id="admin-theme-modal-box"
    class="relative w-full max-w-xl bg-white rounded-3xl border border-slate-200/90 shadow-2xl p-5 sm:p-6 text-left transform transition-all duration-200 scale-95 opacity-0 my-auto"
    onclick="event.stopPropagation()"
  >
    <!-- Modal Header -->
    <div class="flex items-start justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
          <!-- Sliders / Theme SVG Icon -->
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="4" y1="21" x2="4" y2="14" />
            <line x1="4" y1="10" x2="4" y2="3" />
            <line x1="12" y1="21" x2="12" y2="12" />
            <line x1="12" y1="8" x2="12" y2="3" />
            <line x1="20" y1="21" x2="20" y2="16" />
            <line x1="20" y1="12" x2="20" y2="3" />
            <line x1="1" y1="14" x2="7" y2="14" />
            <line x1="9" y1="8" x2="15" y2="8" />
            <line x1="17" y1="16" x2="23" y2="16" />
          </svg>
        </div>
        <div>
          <h2 id="admin-theme-modal-title" class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
            Pereferensi Tema
          </h2>
        </div>
      </div>

      <!-- Close Button (X) -->
      <button
        type="button"
        onclick="closeAdminThemeModal()"
        class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
        aria-label="Tutup Pengaturan"
      >
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
    </div>

    <!-- Modal Body (Scrollable if screen small) -->
    <div class="mt-4 space-y-5 max-h-[72vh] overflow-y-auto pr-1">
      
      <!-- 1. MODE WARNA & KERAPATAN TAMPILAN (SEJAJAR DALAM 1 GRID) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        
        <!-- Mode Warna Card -->
        <div class="flex flex-col justify-between p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60 gap-3">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-500 dark:bg-indigo-500/15 dark:text-indigo-400 flex items-center justify-center shrink-0">
              <span id="theme-mode-indicator-icon" class="flex items-center justify-center">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="4" />
                  <path d="M12 2v2" />
                  <path d="M12 20v2" />
                  <path d="m4.93 4.93 1.41 1.41" />
                  <path d="m17.66 17.66 1.41 1.41" />
                  <path d="M2 12h2" />
                  <path d="M20 12h2" />
                  <path d="m6.34 17.66-1.41 1.41" />
                  <path d="m19.07 4.93-1.41 1.41" />
                </svg>
              </span>
            </div>
            <div class="min-w-0">
              <div class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200">Mode Warna</div>
              <p class="text-[11px] text-slate-500 truncate" id="theme-mode-desc-text">Mode Terang (White)</p>
            </div>
          </div>

          <!-- Small Segmented Toggle -->
          <div class="grid grid-cols-2 p-1 rounded-xl bg-slate-200/70 dark:bg-slate-900 border border-slate-300/50 dark:border-slate-700/60 text-xs">
            <button
              type="button"
              id="theme-mode-light-btn"
              onclick="setAdminMode('light')"
              class="flex items-center justify-center gap-1.5 px-2 py-1.5 rounded-lg text-xs font-semibold transition-all duration-150 cursor-pointer bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400"
            >
              <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="4" />
                <path d="M12 2v2" />
                <path d="M12 20v2" />
                <path d="m4.93 4.93 1.41 1.41" />
                <path d="m17.66 17.66 1.41 1.41" />
                <path d="M2 12h2" />
                <path d="M20 12h2" />
                <path d="m6.34 17.66-1.41 1.41" />
                <path d="m19.07 4.93-1.41 1.41" />
              </svg>
              <span>Terang</span>
            </button>
            <button
              type="button"
              id="theme-mode-dark-btn"
              onclick="setAdminMode('dark')"
              class="flex items-center justify-center gap-1.5 px-2 py-1.5 rounded-lg text-xs font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 transition-all duration-150 cursor-pointer"
            >
              <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z" />
              </svg>
              <span>Gelap</span>
            </button>
          </div>
        </div>

        <!-- Kerapatan Tampilan Card -->
        <div class="flex flex-col justify-between p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60 gap-3">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-500 dark:bg-blue-500/15 dark:text-blue-400 flex items-center justify-center shrink-0">
              <span id="theme-density-indicator-icon" class="flex items-center justify-center">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect width="18" height="18" x="3" y="3" rx="2" />
                  <path d="M3 12h18" />
                </svg>
              </span>
            </div>
            <div class="min-w-0">
              <div class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200">Kerapatan Tampilan</div>
              <p class="text-[11px] text-slate-500 truncate" id="theme-density-desc-text">Standar (Comfortable)</p>
            </div>
          </div>

          <!-- Small Segmented Toggle -->
          <div class="grid grid-cols-2 p-1 rounded-xl bg-slate-200/70 dark:bg-slate-900 border border-slate-300/50 dark:border-slate-700/60 text-xs">
            <button
              type="button"
              id="theme-density-comfortable-btn"
              onclick="setAdminDensity('comfortable')"
              class="flex items-center justify-center gap-1.5 px-2 py-1.5 rounded-lg text-xs font-semibold transition-all duration-150 cursor-pointer bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400"
            >
              <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="18" height="18" x="3" y="3" rx="2" />
                <path d="M3 12h18" />
              </svg>
              <span>Comfy</span>
            </button>
            <button
              type="button"
              id="theme-density-compact-btn"
              onclick="setAdminDensity('compact')"
              class="flex items-center justify-center gap-1.5 px-2 py-1.5 rounded-lg text-xs font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 transition-all duration-150 cursor-pointer"
            >
              <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="18" height="18" x="3" y="3" rx="2" />
                <path d="M3 9h18" />
                <path d="M3 15h18" />
              </svg>
              <span>Compact</span>
            </button>
          </div>
        </div>

      </div>

      <!-- 2. PILIHAN PRESET TEMA (WARNA SIDEBAR & BG) -->
      @php
        $themePresets = [
          ['key' => 'default', 'label' => 'Midnight Slate', 'sidebar' => 'bg-[#0f172a]', 'bg' => 'bg-[#f8fafc]', 'dot' => 'bg-[#0ea5e9]'],
          ['key' => 'ocean', 'label' => 'Deep Ocean', 'sidebar' => 'bg-[#0b1c36]', 'bg' => 'bg-[#f0f4f9]', 'dot' => 'bg-[#0284c7]'],
          ['key' => 'emerald', 'label' => 'Forest Emerald', 'sidebar' => 'bg-[#06281e]', 'bg' => 'bg-[#f0f7f4]', 'dot' => 'bg-[#10b981]'],
          ['key' => 'amethyst', 'label' => 'Royal Amethyst', 'sidebar' => 'bg-[#1a1033]', 'bg' => 'bg-[#f6f3fb]', 'dot' => 'bg-[#8b5cf6]'],
          ['key' => 'crimson', 'label' => 'Crimson Wine', 'sidebar' => 'bg-[#290f19]', 'bg' => 'bg-[#faf4f6]', 'dot' => 'bg-[#f43f5e]'],
          ['key' => 'charcoal', 'label' => 'Cyber Charcoal', 'sidebar' => 'bg-[#141416]', 'bg' => 'bg-[#f5f5f7]', 'dot' => 'bg-[#64748b]'],
        ];
      @endphp
      <div>
        <div class="flex items-center justify-between mb-2">
          <label class="text-xs font-bold uppercase tracking-wider text-slate-500">
            Pilihan Tema Warna
          </label>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5" id="theme-presets-container">
          @foreach ($themePresets as $preset)
            <button
              type="button"
              onclick="setAdminThemePreset('{{ $preset['key'] }}')"
              data-preset="{{ $preset['key'] }}"
              class="theme-preset-card p-2.5 rounded-xl border-2 text-left transition-all duration-150 cursor-pointer flex flex-col gap-2 relative"
            >
              <!-- Miniature Preview Mockup -->
              <div class="w-full h-8 rounded-lg border border-slate-700/30 overflow-hidden flex">
                <div class="w-1/3 h-full {{ $preset['sidebar'] }}"></div>
                <div class="w-2/3 h-full {{ $preset['bg'] }} flex items-center justify-center">
                  <div class="w-2 h-2 rounded-full {{ $preset['dot'] }}"></div>
                </div>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-700 dark:text-slate-200">{{ $preset['label'] }}</span>
                <span class="preset-check hidden text-blue-600 text-xs font-bold">✓</span>
              </div>
            </button>
          @endforeach
        </div>
      </div>

    </div>

  </div>
</div>

<!-- ============================================================== -->
<!-- JAVASCRIPT THEME & PREFERENCE ENGINE (CLIENT-SIDE ONLY)       -->
<!-- ============================================================== -->
<script>
  (function () {
    const STORAGE_KEY = 'admin_theme_settings';

    // Preset Configurations
    const THEME_PRESETS = {
      default: {
        name: 'Midnight Slate',
        sidebarBg: '#0f172a',
        lightBg: '#f8fafc',
        darkBg: '#111827',
        cardBg: '#1b2635',
        accentColor: '#0ea5e9'
      },
      ocean: {
        name: 'Deep Ocean',
        sidebarBg: '#0b1c36',
        lightBg: '#f0f4f9',
        darkBg: '#101b2b',
        cardBg: '#1b2b3f',
        accentColor: '#0284c7'
      },
      emerald: {
        name: 'Forest Emerald',
        sidebarBg: '#06281e',
        lightBg: '#f0f7f4',
        darkBg: '#101d19',
        cardBg: '#1c332b',
        accentColor: '#10b981'
      },
      amethyst: {
        name: 'Royal Amethyst',
        sidebarBg: '#1a1033',
        lightBg: '#f6f3fb',
        darkBg: '#191629',
        cardBg: '#2a243b',
        accentColor: '#8b5cf6'
      },
      crimson: {
        name: 'Crimson Wine',
        sidebarBg: '#290f19',
        lightBg: '#faf4f6',
        darkBg: '#1e151b',
        cardBg: '#35242b',
        accentColor: '#f43f5e'
      },
      charcoal: {
        name: 'Cyber Charcoal',
        sidebarBg: '#141416',
        lightBg: '#f5f5f7',
        darkBg: '#171719',
        cardBg: '#242426',
        accentColor: '#64748b'
      }
    };

    // Default Settings
    const DEFAULT_SETTINGS = {
      mode: 'light',
      preset: 'default',
      density: 'comfortable',
      sidebarBg: '#0f172a',
      mainBg: '#f8fafc',
      cardBg: '#111827'
    };

    // Load or Initialize Settings
    function getSettings() {
      try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (raw) {
          return Object.assign({}, DEFAULT_SETTINGS, JSON.parse(raw));
        }
      } catch (e) {}
      return Object.assign({}, DEFAULT_SETTINGS);
    }

    function saveSettings(settings) {
      try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(settings));
      } catch (e) {}
    }

    // Apply Settings to DOM
    window.applyAdminTheme = function () {
      const s = getSettings();
      const html = document.documentElement;

      // 1. Apply Dark Mode
      if (s.mode === 'dark') {
        html.classList.add('admin-dark', 'dark');
      } else {
        html.classList.remove('admin-dark', 'dark');
      }

      // 2. Apply Density
      if (s.density === 'compact') {
        html.classList.add('admin-compact');
      } else {
        html.classList.remove('admin-compact');
      }

      // 3. Resolve Effective Colors
      let effectiveSidebar = s.sidebarBg;
      let effectiveBg = s.mainBg;
      let effectiveCard = s.cardBg;

      if (s.preset && THEME_PRESETS[s.preset]) {
        const p = THEME_PRESETS[s.preset];
        effectiveSidebar = p.sidebarBg;
        effectiveBg = s.mode === 'dark' ? p.darkBg : p.lightBg;
        effectiveCard = p.cardBg;
      }

      // Apply CSS Custom Properties
      html.style.setProperty('--admin-sidebar-bg', effectiveSidebar);
      html.style.setProperty('--admin-bg-color', effectiveBg);
      html.style.setProperty('--admin-card-bg', effectiveCard);

      // 4. Update Sidebar Trigger Badge
      updateSidebarTriggerBadge(s, effectiveSidebar);

      // 5. Sync Modal Controls
      syncThemeModalUI(s, effectiveSidebar, effectiveBg);
    };

    function updateSidebarTriggerBadge(s, effectiveSidebar) {
      const modeText = document.getElementById('admin-settings-mode-text');
      const themeDot = document.getElementById('admin-settings-theme-dot');
      const btnStatus = document.getElementById('admin-settings-btn-status');

      if (modeText) {
        modeText.textContent = s.mode === 'dark' ? 'Dark' : 'Light';
      }
      if (themeDot) {
        themeDot.style.backgroundColor = s.preset && THEME_PRESETS[s.preset] ? THEME_PRESETS[s.preset].accentColor : effectiveSidebar;
      }
      if (btnStatus) {
        const pName = s.preset && THEME_PRESETS[s.preset] ? THEME_PRESETS[s.preset].name.split(' ')[0] : 'Kustom';
        const dName = s.density === 'compact' ? 'Compact' : 'Comfy';
        btnStatus.textContent = `${pName} • ${dName}`;
      }
    }

    function syncThemeModalUI(s, effectiveSidebar, effectiveBg) {
      const OUTLINE_ICONS = {
        sun: '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4" /><path d="M12 2v2" /><path d="M12 20v2" /><path d="m4.93 4.93 1.41 1.41" /><path d="m17.66 17.66 1.41 1.41" /><path d="M2 12h2" /><path d="M20 12h2" /><path d="m6.34 17.66-1.41 1.41" /><path d="m19.07 4.93-1.41 1.41" /></svg>',
        moon: '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z" /></svg>',
        comfy: '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" /><path d="M3 12h18" /></svg>',
        compact: '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" /><path d="M3 9h18" /><path d="M3 15h18" /></svg>'
      };

      function setSegmentedState(activeBtn, inactiveBtn) {
        if (!activeBtn || !inactiveBtn) return;
        activeBtn.classList.add('bg-white', 'dark:bg-slate-800', 'text-blue-600', 'dark:text-blue-400', 'font-semibold');
        activeBtn.classList.remove('text-slate-600', 'dark:text-slate-400', 'font-medium');

        inactiveBtn.classList.remove('bg-white', 'dark:bg-slate-800', 'text-blue-600', 'dark:text-blue-400', 'font-semibold');
        inactiveBtn.classList.add('text-slate-600', 'dark:text-slate-400', 'font-medium');
      }

      // Sync Mode Cards
      const lightBtn = document.getElementById('theme-mode-light-btn');
      const darkBtn = document.getElementById('theme-mode-dark-btn');
      const modeDesc = document.getElementById('theme-mode-desc-text');
      const modeIcon = document.getElementById('theme-mode-indicator-icon');

      if (lightBtn && darkBtn) {
        if (s.mode === 'dark') {
          setSegmentedState(darkBtn, lightBtn);
          if (modeDesc) modeDesc.textContent = 'Mode Gelap (Dark)';
          if (modeIcon) modeIcon.innerHTML = OUTLINE_ICONS.moon;
        } else {
          setSegmentedState(lightBtn, darkBtn);
          if (modeDesc) modeDesc.textContent = 'Mode Terang (White)';
          if (modeIcon) modeIcon.innerHTML = OUTLINE_ICONS.sun;
        }
      }

      // Sync Preset Cards
      document.querySelectorAll('.theme-preset-card').forEach(function (card) {
        const pKey = card.getAttribute('data-preset');
        const check = card.querySelector('.preset-check');
        if (s.preset === pKey) {
          card.classList.add('border-blue-500', 'bg-blue-500/10');
          card.classList.remove('border-slate-200', 'dark:border-slate-800');
          check?.classList.remove('hidden');
        } else {
          card.classList.remove('border-blue-500', 'bg-blue-500/10');
          card.classList.add('border-slate-200', 'dark:border-slate-800');
          check?.classList.add('hidden');
        }
      });

      // Sync Density Cards
      const comfyBtn = document.getElementById('theme-density-comfortable-btn');
      const compactBtn = document.getElementById('theme-density-compact-btn');
      const densityDesc = document.getElementById('theme-density-desc-text');
      const densityIcon = document.getElementById('theme-density-indicator-icon');

      if (comfyBtn && compactBtn) {
        if (s.density === 'compact') {
          setSegmentedState(compactBtn, comfyBtn);
          if (densityDesc) densityDesc.textContent = 'Padat (Compact)';
          if (densityIcon) densityIcon.innerHTML = OUTLINE_ICONS.compact;
        } else {
          setSegmentedState(comfyBtn, compactBtn);
          if (densityDesc) densityDesc.textContent = 'Standar (Comfortable)';
          if (densityIcon) densityIcon.innerHTML = OUTLINE_ICONS.comfy;
        }
      }
    }

    // Modal Controls
    window.openAdminThemeModal = function () {
      const modal = document.getElementById('admin-theme-modal');
      if (!modal) return;
      modal.classList.add('is-open');
      syncThemeModalUI(getSettings(), document.documentElement.style.getPropertyValue('--admin-sidebar-bg'), document.documentElement.style.getPropertyValue('--admin-bg-color'));
      document.body.classList.add('overflow-hidden');
    };

    window.closeAdminThemeModal = function () {
      const modal = document.getElementById('admin-theme-modal');
      if (!modal) return;
      modal.classList.remove('is-open');
      document.body.classList.remove('overflow-hidden');
    };

    window.handleThemeBackdropClick = function (e) {
      if (e.target.id === 'admin-theme-modal') {
        closeAdminThemeModal();
      }
    };

    // User Actions
    window.setAdminMode = function (mode) {
      const s = getSettings();
      s.mode = mode;
      saveSettings(s);
      window.applyAdminTheme();
    };

    window.setAdminThemePreset = function (presetKey) {
      const s = getSettings();
      s.preset = presetKey;
      if (THEME_PRESETS[presetKey]) {
        const p = THEME_PRESETS[presetKey];
        s.sidebarBg = p.sidebarBg;
        s.mainBg = s.mode === 'dark' ? p.darkBg : p.lightBg;
        s.cardBg = p.cardBg;
      }
      saveSettings(s);
      window.applyAdminTheme();
    };

    window.setAdminDensity = function (density) {
      const s = getSettings();
      s.density = density;
      saveSettings(s);
      window.applyAdminTheme();
    };

    window.resetAdminThemeToDefault = function () {
      saveSettings(DEFAULT_SETTINGS);
      window.applyAdminTheme();
    };

    // Keyboard Shortcuts
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        const modal = document.getElementById('admin-theme-modal');
        if (modal && modal.classList.contains('is-open')) {
          closeAdminThemeModal();
        }
      }
    });

    // Run on Initial Load
    document.addEventListener('DOMContentLoaded', function () {
      window.applyAdminTheme();
    });

    // Run Immediately
    window.applyAdminTheme();
  })();
</script>
