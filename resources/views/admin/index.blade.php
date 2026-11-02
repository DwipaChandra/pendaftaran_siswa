@extends('layouts.admin')

@section('title', 'Dasbor Panitia SPMB 2027 - SMK TI Bali Global Badung')
@section('html_class', 'h-full bg-slate-50')

@push('styles')
    <style>
      #drawer-view-mode .drawer-data-card {
        padding: 0.75rem;
        border-radius: 0.75rem;
        column-gap: 1rem;
        row-gap: 0.625rem;
      }

      #drawer-view-mode .drawer-data-card > div {
        min-width: 0;
      }

      #drawer-view-mode .drawer-data-card > div > span:first-child {
        display: block;
        margin-bottom: 0.125rem;
        color: #64748b;
        font-size: 0.6875rem;
        font-weight: 500;
        line-height: 1rem;
      }

      #drawer-view-mode .drawer-data-card > div > span:last-child {
        display: block;
        color: #1e293b;
        font-size: 0.75rem;
        font-weight: 500;
        line-height: 1.25rem;
        overflow-wrap: anywhere;
      }

      #admin-applicant-table th,
      #admin-applicant-table td {
        vertical-align: middle;
      }

      #admin-applicant-table thead th {
        line-height: 1.25;
      }

      /* Print Styles: Isolates Kartu Identitas Pendaftaran */
      @media print {
        *,
        *::before,
        *::after {
          -webkit-print-color-adjust: exact !important;
          print-color-adjust: exact !important;
        }
        body * {
          visibility: hidden !important;
        }
        #printable-id-card,
        #printable-id-card * {
          visibility: visible !important;
        }
        #printable-id-card {
          position: absolute !important;
          top: 0 !important;
          left: 0 !important;
          right: 0 !important;
          width: 100% !important;
          max-width: 760px !important;
          margin: 0 auto !important;
          padding: 1.5rem !important;
          border: 1px solid #cbd5e1 !important;
          border-radius: 1rem !important;
          box-shadow: none !important;
          display: block !important;
          background: #ffffff !important;
          page-break-inside: avoid !important;
          break-inside: avoid !important;
        }
        #admin-print-card-body-grid {
          display: grid !important;
          grid-template-columns: 120px 1fr !important;
          gap: 1.25rem !important;
          align-items: start !important;
        }
        #admin-print-card-body-grid > div:first-child {
          grid-column: 1 !important;
          display: flex !important;
          flex-direction: column !important;
          align-items: center !important;
        }
        #admin-print-card-body-grid > div:last-child {
          grid-column: 2 !important;
        }
        #printable-id-card img {
          max-width: 100% !important;
          display: inline-block !important;
          visibility: visible !important;
        }
        #admin-print-photo-box img {
          width: 100% !important;
          height: 100% !important;
          object-fit: cover !important;
          display: block !important;
        }
        .no-print {
          display: none !important;
        }
        @page {
          size: A4 portrait;
          margin: 10mm 12mm;
        }
      }
    </style>
@endpush

@section('content')

    <!-- Global Modal Preview Dokumen Digital -->
    <div
      id="doc-modal"
      class="fixed inset-0 z-[70] hidden bg-slate-900/60 items-center justify-center p-4 no-print"
      onclick="if (event.target === this) closeDocModal();"
    >
      <div
        class="bg-white rounded-3xl shadow-2xl border border-slate-200 max-w-lg w-full max-h-[90vh] flex flex-col overflow-hidden"
      >
        <div
          class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50 sticky top-0 z-10 shrink-0"
        >
          <div class="flex items-center gap-2.5">
            <div
              class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center"
            >
              <img
                src="{{ asset('assets/img/icon/eye.svg') }}"
                alt=""
                class="w-4 h-4 object-contain shrink-0"
              />
            </div>
            <h3
              id="doc-modal-title"
              class="text-sm font-bold text-slate-900 leading-tight"
            >
              Pratinjau Dokumen
            </h3>
          </div>
          <button
            type="button"
            onclick="closeDocModal()"
            title="Tutup"
            class="text-slate-400 hover:text-slate-700 p-2 rounded-xl hover:bg-slate-100 transition"
          >
            <img
              src="{{ asset('assets/img/icon/close.svg') }}"
              alt="Tutup"
              class="w-5 h-5 object-contain shrink-0"
            />
          </button>
        </div>
        <div
          id="doc-modal-body"
          class="p-6 overflow-y-auto flex-1 text-center space-y-4"
        ></div>
      </div>
    </div>

    <!-- Modal Konfirmasi Hapus Data Pendaftar (Digunakan Bersama oleh Tabel & Notifikasi) -->
    <div
      id="delete-confirm-modal"
      class="hidden fixed inset-0 z-[100] bg-slate-900/60 items-center justify-center p-4 transition-all duration-200"
      role="dialog"
      aria-modal="true"
      aria-labelledby="delete-confirm-title"
    >
      <div class="relative w-full max-w-md bg-white rounded-2xl border border-slate-200 shadow-2xl overflow-hidden p-5 sm:p-6 text-center transform transition-all duration-200 scale-95 opacity-0" id="delete-confirm-box">
        <!-- Icon Peringatan Merah Bulat -->
        <div class="w-12 h-12 mx-auto rounded-full bg-rose-50 text-rose-600 border border-rose-200/70 flex items-center justify-center mb-3.5">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
        </div>

        <h3 id="delete-confirm-title" class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
          Konfirmasi Hapus Data
        </h3>

        <p id="delete-confirm-message" class="text-xs sm:text-sm text-slate-500 mt-2 leading-relaxed">
          Apakah Anda yakin ingin menghapus data ini? Tindakan ini akan menghapus data pendaftaran, berkas, dan verifikasi secara permanen dari database.
        </p>

        <!-- Tombol Aksi: Batal vs Ya, Hapus -->
        <div class="flex items-center justify-center gap-2.5 mt-5">
          <button
            type="button"
            id="delete-confirm-cancel-btn"
            class="w-full sm:w-auto px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition cursor-pointer active:scale-95"
          >
            Batal
          </button>
          <button
            type="button"
            id="delete-confirm-submit-btn"
            class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-sm transition cursor-pointer active:scale-95 disabled:opacity-50"
          >
            <span id="delete-confirm-submit-text">Ya, Hapus Data</span>
            <svg id="delete-confirm-spinner" class="hidden w-3.5 h-3.5 animate-spin text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- MAIN ADMIN APP LAYOUT (SIDEBAR + CONTENT) -->
    <script>
      (function() {
        try {
          if (window.innerWidth >= 1024 && localStorage.getItem('admin_sidebar_collapsed') === 'true') {
            document.documentElement.classList.add('admin-sidebar-init-collapsed');
          }
        } catch (e) {}
      })();
    </script>
    <div class="min-h-screen bg-slate-50 flex">
      <!-- SIDEBAR COMPONENT (DESKTOP FIXED + MOBILE DRAWER) -->
      @include('admin.sidebar')

      <!-- MAIN CONTENT WRAPPER -->
      <div id="admin-main-content" class="flex-1 flex flex-col min-w-0 lg:pl-64 transition-[padding] duration-300 ease-in-out">
        <!-- Top App Bar -->
        <header class="bg-white border-b border-slate-200 sticky top-0 z-20 px-4 sm:px-6 lg:px-8 py-3.5 no-print">
          <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
              <!-- Unified Brand & Sidebar Toggle Button (Shown on mobile, and on desktop when sidebar is collapsed) -->
              <div class="sidebar-closed-brand relative flex items-center shrink-0">
                <button
                  type="button"
                  id="admin-top-sidebar-toggle"
                  onclick="toggleAdminSidebar()"
                  aria-label="Buka Sidebar"
                  class="group relative flex items-center gap-2.5 sm:gap-3 p-1 sm:p-1.5 -ml-1 sm:-ml-1.5 rounded-2xl transition-all duration-200 cursor-pointer outline-none focus:outline-none focus:ring-0 focus-visible:outline-none active:outline-none text-left select-none"
                  style="outline: none !important; box-shadow: none !important;"
                >
                  <!-- Logo Container with Smooth Hover Transition: Logo SMK TI -> Ikon Sidebar -->
                  <div class="relative w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center shrink-0 transition-colors duration-200 group-hover:bg-slate-100">
                    <!-- School Logo (visible by default, fades out & scales down on hover) -->
                    <img
                      src="{{ asset('assets/img/alogo.png') }}"
                      alt="Logo SMK TI Bali Global Badung"
                      class="w-full h-full object-contain transition-all duration-200 ease-in-out group-hover:opacity-0 group-hover:scale-75"
                    />

                    <!-- Sidebar Panel SVG (hidden by default, fades in & scales up on hover) -->
                    <span class="absolute inset-0 flex items-center justify-center transition-all duration-200 ease-in-out opacity-0 scale-75 group-hover:opacity-100 group-hover:scale-100 pointer-events-none text-slate-700 group-hover:text-slate-900">
                      <svg id="admin-top-sidebar-svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="3" rx="2" />
                        <path d="M9 3v18" />
                        <path id="admin-top-sidebar-arrow" d="m13 9 3 3-3 3" />
                      </svg>
                    </span>
                  </div>

                  <!-- Brand Text (Matching landing page style) -->
                  <div class="flex flex-col leading-none">
                    <span
                      class="tracking-widest text-xs sm:text-sm font-extrabold text-[#00A8E8] whitespace-nowrap"
                      style="line-height: 1.1"
                    >
                      SMK TI
                    </span>
                    <span
                      class="text-[11px] sm:text-xs font-bold text-slate-800 -mt-0.5 whitespace-nowrap"
                      style="line-height: 1.15"
                    >
                      Bali Global Badung
                    </span>
                  </div>

                  <!-- Custom Tooltip "Buka Sidebar" on hover -->
                  <div
                    role="tooltip"
                    class="pointer-events-none absolute left-1 top-full mt-2.5 px-2.5 py-1.5 bg-slate-900 text-white text-xs font-semibold rounded-lg shadow-xl border border-slate-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 whitespace-nowrap z-50 flex items-center"
                  >
                    <span id="admin-top-sidebar-tooltip-text">Buka Sidebar</span>
                    <div class="absolute -top-1 left-4 w-2 h-2 bg-slate-900 border-l border-t border-slate-700 rotate-45"></div>
                  </div>
                </button>

                <!-- Vertical Divider between Brand and View Title -->
                <div class="h-6 w-px bg-slate-200 hidden sm:block ml-2.5 sm:ml-3 shrink-0"></div>
              </div>

              <div class="min-w-0">
                <h1 id="navbar-view-title" class="text-base sm:text-xl font-extrabold text-slate-900 tracking-tight truncate">
                  @if (($activePage ?? 'beranda') === 'kuota-rombel')
                    Kuota Rombel
                  @elseif (($activePage ?? 'beranda') === 'tabel-jurusan')
                    Tabel Pendaftar
                  @elseif (($activePage ?? 'beranda') === 'daftar-ulang')
                    Daftar Ulang
                  @elseif (($activePage ?? 'beranda') === 'keuangan')
                    Keuangan & Kas Siswa
                  @elseif (($activePage ?? 'beranda') === 'office' || ($activePage ?? 'beranda') === 'gelombang')
                    Gelombang
                  @elseif (($activePage ?? 'beranda') === 'status-berkas')
                    Status Berkas
                  @else
                    Beranda
                  @endif
                </h1>
                <div class="hidden sm:flex items-center gap-2 mt-0.5">
                  <p id="navbar-view-subtitle" class="text-xs text-slate-500 truncate">
                    <span class="font-semibold text-slate-600">Dasbor Administrasi SPMB 2027</span>
                  </p>
                </div>
              </div>
            </div>

            <!-- Right Action: Realtime Time, Notifications & Admin Account Button -->
            <div class="flex items-center gap-2.5 sm:gap-3.5 shrink-0">
              <!-- 1. Realtime Date & Time dengan Status Jam Kerja / Istirahat -->
              @php
                $navTime = now()->setTimezone('Asia/Makassar');
                $indonesianDays = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                $indonesianShortMonths = [
                  1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
                  5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
                  9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
                ];
                $navDayName = $indonesianDays[$navTime->dayOfWeek];
                $navDateNum = $navTime->day;
                $navMonthName = $indonesianShortMonths[$navTime->month];
                $navYearVal = $navTime->year;
                $navTimeStr = $navTime->format('H:i');
                $navMinutes = $navTime->hour * 60 + $navTime->minute;
                $isWorkHour = ($navMinutes >= (7 * 60 + 5) && $navMinutes <= (18 * 60));
              @endphp
              <div class="text-right leading-tight select-none">
                <div id="nav-realtime-date" class="text-[11px] sm:text-xs font-medium text-slate-500">
                  {{ $navDayName }}, {{ $navDateNum }} {{ $navMonthName }} {{ $navYearVal }}
                </div>
                <div class="text-xs sm:text-sm font-bold font-mono text-slate-900 tracking-tight mt-0.5 tabular-nums flex items-center justify-end gap-1.5">
                  <span
                    id="nav-work-status-text"
                    class="font-sans font-semibold text-[11px] sm:text-xs {{ $isWorkHour ? 'text-emerald-600 status-work' : 'text-amber-600 status-break' }}"
                    title="{{ $isWorkHour ? 'Work (07.05 - 18.00 WITA)' : 'Break (Di luar jam kerja 07.05 - 18.00 WITA)' }}"
                  >{{ $isWorkHour ? 'Work' : 'Break' }}</span>
                  <span class="text-slate-300 font-normal font-sans" aria-hidden="true">|</span>
                  <span id="nav-realtime-time">{{ $navTimeStr }}</span>
                  <span id="nav-realtime-tz" class="font-sans font-bold text-slate-800 text-[10px] sm:text-xs">WITA</span>
                </div>
              </div>

              <!-- 2. Notifikasi SPMB -->
              @include('admin.notification-dropdown')

              <!-- 3. Tombol Akun Admin (Squircle dengan Avatar 'A' & Chevron) -->
              <div class="relative group" id="admin-user-menu-container">
                <button
                  id="admin-nav-user-btn"
                  type="button"
                  class="flex items-center gap-1.5 sm:gap-2 py-1 pl-1 pr-2 sm:py-1.5 sm:pl-1.5 sm:pr-2.5 h-9 sm:h-10 rounded-2xl bg-white hover:bg-slate-50 border border-slate-200/90 hover:border-slate-300 transition-all duration-200 cursor-pointer active:scale-95"
                  aria-expanded="false"
                  aria-haspopup="true"
                  title="Menu Akun Admin"
                >
                  <div class="w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold shrink-0">
                    {{ strtoupper(substr(Auth::user()->panitia->nama_panitia ?? 'Admin', 0, 1)) }}
                  </div>
                  <svg
                    id="admin-user-chevron-icon"
                    class="w-3.5 h-3.5 text-slate-500 group-hover:text-slate-700 transition-all duration-200 group-hover:rotate-180 shrink-0"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2.5"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                  </svg>
                </button>

                <!-- POPUP DROPDOWN MENU -->
                <div
                  id="admin-user-dropdown-menu"
                  class="invisible opacity-0 translate-y-1.5 scale-95 pointer-events-none group-hover:visible group-hover:opacity-100 group-hover:translate-y-0 group-hover:scale-100 group-hover:pointer-events-auto transition-all duration-200 ease-out absolute right-0 top-full mt-2 w-72 rounded-2xl bg-white text-slate-800 shadow-xl shadow-slate-200/60 border border-slate-100 p-3.5 z-50 origin-top-right"
                >
                  <!-- Invisible bridge to keep hover active between button and menu -->
                  <div class="absolute -top-3 left-0 right-0 h-3" aria-hidden="true"></div>

                  <!-- Profile Info Header -->
                  <div class="flex items-center gap-3 px-1 py-1">
                    <div class="w-10 h-10 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-sm shrink-0 ring-2 ring-blue-50">
                      {{ strtoupper(substr(Auth::user()->panitia->nama_panitia ?? 'Admin', 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                      <h4 class="font-bold text-sm text-slate-800 leading-tight truncate">
                        {{ Auth::user()->panitia->nama_panitia ?? 'Administrator' }}
                      </h4>
                      <p class="text-xs text-slate-400 font-normal truncate mt-0.5" title="{{ Auth::user()->email ?? 'admin@smktibadung.sch.id' }}">
                        {{ Auth::user()->email ?? 'admin@smktibadung.sch.id' }}
                      </p>
                    </div>
                    <span class="shrink-0 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-600 border border-blue-100/80">
                      Admin
                    </span>
                  </div>

                  <!-- Divider -->
                  <div class="my-2.5 border-t border-slate-100"></div>

                  <!-- Menu Items -->
                  <div class="space-y-1">
                    <a
                      href="{{ route('admin.password.edit') }}"
                      class="group/item flex items-center gap-3 px-2.5 py-2 rounded-xl text-xs sm:text-sm font-medium text-slate-700 hover:text-blue-600 hover:bg-blue-50/60 transition-colors"
                    >
                      <svg class="w-4 h-4 text-blue-600 shrink-0 transition-transform duration-200 group-hover/item:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                      </svg>
                      <span>Ganti kata sandi</span>
                    </a>

                    <!-- Divider -->
                    <div class="my-2 border-t border-slate-100"></div>

                    <!-- Keluar (Logout) -->
                    <form id="admin-navbar-logout-form" action="{{ route('logout') }}" method="POST">
                      @csrf
                      <button
                        type="button"
                        id="admin-logout-btn"
                        onclick="handleAdminLogout(event)"
                        class="group/item w-full flex items-center gap-3 px-2.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-rose-500 hover:bg-rose-50/80 hover:text-rose-600 transition-colors cursor-pointer text-left"
                      >
                        <svg id="admin-logout-icon" class="w-4 h-4 text-rose-500 shrink-0 transition-transform duration-200 group-hover/item:-translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <svg id="admin-logout-spinner" class="w-4 h-4 animate-spin shrink-0 hidden text-rose-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span id="admin-logout-text">Keluar</span>
                      </button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </header>

        <!-- DASHBOARD BODY CONTAINER -->
        <main
          id="admin-dashboard-wrapper"
          data-download-icon="{{ asset('assets/img/icon/download.svg') }}"
          class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-7 space-y-6"
        >
          @if (session('quota_warning'))
            <div
              role="alert"
              class="flex items-start gap-2.5 rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-xs font-medium text-amber-900"
            >
              <img
                src="{{ asset('assets/img/icon/alert-triangle.svg') }}"
                alt=""
                class="w-4 h-4 shrink-0"
              />
              <span>{{ session('quota_warning') }}</span>
            </div>
          @endif


          <!-- ============================================================== -->
          <!-- HALAMAN-HALAMAN MENU DASBOR (MODULAR BLADE FILES)              -->
          <!-- ============================================================== -->
          @include('admin.beranda')
          @include('admin.office')
          @include('admin.status-berkas')
          @include('admin.kuota-rombel')
          @include('admin.tabel-jurusan')
          @include('admin.daftar-ulang')
          @include('admin.keuangan')
          @include('admin.log-aktivitas')
        </main>

        @include('partials.footer-panitia')
      </div>
    </div>

    <!-- PENGATURAN TAMPILAN ADMIN (POPUP MODAL) -->
    @include('admin.theme-modal')

    <!-- ============================================================== -->
    <!-- DRAWER / MODAL VERIFIKASI BERKAS PANITIA GURU                  -->
    <!-- ============================================================== -->
    <div
      id="drawer-backdrop"
      data-undo-url="{{ route('admin.undo') }}"
      class="fixed inset-0 z-50 hidden bg-slate-900/60 transition-opacity duration-200"
    >
      <div
        class="fixed inset-y-0 right-0 max-w-2xl w-full bg-white shadow-2xl flex flex-col h-full overflow-hidden"
      >
        <!-- Drawer Header -->
        <div
          class="px-6 py-4 border-b border-blue-100 bg-blue-50/40 flex items-center justify-between"
        >
          <div class="flex items-center gap-3 min-w-0">
            <div
              id="drawer-student-avatar-box"
              class="w-11 h-14 rounded-xl border-2 border-white bg-blue-600 flex items-center justify-center overflow-hidden shrink-0"
            >
              <img
                id="drawer-student-avatar-img"
                src=""
                alt="Pasfoto"
                class="w-full h-full object-cover hidden"
              />
              <img
                id="drawer-student-avatar-icon"
                src="{{ asset('assets/img/icon/photo.svg') }}"
                alt=""
                class="w-5 h-5 object-contain brightness-0 invert"
              />
            </div>
            <div class="min-w-0">
              <div class="flex items-center gap-2">
                <span
                  class="text-xs font-black uppercase text-blue-700 bg-blue-50 border border-blue-200 px-2.5 py-0.5 rounded-full tracking-wider"
                  id="drawer-reg-no"
                  >001</span
                >
                <span id="drawer-status-badge"></span>
              </div>
              <h3
                class="text-base font-bold text-slate-900 mt-0.5 truncate"
                id="drawer-student-name"
              >
                Nama Calon Siswa
              </h3>
            </div>
          </div>
          <div class="flex items-center gap-2 shrink-0">
            <button
              onclick="openKartuPageFromAdmin()"
              class="px-3 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200 hover:bg-blue-100 rounded-xl transition flex items-center gap-1.5"
              title="Buka Halaman Kartu Peserta Resmi"
            >
              <img
                src="{{ asset('assets/img/icon/external-link.svg') }}"
                alt=""
                class="w-3.5 h-3.5 object-contain shrink-0"
              />
              <span>Halaman Kartu</span>
            </button>
            <button
              onclick="printApplicantCardFromAdmin()"
              class="px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-100 rounded-xl transition flex items-center gap-1.5"
              title="Cetak Kartu Identitas Pendaftaran"
            >
              <img
                src="{{ asset('assets/img/icon/printer-blue.svg') }}"
                alt=""
                class="w-3.5 h-3.5 object-contain shrink-0"
              />
              <span>Cetak Kartu</span>
            </button>
            <button
              onclick="closeReviewDrawer()"
              class="p-2 text-slate-400 hover:text-slate-700 rounded-xl hover:bg-slate-100 transition"
            >
              <img
                src="{{ asset('assets/img/icon/close.svg') }}"
                alt="Tutup"
                class="w-5 h-5 object-contain shrink-0"
              />
            </button>
          </div>
        </div>

        <!-- Drawer Content -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-4">
          <!-- Mode Tampilan Normal (Read-Only) -->
          <div id="drawer-view-mode" class="space-y-4">
            <div
              id="drawer-data-status"
              role="status"
              class="hidden text-xs text-emerald-700"
            ></div>

            <!-- Biodata Siswa -->
            <div class="space-y-1.5">
              <div class="flex items-center justify-between">
                <h4
                  class="text-xs font-bold text-slate-900 uppercase tracking-wider"
                >
                  Identitas Calon Siswa & Kontak
                </h4>
                <button
                  type="button"
                  onclick="startEditStudentData('identitas')"
                  class="text-[11px] font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1 transition"
                  title="Edit Identitas Siswa"
                >
                  <img
                    src="{{ asset('assets/img/icon/pencil.svg') }}"
                    alt=""
                    class="w-3 h-3 object-contain shrink-0"
                  />
                  <span>Edit</span>
                </button>
              </div>
              <div
                class="drawer-data-card bg-blue-50/30 rounded-xl p-3 border border-blue-100/80 grid grid-cols-2 gap-3 text-xs"
              >
                <div>
                  <span class="text-slate-400 block text-[11px]">NISN:</span>
                  <span
                    class="text-slate-800 font-bold font-mono"
                    id="drawer-nisn"
                    >-</span
                  >
                </div>
                <div>
                  <span class="text-slate-400 block text-[11px]" 
                    >Tempat, Tanggal Lahir:</span
                  >
                  <span class="text-slate-800 font-semibold" id="drawer-ttl"
                    >-</span
                  >
                </div>
                <div>
                  <span class="text-slate-400 block text-[11px]"
                    >Jenis Kelamin:</span
                  >
                  <span class="text-slate-800 font-semibold" id="drawer-jk"
                    >-</span
                  >
                </div>
                <div>
                  <span class="text-slate-400 block text-[11px]">Agama:</span>
                  <span class="text-slate-800 font-semibold" id="drawer-agama"
                    >-</span
                  >
                </div>
                <div>
                  <span class="text-slate-400 block text-[11px]"
                    >WhatsApp Siswa:</span
                  >
                  <span class="text-slate-800 font-medium" id="drawer-wa-siswa"
                    >-</span
                  >
                </div>
                <div>
                  <span class="text-slate-400 block text-[11px]"
                    >Asal SMP/MTs:</span
                  >
                  <span class="font-bold text-slate-900" id="drawer-sekolah"
                    >-</span
                  >
                </div>
                <div class="col-span-2">
                  <span class="text-slate-400 block text-[11px]"
                    >Alamat Domisili Siswa:</span
                  >
                  <span
                    class="text-slate-700 leading-relaxed"
                    id="drawer-alamat"
                    >-</span
                  >
                </div>
              </div>
            </div>

            <!-- Data Orang Tua / Wali -->
            <div class="space-y-1.5">
              <div class="flex items-center justify-between">
                <h4
                  class="text-xs font-bold text-slate-900 uppercase tracking-wider"
                >
                  Data Orang Tua / Wali
                </h4>
                <button
                  type="button"
                  onclick="startEditStudentData('ortu')"
                  class="text-[11px] font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1 transition"
                  title="Edit Data Orang Tua"
                >
                  <img
                    src="{{ asset('assets/img/icon/pencil.svg') }}"
                    alt=""
                    class="w-3 h-3 object-contain shrink-0"
                  />
                  <span>Edit</span>
                </button>
              </div>
              <div
                class="drawer-data-card bg-blue-50/30 rounded-xl p-3 border border-blue-100/80 grid grid-cols-2 gap-3 text-xs"
              >
                <div>
                  <span class="text-slate-400 block text-[11px]"
                    >Nama Bapak:</span
                  >
                  <span class="font-bold text-slate-800" id="drawer-nama-bapak"
                    >-</span
                  >
                </div>
                <div>
                  <span class="text-slate-400 block text-[11px]"
                    >Nama Ibu:</span
                  >
                  <span class="font-bold text-slate-800" id="drawer-nama-ibu"
                    >-</span
                  >
                </div>
                <div>
                  <span class="text-slate-400 block text-[11px]"
                    >Pekerjaan Bapak:</span
                  >
                  <span
                    class="text-slate-800 font-medium"
                    id="drawer-pekerjaan-bapak"
                    >-</span
                  >
                </div>
                <div>
                  <span class="text-slate-400 block text-[11px]"
                    >Pekerjaan Ibu:</span
                  >
                  <span
                    class="text-slate-800 font-medium"
                    id="drawer-pekerjaan-ibu"
                    >-</span
                  >
                </div>
                <div>
                  <span class="text-slate-400 block text-[11px]"
                    >No. WhatsApp Ayah:</span
                  >
                  <span
                    class="text-slate-800 font-medium"
                    id="drawer-no-wa-ayah"
                    >-</span
                  >
                </div>
                <div>
                  <span class="text-slate-400 block text-[11px]"
                    >No. WhatsApp Ibu:</span
                  >
                  <span
                    class="text-slate-800 font-medium"
                    id="drawer-no-wa-ibu"
                    >-</span
                  >
                </div>
                <div>
                  <span class="text-slate-400 block text-[11px]"
                    >Alamat Orang Tua / Wali:</span
                  >
                  <span
                    class="text-slate-700 leading-relaxed"
                    id="drawer-alamat-ortu"
                    >-</span
                  >
                </div>
              </div>
            </div>

            <!-- Pilihan Jurusan & Gelombang -->
            <div class="space-y-1.5">
              <div class="flex items-center justify-between">
                <h4   
                  class="text-xs font-bold text-slate-900 uppercase tracking-wider"
                >
                  Pilihan Pendaftaran
                </h4>
                <button
                  type="button"
                  onclick="startEditStudentData('pilihan')"
                  class="text-[11px] font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1 transition"
                  title="Edit Pilihan Pendaftaran"
                >
                  <img
                    src="{{ asset('assets/img/icon/pencil.svg') }}"
                    alt=""
                    class="w-3 h-3 object-contain shrink-0"
                  />
                  <span>Edit</span>
                </button>
              </div>
              <div
                class="drawer-data-card bg-blue-50/30 rounded-xl p-3 border border-blue-100/80 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs"
              >
                <div>
                  <span class="text-slate-400 block text-[11px]"
                    >Jurusan Utama:</span
                  >
                  <span
                    class="font-bold text-blue-700 text-sm"
                    id="drawer-major-1"
                    >-</span
                  >
                </div>
                <div>
                  <span class="text-slate-400 block text-[11px]"
                    >Jalur Pendaftaran:</span
                  >
                  <span class="font-semibold text-slate-800" id="drawer-jalur"
                    >-</span
                  >
                </div>
                <div>
                  <span class="text-slate-400 block text-[11px]"
                    >Gelombang:</span
                  >
                  <span
                    class="font-semibold text-slate-800"
                    id="drawer-gelombang"
                    >-</span
                  >
                </div>
                <div>
                  <span class="text-slate-400 block text-[11px]"
                    >Tanggal Daftar:</span
                  >
                  <span
                    class="font-semibold text-slate-800"
                    id="drawer-tgl-daftar"
                    >-</span
                  >
                </div>
              </div>
            </div>

            <!-- Berkas Persyaratan Digital (Tepat 3 Berkas) -->
            <div class="space-y-1.5">
              <h4
                class="text-xs font-bold text-slate-900 uppercase tracking-wider"
              >
                Kelengkapan Dokumen Digital
              </h4>
              <div
                id="drawer-docs-list"
                data-url-template="{{ route('admin.berkas.update', ['no_registrasi' => '__REGISTRATION__']) }}"
                class="space-y-2"
              ></div>
              <input type="hidden" id="drawer-docs-csrf" value="{{ csrf_token() }}" />
              <div
                id="drawer-docs-status"
                role="status"
                class="hidden text-xs text-emerald-700"
              ></div>
            </div>

            <!-- Catatan Verifikator & Feedback Form -->
            <div class="space-y-1.5 pt-3 border-t border-slate-200">
              <h4
                class="text-xs font-bold text-slate-900 uppercase tracking-wider"
              >
                Catatan Resmi Verifikator
              </h4>
              <textarea
                id="drawer-notes-input"
                rows="3"
                placeholder="Tuliskan catatan hasil pemeriksaan berkas atau instruksi perbaikan untuk calon siswa..."
                class="w-full text-xs rounded-xl border border-slate-200 p-3 text-slate-800 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition"
              ></textarea>
              <div class="flex items-center gap-1.5 flex-wrap">
                <span class="text-[10px] font-semibold text-slate-400"
                  >Template Cepat:</span
                >
                <button
                  type="button"
                  onclick="
                    setTemplateNote(
                      'Seluruh dokumen persyaratan telah diterima dan dinyatakan lengkap. Silakan menunggu informasi mengenai tahapan seleksi selanjutnya.',
                    )
                  "
                  class="px-2 py-0.5 bg-slate-50/80 hover:bg-slate-100 text-slate-700 text-[10px] rounded-lg transition"
                >
                  Lengkap
                </button>
                <button
                  type="button"
                  onclick="
                    setTemplateNote(
                      'Dokumen Kartu Keluarga belum terbaca dengan jelas atau terpotong. Mohon unggah ulang salinan yang utuh dan terbaca.',
                    )
                  "
                  class="px-2 py-0.5 bg-slate-50/80 hover:bg-slate-100 text-slate-700 text-[10px] rounded-lg transition"
                >
                  KK Buram / Terpotong
                </button>
                <button
                  type="button"
                  onclick="
                    setTemplateNote(
                      'Pasfoto belum memenuhi ketentuan. Mohon unggah pasfoto formal dengan latar belakang polos, tampak jelas, dan bukan swafoto.',
                    )
                  "
                  class="px-2 py-0.5 bg-slate-50/80 hover:bg-slate-100 text-slate-700 text-[10px] rounded-lg transition"
                >
                  Pasfoto Tidak Sesuai
                </button>
                <button
                  type="button"
                  onclick="
                    setTemplateNote(
                      'Data pada Kartu Keluarga belum sesuai dengan data pendaftaran. Mohon periksa kembali dan unggah dokumen pendukung yang benar.',
                    )
                  "
                  class="px-2 py-0.5 bg-slate-50/80 hover:bg-slate-100 text-slate-700 text-[10px] rounded-lg transition"
                >
                  Data KK Tidak Sesuai
                </button>
                <button
                  type="button"
                  onclick="
                    setTemplateNote(
                      'Data pada Akta Kelahiran belum sesuai dengan data yang tercantum pada formulir pendaftaran. Mohon perbaiki data atau unggah dokumen yang sesuai.',
                    )
                  "
                  class="px-2 py-0.5 bg-slate-50/80 hover:bg-slate-100 text-slate-700 text-[10px] rounded-lg transition"
                >
                  Data Akta Tidak Sesuai
                </button>
                <button
                  type="button"
                  onclick="
                    setTemplateNote(
                      'Nomor Induk Siswa Nasional (NISN) belum sesuai dengan data resmi. Mohon periksa kembali dan perbarui NISN pada formulir pendaftaran.',
                    )
                  "
                  class="px-2 py-0.5 bg-slate-50/80 hover:bg-slate-100 text-slate-700 text-[10px] rounded-lg transition"
                >
                  NISN Tidak Sesuai
                </button>
                <button
                  type="button"
                  onclick="
                    setTemplateNote(
                      'Dokumen persyaratan belum lengkap. Mohon unggah seluruh berkas yang diminta melalui akun pendaftaran.',
                    )
                  "
                  class="px-2 py-0.5 bg-slate-50/80 hover:bg-slate-100 text-slate-700 text-[10px] rounded-lg transition"
                >
                  Berkas Belum Lengkap
                </button>
                <button
                  type="button"
                  onclick="
                    setTemplateNote(
                      'Berkas yang diunggah tidak dapat dibuka atau dibaca. Mohon unggah ulang dalam format JPG, PNG, atau PDF yang valid.',
                    )
                  "
                  class="px-2 py-0.5 bg-slate-50/80 hover:bg-slate-100 text-slate-700 text-[10px] rounded-lg transition"
                >
                  File Tidak Terbaca
                </button>
                <button
                  type="button"
                  onclick="
                    setTemplateNote(
                      'Mohon unggah ulang dokumen dengan seluruh bagian terlihat utuh, tidak buram, dan tidak terpotong.',
                    )
                  "
                  class="px-2 py-0.5 bg-slate-50/80 hover:bg-slate-100 text-slate-700 text-[10px] rounded-lg transition"
                >
                  Unggah Ulang Dokumen
                </button>
              </div>
            </div>
          </div>

          <!-- Mode Form Edit Data Calon Siswa -->
          <form
            id="drawer-edit-mode"
            data-url-template="{{ route('admin.pendaftaran.data.update', ['no_registrasi' => '__REGISTRATION__']) }}"
            class="space-y-6 hidden"
            onsubmit="
              event.preventDefault();
              saveStudentDataChanges();
            "
          >
            <!-- Notice Info Bar di Edit Mode -->
            <div
              class="p-3 bg-blue-50 border border-blue-200 rounded-2xl flex items-center gap-2.5 text-xs text-blue-800"
            >
              <img
                src="{{ asset('assets/img/icon/info-circle.svg') }}"
                alt=""
                class="w-4 h-4 object-contain shrink-0"
              />
              <span class="font-medium text-[11px]">
                Perubahan NISN mengubah awalan email yang digunakan siswa untuk login.
              </span>
            </div>

            <!-- Section 1: Edit Identitas Calon Siswa -->
            <div id="edit-section-identitas" class="space-y-2">
              <h4
                class="text-xs font-bold text-slate-900 uppercase tracking-wider"
              >
                Identitas Calon Siswa & Kontak
              </h4>
              <div
                class="bg-blue-50/30 rounded-2xl p-4 border border-blue-100/80 space-y-3 text-xs"
              >
                <div>
                  <label
                    for="edit-student-nama"
                    class="block text-[11px] font-semibold text-slate-600 mb-1"
                  >
                    Nama Lengkap Siswa <span class="text-rose-500">*</span>
                  </label>
                  <input
                    type="text"
                    id="edit-student-nama"
                    required
                    class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 bg-white transition"
                    placeholder="Nama lengkap calon siswa"
                  />
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label
                      for="edit-student-nisn"
                      class="block text-[11px] font-semibold text-slate-600 mb-1"
                    >
                      NISN / ID Login <span class="text-rose-500">*</span>
                    </label>
                    <input
                      type="text"
                      id="edit-student-nisn"
                      maxlength="100"
                      required
                      class="w-full text-xs font-mono rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 bg-white transition"
                      placeholder="0010014821"
                    />
                  </div>
                  <div>
                    <label
                      for="edit-student-wa"
                      class="block text-[11px] font-semibold text-slate-600 mb-1"
                    >
                      WhatsApp Siswa <span class="text-rose-500">*</span>
                    </label>
                    <input
                      type="tel"
                      id="edit-student-wa"
                      data-phone-input
                      required
                      inputmode="tel"
                      pattern="[+]?[0-9]+"
                      maxlength="20"
                      class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 bg-white transition"
                      placeholder="08123456789"
                    />
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label
                      for="edit-student-tempat-lahir"
                      class="block text-[11px] font-semibold text-slate-600 mb-1"
                    >
                      Tempat Lahir <span class="text-rose-500">*</span>
                    </label>
                    <input
                      type="text"
                      id="edit-student-tempat-lahir"
                      required
                      class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 bg-white transition"
                      placeholder="Kota Lahir"
                    />
                  </div>
                  <div>
                    <label
                      for="edit-student-tanggal-lahir"
                      class="block text-[11px] font-semibold text-slate-600 mb-1"
                    >
                      Tanggal Lahir <span class="text-rose-500">*</span>
                    </label>
                    <input
                      type="date"
                      id="edit-student-tanggal-lahir"
                      required
                      min="1950-01-01"
                      max="{{ date('Y-m-d') }}"
                      class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 bg-white transition cursor-pointer"
                    />
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label
                      for="edit-student-jk"
                      class="block text-[11px] font-semibold text-slate-600 mb-1"
                    >
                      Jenis Kelamin <span class="text-rose-500">*</span>
                    </label>
                    <select
                      id="edit-student-jk"
                      class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 bg-white transition cursor-pointer"
                    >
                      <option value="Laki-laki">Laki-laki</option>
                      <option value="Perempuan">Perempuan</option>
                    </select>
                  </div>
                  <div>
                    <label
                      for="edit-student-agama"
                      class="block text-[11px] font-semibold text-slate-600 mb-1"
                    >
                      Agama <span class="text-rose-500">*</span>
                    </label>
                    <select
                      id="edit-student-agama"
                      class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 bg-white transition cursor-pointer"
                    >
                      <option value="Hindu">Hindu</option>
                      <option value="Islam">Islam</option>
                      <option value="Kristen Protestan">
                        Kristen Protestan
                      </option>
                      <option value="Katolik">Katolik</option>
                      <option value="Buddha">Buddha</option>
                      <option value="Konghucu">Konghucu</option>
                    </select>
                  </div>
                </div>

                <div>
                  <label
                    for="edit-student-sekolah"
                    class="block text-[11px] font-semibold text-slate-600 mb-1"
                  >
                    Asal SMP/MTs <span class="text-rose-500">*</span>
                  </label>
                  <input
                    type="text"
                    id="edit-student-sekolah"
                    required
                    class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 bg-white transition"
                    placeholder="Nama SMP / MTs Asal"
                  />
                </div>

                <div>
                  <label
                    for="edit-student-alamat"
                    class="block text-[11px] font-semibold text-slate-600 mb-1"
                  >
                    Alamat Domisili Siswa <span class="text-rose-500">*</span>
                  </label>
                  <textarea
                    id="edit-student-alamat"
                    rows="2"
                    required
                    class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 bg-white transition"
                    placeholder="Alamat lengkap domisili calon siswa"
                  ></textarea>
                </div>
              </div>
            </div>

            <!-- Section 2: Edit Data Orang Tua / Wali -->
            <div id="edit-section-ortu" class="space-y-2">
              <h4
                class="text-xs font-bold text-slate-900 uppercase tracking-wider"
              >
                Data Orang Tua / Wali
              </h4>
              <div
                class="bg-blue-50/30 rounded-2xl p-4 border border-blue-100/80 space-y-3 text-xs"
              >
                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label
                      for="edit-student-nama-bapak"
                      class="block text-[11px] font-semibold text-slate-600 mb-1"
                    >
                      Nama Ayah / Wali
                    </label>
                    <input
                      type="text"
                      id="edit-student-nama-bapak"
                      class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 bg-white transition"
                      placeholder="Nama Ayah / Wali"
                    />
                  </div>
                  <div>
                    <label
                      for="edit-student-pekerjaan-bapak"
                      class="block text-[11px] font-semibold text-slate-600 mb-1"
                    >
                      Pekerjaan Ayah
                    </label>
                    <input
                      type="text"
                      id="edit-student-pekerjaan-bapak"
                      class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 bg-white transition"
                      placeholder="Pekerjaan Ayah"
                    />
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label
                      for="edit-student-nama-ibu"
                      class="block text-[11px] font-semibold text-slate-600 mb-1"
                    >
                      Nama Ibu Kandung
                    </label>
                    <input
                      type="text"
                      id="edit-student-nama-ibu"
                      class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 bg-white transition"
                      placeholder="Nama Ibu Kandung"
                    />
                  </div>
                  <div>
                    <label
                      for="edit-student-pekerjaan-ibu"
                      class="block text-[11px] font-semibold text-slate-600 mb-1"
                    >
                      Pekerjaan Ibu
                    </label>
                    <input
                      type="text"
                      id="edit-student-pekerjaan-ibu"
                      class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 bg-white transition"
                      placeholder="Pekerjaan Ibu"
                    />
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label
                      for="edit-student-no-wa-ayah"
                      class="block text-[11px] font-semibold text-slate-600 mb-1"
                    >
                      No. WhatsApp Ayah
                    </label>
                    <input
                      type="tel"
                      id="edit-student-no-wa-ayah"
                      data-phone-input
                      inputmode="tel"
                      pattern="[+]?[0-9]+"
                      maxlength="20"
                      class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 bg-white transition"
                      placeholder="08123456789"
                    />
                  </div>
                  <div>
                    <label
                      for="edit-student-no-wa-ibu"
                      class="block text-[11px] font-semibold text-slate-600 mb-1"
                    >
                      No. WhatsApp Ibu
                    </label>
                    <input
                      type="tel"
                      id="edit-student-no-wa-ibu"
                      data-phone-input
                      inputmode="tel"
                      pattern="[+]?[0-9]+"
                      maxlength="20"
                      class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 bg-white transition"
                      placeholder="08123456789"
                    />
                  </div>
                </div>

                <div>
                  <label
                    for="edit-student-alamat-ortu"
                    class="block text-[11px] font-semibold text-slate-600 mb-1"
                  >
                    Alamat Orang Tua / Wali
                  </label>
                  <textarea
                    id="edit-student-alamat-ortu"
                    rows="2"
                    class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 bg-white transition"
                    placeholder="Alamat orang tua (kosongkan jika sama dengan domisili siswa)"
                  ></textarea>
                </div>
              </div>
            </div>

            <!-- Section 3: Edit Pilihan Pendaftaran & Status -->
            <div id="edit-section-pilihan" class="space-y-2">
              <h4
                class="text-xs font-bold text-slate-900 uppercase tracking-wider"
              >
                Pilihan Pendaftaran & Status
              </h4>
              <div
                class="bg-blue-50/30 rounded-2xl p-4 border border-blue-100/80 space-y-3 text-xs"
              >
                <div>
                  <label
                    for="edit-student-major"
                    class="block text-[11px] font-semibold text-slate-600 mb-1"
                  >
                    Kompetensi Keahlian (Jurusan)
                    <span class="text-rose-500">*</span>
                  </label>
                  <select
                    id="edit-student-major"
                    required
                    class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 bg-white transition cursor-pointer font-semibold"
                  >
                    @foreach ($jurusanList as $jurusan)
                      <option value="{{ $jurusan->id_jurusan }}">{{ $jurusan->nama_jurusan }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label
                      for="edit-student-jalur"
                      class="block text-[11px] font-semibold text-slate-600 mb-1"
                    >
                      Jalur Pendaftaran <span class="text-rose-500">*</span>
                    </label>
                    <select
                      id="edit-student-jalur"
                      required
                      class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 bg-white transition cursor-pointer"
                    >
                      @foreach ($jalurList as $jalur)
                        <option value="{{ $jalur->id_jalur }}">{{ $jalur->nama_jalur }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div>
                    <label
                      for="edit-student-gelombang"
                      class="block text-[11px] font-semibold text-slate-600 mb-1"
                    >
                      Gelombang <span class="text-rose-500">*</span>
                    </label>
                    <select
                      id="edit-student-gelombang"
                      required
                      class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 bg-white transition cursor-pointer"
                    >
                      @foreach ($gelombangList as $gelombang)
                        <option value="{{ $gelombang->id_gelombang }}">{{ $gelombang->nama_gelombang }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label
                      for="edit-student-sub-jalur"
                      class="block text-[11px] font-semibold text-slate-600 mb-1"
                    >
                      Sub-Jalur / Kategori Khusus
                    </label>
                    <input
                      type="text"
                      id="edit-student-sub-jalur"
                      placeholder="prestasi_sekolah_kab / mou / dll"
                      class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 bg-white transition"
                    />
                  </div>
                  <div>
                    <label
                      for="edit-student-nama-sub-jalur"
                      class="block text-[11px] font-semibold text-slate-600 mb-1"
                    >
                      Keterangan Sub-Jalur
                    </label>
                    <input
                      type="text"
                      id="edit-student-nama-sub-jalur"
                      placeholder="Juara Kelas / Rekomendasi MOU"
                      class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 bg-white transition"
                    />
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label
                      for="edit-student-voucher-jenis"
                      class="block text-[11px] font-semibold text-slate-600 mb-1"
                    >
                      Voucher
                    </label>
                    <select
                      id="edit-student-voucher-jenis"
                      class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 bg-white transition cursor-pointer"
                    >
                      <option value="">Tidak ada voucher</option>
                      <option value="rekomendasi">Voucher Rekomendasi Siswa/Alumni</option>
                      <option value="hut">Voucher HUT SMK TI</option>
                    </select>
                  </div>
                  <div>
                    <label
                      for="edit-student-voucher-kode"
                      class="block text-[11px] font-semibold text-slate-600 mb-1"
                    >
                      Kode / Info Voucher
                    </label>
                    <input
                      type="text"
                      id="edit-student-voucher-kode"
                      placeholder="Kode atau Nama Perekomendasi"
                      class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 bg-white transition"
                    />
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label
                      for="edit-student-status"
                      class="block text-[11px] font-semibold text-slate-600 mb-1"
                    >
                      Status Verifikasi Berkas
                      <span class="text-rose-500">*</span>
                    </label>
                    <select
                      id="edit-student-status"
                      required
                      class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 bg-white transition cursor-pointer font-semibold"
                    >
                      <option value="Menunggu Verifikasi">
                        Menunggu Verifikasi
                      </option>
                      <option value="Diterima">Terverifikasi</option>
                      <option value="Perbaikan Berkas">Perlu Revisi</option>
                      <option value="Ditolak">Ditolak</option>
                    </select>
                  </div>
                  <div>
                    <label
                      for="edit-student-tgl-daftar"
                      class="block text-[11px] font-semibold text-slate-600 mb-1"
                    >
                      Tanggal Pendaftaran <span class="text-rose-500">*</span>
                    </label>
                    <input
                      type="date"
                      id="edit-student-tgl-daftar"
                      required
                      class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 bg-white transition cursor-pointer"
                    />
                  </div>
                </div>

                <div>
                  <label
                    for="edit-student-notes"
                    class="block text-[11px] font-semibold text-slate-600 mb-1"
                  >
                    Catatan Verifikator
                  </label>
                  <textarea
                    id="edit-student-notes"
                    rows="2"
                    placeholder="Catatan hasil verifikasi atau instruksi perbaikan..."
                    class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 bg-white transition"
                  ></textarea>
                </div>
              </div>
            </div>
          </form>
        </div>

        <!-- Drawer Footer Actions (View Mode) -->
        <div
          id="drawer-view-footer"
          class="p-4 bg-slate-50 border-t border-slate-200 overflow-x-auto"
        >
          <div class="flex w-max min-w-full items-center gap-2 whitespace-nowrap">
            <button
              id="drawer-undo-button"
              type="button"
              onclick="undoDrawerChange()"
              aria-label="Undo satu langkah"
              title="Undo satu langkah"
              class="shrink-0 p-2 text-slate-700 bg-white border border-slate-200 hover:bg-slate-100 rounded-lg transition disabled:opacity-40 disabled:cursor-not-allowed"
              disabled
            >
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <use href="{{ asset('assets/img/icon/sprite.svg#refresh-undo') }}" />
              </svg>
            </button>
            <button
              id="drawer-redo-button"
              type="button"
              onclick="redoDrawerChange()"
              aria-label="Redo satu langkah"
              title="Redo satu langkah"
              class="shrink-0 p-2 text-slate-700 bg-white border border-slate-200 hover:bg-slate-100 rounded-lg transition disabled:opacity-40 disabled:cursor-not-allowed"
              disabled
            >
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <use href="{{ asset('assets/img/icon/sprite.svg#refresh-redo') }}" />
              </svg>
            </button>
            <span class="mx-1 h-7 shrink-0 border-l border-slate-300" aria-hidden="true"></span>
            <button
              type="button"
              onclick="updateApplicantStatusAction('Perbaikan Berkas')"
              class="shrink-0 px-3 py-2 text-xs font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 rounded-xl transition"
            >
              Minta Revisi Berkas
            </button>
            <button
              type="button"
              onclick="updateApplicantStatusAction('Ditolak')"
              class="shrink-0 px-3 py-2 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-xl transition"
            >
              Tolak Pendaftaran
            </button>
            <button
              type="button"
              onclick="updateApplicantStatusAction('Diterima')"
              class="shrink-0 px-4 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition flex items-center gap-1.5"
            >
              <img
                src="{{ asset('assets/img/icon/check-white.svg') }}"
                alt=""
                class="w-4 h-4 object-contain shrink-0"
              />
              <span>Setujui & Verifikasi Berkas</span>
            </button>
          </div>
        </div>

        <!-- Drawer Footer Actions (Edit Mode) -->
        <div
          id="drawer-edit-footer"
          class="p-4 bg-slate-50 border-t border-slate-200 items-center justify-between gap-3 hidden"
        >
          <span id="drawer-edit-status" role="status" class="text-xs text-slate-500"></span>
          <div class="ml-auto flex items-center gap-2">
            <button
              type="button"
              onclick="cancelEditStudentData()"
              class="px-4 py-2 text-xs font-semibold text-slate-700 bg-slate-50/80 hover:bg-slate-100 rounded-xl transition"
            >
              Batal
            </button>
            <button
              type="button"
              id="drawer-edit-save-button"
              onclick="saveStudentDataChanges()"
              class="px-5 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition flex items-center gap-1.5"
            >
              <img
                src="{{ asset('assets/img/icon/check-white.svg') }}"
                alt=""
                class="w-4 h-4 object-contain shrink-0"
              />
              <span>Simpan Perubahan Data</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Form Tersembunyi untuk Verifikasi Panitia -->
    <form id="admin-verification-form" method="POST" style="display: none;">
      @csrf
      <input type="hidden" name="status_akhir" id="form-status-akhir" value="">
      <input type="hidden" name="catatan_verifikator" id="form-catatan-verifikator" value="">
    </form>

    <!-- ============================================================== -->
    <!-- TEMPLATE KARTU IDENTITAS PENDAFTARAN (SIAP CETAK / PDF DARI ADMIN) -->
    <!-- ============================================================== -->
    <div
      id="printable-id-card"
      class="hidden w-full max-w-3xl mx-auto bg-white rounded-2xl p-4 sm:p-6 shadow-xl shadow-slate-300/70 relative text-slate-900 overflow-hidden"
    >
      <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] pointer-events-none select-none">
        <img src="{{ asset('assets/img/icon/academic-cap.svg') }}" alt="" class="w-96 h-96 object-contain pointer-events-none select-none" />
      </div>

      <div class="relative z-10 border-b-2 border-slate-900 pb-3 mb-3">
        <div class="flex items-center justify-between gap-3 text-center sm:text-left">
          <img src="{{ asset('assets/img/alogo.png') }}" alt="Logo SMK TI Bali Global Badung" class="w-12 h-12 sm:w-16 sm:h-16 object-contain shrink-0" />
          <div class="flex-1 text-left">
            <p class="text-[9.5px] sm:text-[10.5px] font-semibold uppercase tracking-widest text-slate-600">Yayasan Widya Dharma Shanti</p>
            <h2 class="text-sm sm:text-base md:text-lg font-black text-slate-950 tracking-tight leading-snug uppercase">SMK TI BALI GLOBAL BADUNG</h2>
            <p class="text-[9.5px] sm:text-[10.5px] font-bold text-blue-700">TERAKREDITASI &ldquo;A&rdquo; (UNGGUL) &bull; NPSN: 69959273</p>
          </div>
          <div class="flex flex-col items-end shrink-0">
            <span class="px-2.5 py-1 bg-slate-900 text-white text-[9px] font-black rounded uppercase tracking-wider">SPMB 2027/2028</span>
            <span class="text-[8px] text-slate-500 font-semibold mt-0.5">Kartu Bukti Resmi</span>
          </div>
        </div>
      </div>

      <div class="relative z-10 text-center mb-3">
        <h3 class="text-xs sm:text-sm font-black text-slate-950 uppercase tracking-wide">KARTU TANDA PESERTA SELEKSI SPMB</h3>
        <p class="text-[9px] text-slate-500 font-medium">Tahun Ajaran 2027/2028</p>
      </div>

      <div class="relative z-10 bg-slate-50 rounded-lg px-3 py-2 mb-3.5 border border-slate-300 flex flex-wrap items-center justify-between gap-2 text-xs">
        <div class="flex items-center gap-2">
          <span class="text-slate-500 text-[10px] sm:text-[11px]">No. Registrasi:</span>
          <strong id="admin-print-reg-no" class="text-xs sm:text-sm font-black text-blue-700 tracking-wide font-mono">-</strong>
        </div>
        <div class="flex items-center gap-2 text-[10px] sm:text-[11px]">
          <span class="text-slate-500">Tgl Pendaftaran:</span>
          <strong id="admin-print-date" class="text-slate-800 font-semibold">-</strong>
        </div>
        <div class="flex items-center gap-2 text-[10px] sm:text-[11px]">
          <span class="text-slate-500">Status:</span>
          <strong id="admin-print-status" class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded font-semibold">-</strong>
        </div>
      </div>

      <div id="admin-print-card-body-grid" class="relative z-10 grid grid-cols-1 md:grid-cols-4 gap-3.5 sm:gap-4 items-start">
        <div class="md:col-span-1 flex flex-col items-center">
          <div id="admin-print-photo-box" class="w-24 sm:w-28 h-32 sm:h-36 bg-slate-100 border-2 border-slate-300 rounded-lg overflow-hidden flex items-center justify-center relative">
            <span class="text-[9px] text-slate-400 font-bold uppercase">Pasfoto 3x4</span>
          </div>
          <span class="text-[8px] text-slate-400 font-semibold mt-1 text-center">Pasfoto 3x4 Calon Siswa</span>
        </div>

        <div class="md:col-span-3 text-[11px] sm:text-xs">
          <table class="card-data-table w-full border-collapse">
            <tbody>
              <tr class="border-b border-slate-100">
                <td class="py-0.5 sm:py-1 text-slate-500 w-32 sm:w-40 font-medium">Nama Lengkap</td>
                <td class="py-0.5 sm:py-1 font-extrabold text-slate-950 uppercase">: <span id="admin-print-nama">-</span></td>
              </tr>
              <tr class="border-b border-slate-100">
                <td class="py-0.5 sm:py-1 text-slate-500 font-medium">NISN</td>
                <td class="py-0.5 sm:py-1 font-bold text-slate-900">: <span id="admin-print-nisn" class="font-mono">-</span></td>
              </tr>
              <tr class="border-b border-slate-100">
                <td class="py-0.5 sm:py-1 text-slate-500 font-medium">Tempat, Tanggal Lahir</td>
                <td class="py-0.5 sm:py-1 text-slate-800 font-medium">: <span id="admin-print-ttl">-</span></td>
              </tr>
              <tr class="border-b border-slate-100">
                <td class="py-0.5 sm:py-1 text-slate-500 font-medium">Jenis Kelamin</td>
                <td class="py-0.5 sm:py-1 text-slate-800 font-medium">: <span id="admin-print-jk">-</span></td>
              </tr>
              <tr class="border-b border-slate-100">
                <td class="py-0.5 sm:py-1 text-slate-500 font-medium">Agama</td>
                <td class="py-0.5 sm:py-1 text-slate-800 font-medium">: <span id="admin-print-agama">-</span></td>
              </tr>
              <tr class="border-b border-slate-100">
                <td class="py-0.5 sm:py-1 text-slate-500 font-medium">Asal Sekolah</td>
                <td class="py-0.5 sm:py-1 text-slate-800 font-medium">: <span id="admin-print-sekolah">-</span></td>
              </tr>
              <tr class="border-b border-slate-100">
                <td class="py-0.5 sm:py-1 text-slate-500 font-medium">Alamat Siswa</td>
                <td class="py-0.5 sm:py-1 text-slate-800 font-medium">: <span id="admin-print-alamat">-</span></td>
              </tr>
              <tr class="border-b border-slate-100">
                <td class="py-0.5 sm:py-1 text-slate-500 font-medium">WhatsApp Siswa</td>
                <td class="py-0.5 sm:py-1 text-slate-800 font-medium">: <span id="admin-print-wa-siswa">-</span></td>
              </tr>
              <tr class="border-b border-slate-100">
                <td class="py-0.5 sm:py-1 text-slate-500 font-medium">Email Siswa</td>
                <td class="py-0.5 sm:py-1 text-slate-800 font-medium">: <span id="admin-print-email-siswa">-</span></td>
              </tr>
              <tr class="border-b border-slate-100">
                <td class="py-0.5 sm:py-1 text-slate-500 font-medium">Alamat Orang Tua</td>
                <td class="py-0.5 sm:py-1 text-slate-800 font-medium">: <span id="admin-print-alamat-ortu">-</span></td>
              </tr>
              <tr class="border-b border-slate-100">
                <td class="py-0.5 sm:py-1 text-slate-500 font-medium">Pilihan Kompetensi</td>
                <td class="py-0.5 sm:py-1 text-slate-800 font-bold">: <span id="admin-print-jurusan">-</span></td>
              </tr>
              <tr class="border-b border-slate-100">
                <td class="py-0.5 sm:py-1 text-slate-500 font-medium">Jalur Pendaftaran</td>
                <td class="py-0.5 sm:py-1 text-slate-800 font-medium">: <span id="admin-print-jalur">-</span></td>
              </tr>
              <tr class="border-b border-slate-100">
                <td class="py-0.5 sm:py-1 text-slate-500 font-medium">Gelombang Pendaftaran</td>
                <td class="py-0.5 sm:py-1 text-slate-800 font-medium">: <span id="admin-print-gelombang">-</span></td>
              </tr>
              <tr class="border-b border-slate-100">
                <td class="py-0.5 sm:py-1 text-slate-500 font-medium">Nama Ayah</td>
                <td class="py-0.5 sm:py-1 text-slate-800 font-medium">: <span id="admin-print-bapak">-</span></td>
              </tr>
              <tr class="border-b border-slate-100">
                <td class="py-0.5 sm:py-1 text-slate-500 font-medium">Pekerjaan Ayah</td>
                <td class="py-0.5 sm:py-1 text-slate-800 font-medium">: <span id="admin-print-pekerjaan-bapak">-</span></td>
              </tr>
              <tr class="border-b border-slate-100">
                <td class="py-0.5 sm:py-1 text-slate-500 font-medium">Nama Ibu</td>
                <td class="py-0.5 sm:py-1 text-slate-800 font-medium">: <span id="admin-print-ibu">-</span></td>
              </tr>
              <tr class="border-b border-slate-100">
                <td class="py-0.5 sm:py-1 text-slate-500 font-medium">Pekerjaan Ibu</td>
                <td class="py-0.5 sm:py-1 text-slate-800 font-medium">: <span id="admin-print-pekerjaan-ibu">-</span></td>
              </tr>
              <tr class="border-b border-slate-100">
                <td class="py-0.5 sm:py-1 text-slate-500 font-medium">No. WhatsApp Ayah</td>
                <td class="py-0.5 sm:py-1 text-slate-800 font-medium">: <span id="admin-print-no-wa-ayah">-</span></td>
              </tr>
              <tr>
                <td class="py-0.5 sm:py-1 text-slate-500 font-medium">No. WhatsApp Ibu</td>
                <td class="py-0.5 sm:py-1 text-slate-800 font-medium">: <span id="admin-print-no-wa-ibu">-</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="relative z-10 mt-4 pt-3 border-t border-slate-200 flex items-end justify-between gap-4">
        <div class="flex flex-col items-start text-left">
          <p class="text-[9px] text-slate-600 font-medium">Tanda Tangan Orang Tua / Wali,</p>
          <div class="h-10"></div>
          <p class="text-[10px] text-slate-700 font-semibold tracking-wide">( Orang Tua / Wali )</p>
        </div>
        <div class="flex flex-col items-end text-right">
          <p class="text-[9px] text-slate-600 font-medium">Tanda Tangan Calon Siswa,</p>
          <div class="h-10"></div>
          <p id="admin-print-ttd-nama" class="text-[10px] text-slate-700 font-semibold tracking-wide">( Calon Siswa )</p>
        </div>
      </div>
    </div>
@endsection

@push('scripts')
    <!-- ============================================================== -->
    <!-- JAVASCRIPT LOGIC: DASBOR ADMIN PANITIA SPA                     -->
    <!-- ============================================================== -->
    <script id="admin-pendaftaran-data" type="application/json">
      {!! json_encode($pendaftaranList) !!}
    </script>
    <script id="admin-doc-types" type="application/json">
      {!! json_encode(array_values(array_map(function ($k, $def) {
          return [
              'inputName' => (string) $k,
              'label' => (string) $def['label'],
              'icon' => asset($def['icon']),
              'accept' => (string) $def['accept'],
          ];
      }, array_keys(\App\Services\SyaratBerkasService::BERKAS_DEFINITIONS), \App\Services\SyaratBerkasService::BERKAS_DEFINITIONS))) !!}
    </script>
    <script>
      const pendaftaranData = (() => {
        try {
          const raw = document.getElementById('admin-pendaftaran-data')?.textContent;
          return raw ? JSON.parse(raw) : [];
        } catch (e) {
          console.error('Gagal memuat pendaftaranData:', e);
          return [];
        }
      })();
      window.pendaftaranData = pendaftaranData;
      const downloadIconSource = document.getElementById('admin-dashboard-wrapper')?.dataset?.downloadIcon || "{{ asset('assets/img/icon/download.svg') }}";
      let currentNoRegistrasi = null;
      let currentApplicant = null;
      const drawerChangeHistory = new Map();
      let historyRequestPending = false;
      const adminFilterForm = document.getElementById('admin-filter-form');
      const adminSearchInput = adminFilterForm ? adminFilterForm.querySelector('input[name="q"]') : null;
      const adminTableBody = document.getElementById('admin-table-body');
      const adminSearchEmptyState = document.getElementById('admin-search-empty-state');
      const adminTableCount = document.getElementById('admin-table-count');
      const selectAllApplicants = document.getElementById('select-all-applicants');
      const bulkDeleteForm = document.getElementById('bulk-delete-form');
      const adminFilterScrollStorageKey = 'adminFilterScrollY';
      const savedAdminFilterScrollY = sessionStorage.getItem(adminFilterScrollStorageKey);

      if (savedAdminFilterScrollY !== null) {
        sessionStorage.removeItem(adminFilterScrollStorageKey);
        const scrollPosition = Number(savedAdminFilterScrollY);
        if (Number.isFinite(scrollPosition) && scrollPosition > 0) {
          requestAnimationFrame(() => window.scrollTo(0, scrollPosition));
        }
      }

      function updateTopSidebarToggleState(isOpen) {
        const topToggleBtn = document.getElementById('admin-top-sidebar-toggle');
        const tooltipText = document.getElementById('admin-top-sidebar-tooltip-text');
        const arrow = document.getElementById('admin-top-sidebar-arrow');

        if (topToggleBtn) {
          topToggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
          topToggleBtn.setAttribute('aria-label', isOpen ? 'Tutup Sidebar' : 'Buka Sidebar');
        }
        if (tooltipText) {
          tooltipText.textContent = isOpen ? 'Tutup Sidebar' : 'Buka Sidebar';
        }
        if (arrow) {
          arrow.setAttribute('d', isOpen ? 'm16 15-3-3 3-3' : 'm13 9 3 3-3 3');
        }
      }

      function toggleAdminSidebar(force) {
        const sidebar = document.getElementById('admin-sidebar');
        const mainContent = document.getElementById('admin-main-content');
        const backdrop = document.getElementById('admin-sidebar-backdrop');
        const collapseBtn = document.getElementById('admin-sidebar-collapse-btn');
        if (!sidebar) return;

        const isMobile = window.innerWidth < 1024;

        if (isMobile) {
          const isCurrentlyOpen = sidebar.classList.contains('translate-x-0');
          const nextState = force !== undefined ? force : !isCurrentlyOpen;

          if (nextState) {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            if (backdrop) {
              backdrop.classList.remove('opacity-0', 'pointer-events-none');
              backdrop.classList.add('opacity-100', 'pointer-events-auto');
            }
            document.body.classList.add('overflow-hidden');
            updateTopSidebarToggleState(true);
          } else {
            sidebar.classList.remove('translate-x-0');
            sidebar.classList.add('-translate-x-full');
            if (backdrop) {
              backdrop.classList.remove('opacity-100', 'pointer-events-auto');
              backdrop.classList.add('opacity-0', 'pointer-events-none');
            }
            document.body.classList.remove('overflow-hidden');
            updateTopSidebarToggleState(false);
          }
        } else {
          // Desktop mode (>= 1024px)
          const isCollapsed = sidebar.classList.contains('sidebar-collapsed');
          const nextCollapse = force !== undefined ? !force : !isCollapsed;

          if (nextCollapse) {
            sidebar.classList.add('sidebar-collapsed');
            if (mainContent) mainContent.classList.add('sidebar-collapsed');
            try {
              localStorage.setItem('admin_sidebar_collapsed', 'true');
            } catch (e) {}
            if (collapseBtn) {
              collapseBtn.setAttribute('aria-expanded', 'false');
            }
            updateTopSidebarToggleState(false);
          } else {
            sidebar.classList.remove('sidebar-collapsed');
            if (mainContent) mainContent.classList.remove('sidebar-collapsed');
            try {
              localStorage.setItem('admin_sidebar_collapsed', 'false');
            } catch (e) {}
            if (collapseBtn) {
              collapseBtn.setAttribute('aria-expanded', 'true');
            }
            updateTopSidebarToggleState(true);
          }

          if (backdrop) {
            backdrop.classList.remove('opacity-100', 'pointer-events-auto');
            backdrop.classList.add('opacity-0', 'pointer-events-none');
          }
          document.body.classList.remove('overflow-hidden');
        }
      }

      function initAdminSidebarState() {
        if (window.innerWidth >= 1024) {
          try {
            const isCollapsed = localStorage.getItem('admin_sidebar_collapsed') === 'true';
            if (isCollapsed) {
              const sidebar = document.getElementById('admin-sidebar');
              const mainContent = document.getElementById('admin-main-content');
              const collapseBtn = document.getElementById('admin-sidebar-collapse-btn');
              if (sidebar) sidebar.classList.add('sidebar-collapsed');
              if (mainContent) mainContent.classList.add('sidebar-collapsed');
              if (collapseBtn) {
                collapseBtn.setAttribute('aria-expanded', 'false');
              }
              updateTopSidebarToggleState(false);
            } else {
              updateTopSidebarToggleState(true);
            }
          } catch (e) {
            updateTopSidebarToggleState(true);
          }
          requestAnimationFrame(() => {
            document.documentElement.classList.remove('admin-sidebar-init-collapsed');
          });
        } else {
          updateTopSidebarToggleState(false);
        }
      }

      function copyAdminEmail(btn, email) {
        if (!email) return;

        const copyAction = navigator.clipboard && navigator.clipboard.writeText
          ? navigator.clipboard.writeText(email)
          : new Promise((resolve, reject) => {
              try {
                const tempInput = document.createElement('textarea');
                tempInput.value = email;
                tempInput.style.position = 'fixed';
                tempInput.style.opacity = '0';
                document.body.appendChild(tempInput);
                tempInput.focus();
                tempInput.select();
                const success = document.execCommand('copy');
                document.body.removeChild(tempInput);
                success ? resolve() : reject();
              } catch (err) {
                reject(err);
              }
            });

        copyAction.then(() => {
          const tooltipText = btn.querySelector('#copy-email-tooltip-text');
          const tooltipIcon = btn.querySelector('#copy-email-icon');
          if (tooltipText) tooltipText.textContent = 'Email tersalin!';
          if (tooltipIcon) {
            tooltipIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />';
            tooltipIcon.classList.remove('text-slate-300');
            tooltipIcon.classList.add('text-emerald-400');
          }
          if (typeof showToast === 'function') {
            showToast('Email panitia berhasil disalin ke clipboard.', 'success');
          }
          setTimeout(() => {
            if (tooltipText) tooltipText.textContent = 'Salin email';
            if (tooltipIcon) {
              tooltipIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />';
              tooltipIcon.classList.remove('text-emerald-400');
              tooltipIcon.classList.add('text-slate-300');
            }
          }, 2000);
        }).catch(() => {
          if (typeof showToast === 'function') {
            showToast('Gagal menyalin email.', 'error');
          }
        });
      }

      function handleAdminNavClick(event, viewName) {
        if (event && (event.ctrlKey || event.metaKey || event.shiftKey || event.button === 1)) {
          return;
        }
        if (event) {
          event.preventDefault();
        }
        switchAdminView(viewName, true, true);
      }

      function switchAdminView(viewName, shouldScroll = false, pushState = true) {
        const validViews = ['beranda', 'status-berkas', 'kuota-rombel', 'tabel-jurusan', 'daftar-ulang', 'keuangan', 'office', 'gelombang', 'log-aktivitas'];
        if (viewName === 'gelombang') {
          viewName = 'office';
        }
        if (!validViews.includes(viewName)) {
          viewName = 'beranda';
        }

        window.currentAdminView = viewName;

        const sectionBeranda = document.getElementById('section-beranda');
        const sectionStatus = document.getElementById('section-status-berkas');
        const sectionKuota = document.getElementById('section-kuota-rombel');
        const sectionTabel = document.getElementById('section-tabel-jurusan');
        const sectionDaftarUlang = document.getElementById('section-daftar-ulang');
        const sectionKeuangan = document.getElementById('section-keuangan');
        const sectionOffice = document.getElementById('section-office');
        const sectionLogAktivitas = document.getElementById('section-log-aktivitas');

        if (sectionBeranda) {
          sectionBeranda.classList.toggle('hidden', viewName !== 'beranda');
          if (viewName === 'beranda') {
            setTimeout(() => {
              if (typeof window.initBerandaMap === 'function') {
                window.initBerandaMap();
              }
              if (typeof window.renderBerandaStats === 'function') {
                window.renderBerandaStats();
              }
              if (window.schoolLeafletMap) {
                window.schoolLeafletMap.invalidateSize();
              }
            }, 150);
          }
        }
        if (sectionStatus) {
          sectionStatus.classList.toggle('hidden', viewName !== 'status-berkas');
        }
        if (sectionKuota) {
          sectionKuota.classList.toggle('hidden', viewName !== 'kuota-rombel');
        }
        if (sectionTabel) {
          sectionTabel.classList.toggle('hidden', viewName !== 'tabel-jurusan');
        }
        if (sectionDaftarUlang) {
          sectionDaftarUlang.classList.toggle('hidden', viewName !== 'daftar-ulang');
        }
        if (sectionKeuangan) {
          sectionKeuangan.classList.toggle('hidden', viewName !== 'keuangan');
        }
        if (sectionOffice) {
          sectionOffice.classList.toggle('hidden', viewName !== 'office');
        }
        if (sectionLogAktivitas) {
          sectionLogAktivitas.classList.toggle('hidden', viewName !== 'log-aktivitas');
        }

        const activeClasses = ['bg-blue-500/20', 'text-white', 'font-semibold'];
        const inactiveClasses = ['text-slate-400', 'hover:text-white', 'hover:bg-white/5', 'font-medium'];

        document.querySelectorAll('[data-admin-view-btn]').forEach(btn => {
          const isActive = btn.dataset.adminViewBtn === viewName;
          const indicator = btn.querySelector('.sidebar-active-indicator');
          const icon = btn.querySelector('.sidebar-icon');
          const label = btn.querySelector('.sidebar-label');

          if (isActive) {
            btn.classList.add(...activeClasses);
            btn.classList.remove(...inactiveClasses);
            if (indicator) {
              indicator.classList.remove('opacity-0', 'pointer-events-none');
              indicator.classList.add('opacity-100');
            }
            if (icon) {
              icon.classList.add('text-blue-500');
              icon.classList.remove('text-slate-400', 'group-hover:text-slate-200');
            }
            if (label) {
              label.classList.add('text-white', 'font-semibold');
              label.classList.remove('text-slate-300', 'group-hover:text-white', 'font-medium');
            }
          } else {
            btn.classList.remove(...activeClasses);
            btn.classList.add(...inactiveClasses);
            if (indicator) {
              indicator.classList.remove('opacity-100');
              indicator.classList.add('opacity-0', 'pointer-events-none');
            }
            if (icon) {
              icon.classList.remove('text-blue-500');
              icon.classList.add('text-slate-400', 'group-hover:text-slate-200');
            }
            if (label) {
              label.classList.remove('text-white', 'font-semibold');
              label.classList.add('text-slate-300', 'group-hover:text-white', 'font-medium');
            }
          }
        });

        const viewRoutes = {
          'beranda': '{{ route("admin.beranda") }}',
          'status-berkas': '{{ route("admin.status-berkas") }}',
          'kuota-rombel': '{{ route("admin.kuota-rombel") }}',
          'tabel-jurusan': '{{ route("admin.tabel-jurusan") }}',
          'daftar-ulang': '{{ route("admin.daftar-ulang") }}',
          'keuangan': '{{ route("admin.keuangan") }}',
          'log-aktivitas': '{{ route("admin.log-aktivitas") }}',
          'office': '{{ route("admin.office") }}'
        };

        if (pushState && history.pushState) {
          const targetUrl = viewRoutes[viewName] || '{{ route("admin.dashboard") }}';
          try {
            const currentPath = window.location.pathname.replace(/\/+$/, '') || '/';
            const targetPath = new URL(targetUrl, window.location.origin).pathname.replace(/\/+$/, '') || '/';
            if (currentPath !== targetPath) {
              history.pushState({ view: viewName }, '', targetUrl);
            }
          } catch (e) {
            history.pushState({ view: viewName }, '', targetUrl);
          }
        }

        if (window.innerWidth < 1024) {
          toggleAdminSidebar(false);
        }

        if (shouldScroll) {
          window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        const viewMeta = {
          'beranda': {
            title: 'Beranda'
          },
          'status-berkas': {
            title: 'Status Berkas'
          },
          'kuota-rombel': {
            title: 'Kuota Rombel'
          },
          'tabel-jurusan': {
            title: 'Tabel Pendaftar'
          },
          'daftar-ulang': {
            title: 'Daftar Ulang'
          },
          'keuangan': {
            title: 'Keuangan & Kas Siswa'
          },
          'log-aktivitas': {
            title: 'Log Aktivitas Admin'
          },
          'office': {
            title: 'Gelombang'
          },
          'gelombang': {
            title: 'Gelombang'
          }
        };

        const meta = viewMeta[viewName] || viewMeta['beranda'];
        const titleEl = document.getElementById('navbar-view-title');
        if (titleEl) titleEl.textContent = meta.title;
      }

      function openStatusInTable(statusValue, event) {
        if (event) {
          event.preventDefault();
          event.stopPropagation();
        }
        if (statusValue === 'daftar-ulang') {
          switchAdminView('daftar-ulang', true, true);
        } else {
          switchAdminView('tabel-jurusan', true, true);
          if (typeof selectFilterOption === 'function') {
            const labels = {
              '': 'Semua Status',
              'Menunggu Verifikasi': 'Menunggu',
              'Diterima': 'Terverifikasi',
              'Perbaikan Berkas': 'Perbaikan',
              'Ditolak': 'Ditolak'
            };
            selectFilterOption('status', statusValue, labels[statusValue] || '');
          }
        }
      }
      window.openStatusInTable = openStatusInTable;

      function initAdminViewFromHash() {
        initAdminView();
      }

      function initAdminView() {
        const path = window.location.pathname.replace(/\/+$/, '');
        if (path.endsWith('/kuota-rombel')) {
          switchAdminView('kuota-rombel', false, false);
          return;
        }
        if (path.endsWith('/tabel-jurusan')) {
          switchAdminView('tabel-jurusan', false, false);
          return;
        }
        if (path.endsWith('/daftar-ulang')) {
          switchAdminView('daftar-ulang', false, false);
          return;
        }
        if (path.endsWith('/keuangan')) {
          switchAdminView('keuangan', false, false);
          return;
        }
        if (path.endsWith('/log-aktivitas')) {
          switchAdminView('log-aktivitas', false, false);
          return;
        }
        if (path.endsWith('/office')) {
          switchAdminView('office', false, false);
          return;
        }
        if (path.endsWith('/status-berkas')) {
          switchAdminView('status-berkas', false, false);
          return;
        }
        if (path.endsWith('/beranda')) {
          switchAdminView('beranda', false, false);
          return;
        }

        const hash = window.location.hash.replace('#', '');
        if (['beranda', 'status-berkas', 'kuota-rombel', 'tabel-jurusan', 'daftar-ulang', 'office', 'gelombang', 'log-aktivitas'].includes(hash)) {
          switchAdminView(hash, false, false);
          return;
        }

        const urlParams = new URLSearchParams(window.location.search);
        const halamanParam = urlParams.get('halaman');
        if (['beranda', 'status-berkas', 'kuota-rombel', 'tabel-jurusan', 'daftar-ulang', 'office', 'gelombang', 'log-aktivitas'].includes(halamanParam)) {
          switchAdminView(halamanParam, false, false);
          return;
        }

        if (urlParams.has('q') || urlParams.has('jurusan') || urlParams.has('status') || urlParams.has('jalur') || urlParams.has('gelombang') || urlParams.has('urutan_registrasi')) {
          switchAdminView('tabel-jurusan', false, false);
          return;
        }

        const currentActiveBtn = document.querySelector('[data-admin-view-btn].bg-blue-600, [data-admin-view-btn].bg-blue-500\\/20');
        if (currentActiveBtn && currentActiveBtn.dataset.adminViewBtn) {
          switchAdminView(currentActiveBtn.dataset.adminViewBtn, false, false);
          return;
        }

        switchAdminView('beranda', false, false);
      }

      window.addEventListener('popstate', (e) => {
        if (e.state && e.state.view) {
          switchAdminView(e.state.view, false, false);
        } else {
          initAdminView();
        }
      });

      window.addEventListener('hashchange', () => {
        const hash = window.location.hash.replace('#', '');
        if (['status-berkas', 'kuota-rombel', 'tabel-jurusan', 'office', 'gelombang'].includes(hash)) {
          switchAdminView(hash, false, false);
        }
      });

      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          const sidebar = document.getElementById('admin-sidebar');
          if (window.innerWidth < 1024 && sidebar && sidebar.classList.contains('translate-x-0')) {
            toggleAdminSidebar(false);
          }
        }
      });

      window.addEventListener('resize', () => {
        const sidebar = document.getElementById('admin-sidebar');
        const backdrop = document.getElementById('admin-sidebar-backdrop');
        if (window.innerWidth >= 1024) {
          if (backdrop) {
            backdrop.classList.remove('opacity-100', 'pointer-events-auto');
            backdrop.classList.add('opacity-0', 'pointer-events-none');
          }
          document.body.classList.remove('overflow-hidden');
          if (sidebar) {
            sidebar.classList.remove('translate-x-0');
            if (!sidebar.classList.contains('sidebar-collapsed')) {
              sidebar.classList.remove('-translate-x-full');
            }
          }
        }
      });

      initAdminViewFromHash();
      initAdminSidebarState();

      function submitAdminFilter(select) {
        if (typeof window.applyAdminFiltersAsync === 'function') {
          window.applyAdminFiltersAsync();
          return;
        }
        sessionStorage.setItem(adminFilterScrollStorageKey, String(window.scrollY));
        select.form.action = "{{ route('admin.dashboard') }}#tabel-jurusan";
        select.form.submit();
      }

      function updateApplicantSelectionUi() {
        const checkboxes = Array.from(adminTableBody.querySelectorAll('[data-applicant-select]'));
        const visibleCheckboxes = checkboxes.filter(checkbox => !checkbox.closest('tr').hidden);
        const selectedVisibleCount = visibleCheckboxes.filter(checkbox => checkbox.checked).length;

        selectAllApplicants.checked = visibleCheckboxes.length > 0 && selectedVisibleCount === visibleCheckboxes.length;
        selectAllApplicants.indeterminate = selectedVisibleCount > 0 && selectedVisibleCount < visibleCheckboxes.length;

        const selectedBadge = document.getElementById('admin-table-selected-badge');
        if (selectedBadge) {
          selectedBadge.textContent = selectedVisibleCount;
        }

        const bulkActionBar = document.getElementById('admin-bulk-action-bar');
        const bulkSelectedCount = document.getElementById('admin-bulk-selected-count');
        const bulkDeleteBtnText = document.getElementById('admin-bulk-delete-btn-text');
        if (bulkActionBar) {
          if (selectedVisibleCount > 0) {
            bulkActionBar.classList.remove('hidden');
            bulkActionBar.classList.add('flex');
            if (bulkSelectedCount) bulkSelectedCount.textContent = selectedVisibleCount;
            if (bulkDeleteBtnText) {
              const isAllSelected = visibleCheckboxes.length > 0 && selectedVisibleCount === visibleCheckboxes.length;
              bulkDeleteBtnText.textContent = isAllSelected ? 'Hapus Semua' : 'Hapus Terpilih';
            }
          } else {
            bulkActionBar.classList.remove('flex');
            bulkActionBar.classList.add('hidden');
            if (bulkSelectedCount) bulkSelectedCount.textContent = '0';
          }
        }
      }

      function clearApplicantSelections() {
        if (!adminTableBody) return;
        adminTableBody.querySelectorAll('[data-applicant-select]').forEach(cb => { cb.checked = false; });
        if (selectAllApplicants) {
          selectAllApplicants.checked = false;
          selectAllApplicants.indeterminate = false;
        }
        updateApplicantSelectionUi();
      }

      function triggerBulkDelete() {
        const bulkDeleteForm = document.getElementById('bulk-delete-form');
        if (bulkDeleteForm) {
          confirmBulkDelete(new Event('submit'));
        }
      }

      function filterApplicantRows() {
        const searchTerm = adminSearchInput.value.trim().toLocaleLowerCase();
        const rows = Array.from(adminTableBody.querySelectorAll('tr[data-registration]'));
        let visibleCount = 0;

        rows.forEach(row => {
          const searchableText = [
            row.querySelector('[data-cell="no_registrasi"]')?.textContent,
            row.querySelector('[data-cell="nama"]')?.textContent,
            row.querySelector('[data-cell="nisn"]')?.textContent,
            row.querySelector('[data-cell="no_whatsapp"]')?.textContent,
            row.querySelector('[data-cell="asal_sekolah"]')?.textContent,
          ].join(' ').toLocaleLowerCase();
          const matches = searchableText.includes(searchTerm);

          row.hidden = !matches;
          if (matches) visibleCount += 1;
        });

        if (adminSearchEmptyState) {
          adminSearchEmptyState.hidden = visibleCount > 0;
        }
        adminTableCount.textContent = `Menampilkan ${visibleCount} pendaftar`;
        updateApplicantSelectionUi();
      }

      if (selectAllApplicants && adminTableBody) {
        selectAllApplicants.addEventListener('change', () => {
          adminTableBody.querySelectorAll('[data-applicant-select]').forEach(checkbox => {
            if (!checkbox.closest('tr').hidden) {
              checkbox.checked = selectAllApplicants.checked;
            }
          });
          updateApplicantSelectionUi();
        });
      }

      if (adminTableBody) {
        adminTableBody.addEventListener('change', event => {
          if (event.target.matches('[data-applicant-select]')) {
            updateApplicantSelectionUi();
          }
        });

        adminTableBody.addEventListener('submit', event => {
          if (!event.target.matches('form[data-registration]')) {
            return;
          }

          const selectedCount = adminTableBody.querySelectorAll('[data-applicant-select]:checked').length;
          if (selectedCount === 0) {
            return;
          }

          event.preventDefault();
          event.stopImmediatePropagation();
          if (bulkDeleteForm) {
            bulkDeleteForm.requestSubmit();
          }
        }, true);
      }

      // MODAL KONFIRMASI HAPUS DATA GLOBAL (Digunakan Bersama: Tabel & Notifikasi)
      window.openDeleteConfirmModal = function({ title, message, confirmText, onConfirm }) {
        const modal = document.getElementById('delete-confirm-modal');
        const box = document.getElementById('delete-confirm-box');
        const titleEl = document.getElementById('delete-confirm-title');
        const msgEl = document.getElementById('delete-confirm-message');
        const submitBtn = document.getElementById('delete-confirm-submit-btn');
        const submitText = document.getElementById('delete-confirm-submit-text');
        const cancelBtn = document.getElementById('delete-confirm-cancel-btn');
        const spinner = document.getElementById('delete-confirm-spinner');

        if (!modal) {
          if (window.confirm(message ? message.replace(/<[^>]*>?/gm, '') : 'Hapus data ini?')) {
            if (typeof onConfirm === 'function') onConfirm();
          }
          return;
        }

        if (title && titleEl) titleEl.textContent = title;
        if (message && msgEl) msgEl.innerHTML = message;
        if (confirmText && submitText) submitText.textContent = confirmText;
        if (spinner) spinner.classList.add('hidden');
        if (submitBtn) submitBtn.disabled = false;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        requestAnimationFrame(() => {
          if (box) {
            box.classList.remove('scale-95', 'opacity-0');
            box.classList.add('scale-100', 'opacity-100');
          }
        });

        const closeModal = () => {
          if (box) {
            box.classList.remove('scale-100', 'opacity-100');
            box.classList.add('scale-95', 'opacity-0');
          }
          setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
          }, 200);
        };

        if (cancelBtn) cancelBtn.onclick = closeModal;
        modal.onclick = (e) => {
          if (e.target === modal) closeModal();
        };

        if (submitBtn) {
          submitBtn.onclick = async () => {
            submitBtn.disabled = true;
            if (spinner) spinner.classList.remove('hidden');
            try {
              if (typeof onConfirm === 'function') {
                await onConfirm();
              }
              closeModal();
            } catch (err) {
              console.error('Error saat konfirmasi hapus data:', err);
              submitBtn.disabled = false;
              if (spinner) spinner.classList.add('hidden');
              if (typeof showToast === 'function') {
                showToast(err.message || 'Gagal menghapus data.', 'error');
              }
            }
          };
        }
      };

      function confirmBulkDelete(event) {
        if (event) {
          event.preventDefault();
        }
        if (!adminTableBody) return false;
        const selectedCheckboxes = Array.from(adminTableBody.querySelectorAll('[data-applicant-select]:checked'));
        const selectedCount = selectedCheckboxes.length;
        if (selectedCount === 0) {
          if (typeof showToast === 'function') showToast('Pilih setidaknya satu pendaftar untuk dihapus.', 'info');
          return false;
        }

        const withPayments = selectedCheckboxes.filter(cb => {
          const row = cb.closest('tr');
          const paid = row ? parseInt(row.getAttribute('data-total-paid') || '0', 10) : 0;
          return paid > 0;
        });

        const ensureRedirectInputs = () => {
          if (bulkDeleteForm) {
            // Bersihkan input no_registrasi lama agar tidak duplikat
            bulkDeleteForm.querySelectorAll('input[name="no_registrasi[]"]').forEach(el => el.remove());

            // Masukkan seluruh no_registrasi terpilih secara eksplisit dan unik ke dalam form
            const uniqueRegs = Array.from(new Set(selectedCheckboxes.map(cb => cb.value).filter(Boolean)));
            uniqueRegs.forEach(val => {
              const hiddenInput = document.createElement('input');
              hiddenInput.type = 'hidden';
              hiddenInput.name = 'no_registrasi[]';
              hiddenInput.value = val;
              bulkDeleteForm.appendChild(hiddenInput);
            });

            if (!bulkDeleteForm.querySelector('input[name="redirect_to"]')) {
              const hiddenRedirect = document.createElement('input');
              hiddenRedirect.type = 'hidden';
              hiddenRedirect.name = 'redirect_to';
              hiddenRedirect.value = window.location.href;
              bulkDeleteForm.appendChild(hiddenRedirect);
            }
            if (!bulkDeleteForm.querySelector('input[name="halaman"]')) {
              const hiddenHalaman = document.createElement('input');
              hiddenHalaman.type = 'hidden';
              hiddenHalaman.name = 'halaman';
              hiddenHalaman.value = window.currentAdminView || 'tabel-jurusan';
              bulkDeleteForm.appendChild(hiddenHalaman);
            }
          }
        };

        const totalVisibleRows = adminTableBody.querySelectorAll('tr[data-registration]:not([hidden])').length;
        const isAllSelected = totalVisibleRows > 0 && selectedCount === totalVisibleRows;
        let modalMessage = `Hapus <strong>${selectedCount} pendaftaran terpilih</strong> beserta seluruh berkas dan data verifikasinya? Tindakan ini akan menghapus data pendaftaran secara permanen dari database.`;
        if (withPayments.length > 0) {
          const regList = withPayments.map(cb => '#' + cb.value).join(', ');
          modalMessage += `<br><span class="inline-block mt-2.5 text-xs font-medium text-amber-800 bg-amber-50 p-2.5 rounded-xl border border-amber-200/80 text-left w-full"><strong>Perhatian:</strong> ${withPayments.length} pendaftar (${regList}) memiliki riwayat transaksi pembayaran. Menghapus data ini akan sekaligus membersihkan riwayat tagihan & pembayarannya dari database.</span>`;
        }

        if (typeof window.openDeleteConfirmModal === 'function') {
          window.openDeleteConfirmModal({
            title: isAllSelected && selectedCount > 1 ? 'Hapus Semua Data Pendaftar' : 'Hapus Data Pendaftar Terpilih',
            message: modalMessage,
            confirmText: `Ya, Hapus ${selectedCount} Data`,
            onConfirm: () => {
              if (bulkDeleteForm) {
                ensureRedirectInputs();
                bulkDeleteForm.submit();
              }
            }
          });
          return false;
        }

        const confirmed = window.confirm(
          `Hapus ${selectedCount} pendaftaran yang dipilih beserta berkas dan data verifikasinya?` +
          (withPayments.length > 0 ? `\n\nPerhatian: ${withPayments.length} pendaftar memiliki riwayat pembayaran yang akan ikut dibersihkan.` : '')
        );
        if (confirmed && bulkDeleteForm) {
          ensureRedirectInputs();
          bulkDeleteForm.submit();
        }
        return false;
      }

      if (adminSearchInput) {
        adminSearchInput.addEventListener('input', filterApplicantRows);
        adminSearchInput.addEventListener('keydown', event => {
          if (event.key === 'Enter') {
            event.preventDefault();
            filterApplicantRows();
          }
        });
      }

      if (adminFilterForm) {
        adminFilterForm.addEventListener('submit', event => {
          event.preventDefault();
          filterApplicantRows();
        });
      }
      filterApplicantRows();

      document.getElementById('drawer-edit-mode')?.addEventListener('keydown', event => {
        if (event.key !== 'Enter' || event.isComposing || !event.target.matches('input, select')) return;

        event.preventDefault();
        saveStudentDataChanges();
      });

      document.addEventListener('keydown', event => {
        if (event.key !== 'Escape') return;

        const deleteModal = document.getElementById('delete-confirm-modal');
        if (deleteModal && !deleteModal.classList.contains('hidden')) {
          deleteModal.classList.add('hidden');
          deleteModal.classList.remove('flex');
          return;
        }

        const documentModal = document.getElementById('doc-modal');
        if (!documentModal.classList.contains('hidden')) {
          closeDocModal();
          return;
        }

        const drawer = document.getElementById('drawer-backdrop');
        if (!drawer.classList.contains('hidden')) {
          closeReviewDrawer();
        }
      });

      function showToast(message, type = 'info') {
        if (typeof window.showToast === 'function') {
          window.showToast(message, type);
        }
      }

      function formatDateDisplay(dateStr) {
        if (!dateStr) return '-';
        const raw = String(dateStr).split('T')[0].split(' ')[0];
        const parts = raw.split('-');
        if (parts.length === 3) {
          const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
          const m = parseInt(parts[1], 10) - 1;
          const month = months[m] || parts[1];
          return `${parts[2]} ${month} ${parts[0]}`;
        }
        return dateStr;
      }

      function normalizeWhatsappNumber(phoneNumber) {
        const digits = String(phoneNumber || '').replace(/\D+/g, '');

        if (digits.startsWith('0')) return `62${digits.slice(1)}`;
        if (digits.startsWith('8')) return `62${digits}`;

        return digits;
      }

      function getStatusBadge(status) {
        if (status === 'Diterima') {
          return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Terverifikasi</span>`;
        } else if (status === 'Ditolak') {
          return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">Ditolak</span>`;
        } else if (status === 'Perbaikan Berkas') {
          return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Perbaikan Berkas</span>`;
        } else {
          return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Menunggu Verifikasi</span>`;
        }
      }

      function getTableStatusBadge(status) {
        if (status === 'Diterima') {
          return '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/80"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>Terverifikasi</span>';
        } else if (status === 'Ditolak') {
          return '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200/80"><span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>Ditolak</span>';
        } else if (status === 'Perbaikan Berkas') {
          return '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200/80"><span class="w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>Perbaikan</span>';
        } else {
          return '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200/80"><span class="w-1.5 h-1.5 rounded-full bg-blue-500 shrink-0"></span>Menunggu</span>';
        }
      }

      function populateApplicantDrawer(item) {
        const setTxt = (id, text) => {
          const el = document.getElementById(id);
          if (el) el.textContent = text;
        };
        const setHtml = (id, html) => {
          const el = document.getElementById(id);
          if (el) el.innerHTML = html;
        };
        const setVal = (id, val) => {
          const el = document.getElementById(id);
          if (el) el.value = val;
        };

        setTxt('drawer-reg-no', item.no_registrasi || '-');
        setHtml('drawer-status-badge', getStatusBadge(item.status_berkas));
        setTxt('drawer-student-name', item.calon_siswa?.nama_lengkap || 'Calon Siswa');
        setTxt('drawer-nisn', item.calon_siswa?.nisn || item.calon_siswa?.user?.email?.split('@')[0] || '-');
        const formattedBirth = formatDateDisplay(item.calon_siswa?.tanggal_lahir);
        setTxt('drawer-ttl', [item.calon_siswa?.tempat_lahir, formattedBirth === '-' ? null : formattedBirth].filter(Boolean).join(', ') || '-');
        setTxt('drawer-jk', item.calon_siswa?.jenis_kelamin || '-');
        setTxt('drawer-agama', item.calon_siswa?.agama || '-');
        setTxt('drawer-wa-siswa', item.calon_siswa?.no_whatsapp || '-');
        setTxt('drawer-sekolah', item.calon_siswa?.asal_sekolah || '-');
        setTxt('drawer-alamat', item.calon_siswa?.alamat_domisili || '-');
        setTxt('drawer-nama-bapak', item.calon_siswa?.nama_bapak || '-');
        setTxt('drawer-nama-ibu', item.calon_siswa?.nama_ibu || '-');
        setTxt('drawer-pekerjaan-bapak', item.calon_siswa?.pekerjaan_bapak || '-');
        setTxt('drawer-pekerjaan-ibu', item.calon_siswa?.pekerjaan_ibu || '-');
        setTxt('drawer-no-wa-ayah', item.calon_siswa?.no_wa_ayah || '-');
        setTxt('drawer-no-wa-ibu', item.calon_siswa?.no_wa_ibu || '-');
        setTxt('drawer-alamat-ortu', item.calon_siswa?.alamat_ortu || '-');
        setTxt('drawer-major-1', item.jurusan?.nama_jurusan || '-');
        const jalurDisplay = (item.jalur?.nama_jalur || '-') + (item.nama_sub_jalur ? ` (${item.nama_sub_jalur})` : '') + (item.voucher_jenis ? ` [Voucher: ${item.voucher_jenis}]` : '');
        setTxt('drawer-jalur', jalurDisplay);
        setTxt('drawer-gelombang', item.gelombang?.nama_gelombang || '-');
        setTxt('drawer-tgl-daftar', formatDateDisplay(item.tgl_daftar));
        setVal('drawer-notes-input', item.verifikasi?.catatan_verifikator || '');

        const foto = (item.berkas || []).find(b => (b.jenis_berkas || '').toLowerCase().includes('foto'));
        const avatarImg = document.getElementById('drawer-student-avatar-img');
        const avatarIcon = document.getElementById('drawer-student-avatar-icon');
        if (avatarImg && avatarIcon) {
          if (foto && foto.path_file) {
            const avatarUrl = getApplicantFileUrl(foto.path_file);
            const fallbackUrl = getFallbackFileUrl(foto.path_file);
            avatarImg.dataset.fallbackTried = '';
            avatarImg.src = avatarUrl + (avatarUrl.includes('?') ? '&' : '?') + 't=' + Date.now();
            avatarImg.onerror = function() {
              if (fallbackUrl && !this.dataset.fallbackTried) {
                this.dataset.fallbackTried = '1';
                this.src = fallbackUrl;
                return;
              }
              this.classList.add('hidden');
              avatarIcon.classList.remove('hidden');
            };
            avatarImg.onload = function() {
              this.classList.remove('hidden');
              avatarIcon.classList.add('hidden');
            };
          } else {
            avatarImg.src = '';
            avatarImg.classList.add('hidden');
            avatarIcon.classList.remove('hidden');
          }
        }

        renderApplicantDocuments(item.berkas || []);
      }

      function updateTableRow(item) {
        const row = document.getElementById(`applicant-row-${item.no_registrasi}`) ||
          document.querySelector(`tr[data-registration="${item.no_registrasi}"]`);
        if (!row) return;

        const cellTgl = row.querySelector('[data-cell="tgl_daftar"]');
        const cellNama = row.querySelector('[data-cell="nama"]');
        const cellNisn = row.querySelector('[data-cell="nisn"]');
        const cellWhatsapp = row.querySelector('[data-cell="no_whatsapp"]');
        const cellSekolah = row.querySelector('[data-cell="asal_sekolah"]');
        const cellJurusan = row.querySelector('[data-cell="jurusan"]');
        const cellJalurGel = row.querySelector('[data-cell="jalur_gelombang"]');
        const cellStatus = row.querySelector('[data-cell="status_berkas"]');

        if (cellTgl) cellTgl.textContent = formatDateDisplay(item.tgl_daftar);
        if (cellNama) cellNama.textContent = item.calon_siswa?.nama_lengkap || '-';
        if (cellNisn) cellNisn.textContent = item.calon_siswa?.nisn || item.calon_siswa?.user?.email?.split('@')[0] || '-';
        if (cellWhatsapp) {
          const whatsappNumber = item.calon_siswa?.no_whatsapp || '';
          const normalizedWhatsappNumber = normalizeWhatsappNumber(whatsappNumber);
          cellWhatsapp.replaceChildren();

          if (normalizedWhatsappNumber) {
            const whatsappLink = document.createElement('a');
            whatsappLink.href = `https://wa.me/${normalizedWhatsappNumber}`;
            whatsappLink.target = '_blank';
            whatsappLink.rel = 'noopener noreferrer';
            whatsappLink.className = 'hover:text-blue-600 hover:underline';
            whatsappLink.title = 'Hubungi Siswa via WhatsApp';
            whatsappLink.textContent = whatsappNumber;
            cellWhatsapp.append(whatsappLink);
          } else {
            cellWhatsapp.textContent = '-';
          }
        }
        if (cellSekolah) cellSekolah.textContent = item.calon_siswa?.asal_sekolah || '-';
        if (cellJurusan) {
          const rawJurusan = item.jurusan?.nama_jurusan || '-';
          cellJurusan.textContent = rawJurusan.split(' ')[0];
          cellJurusan.title = rawJurusan;
        }
        if (cellJalurGel) {
          cellJalurGel.innerHTML = `<span class="text-slate-800 text-[14px] block">${item.jalur?.nama_jalur || '-'}</span><span class="block text-[12.5px] text-slate-500 font-normal mt-0.5">${item.gelombang?.nama_gelombang || '-'}</span>`;
        }
        if (cellStatus) {
          cellStatus.innerHTML = getTableStatusBadge(item.status_berkas);
        }

        row.classList.add('bg-blue-50/80');
        setTimeout(() => row.classList.remove('bg-blue-50/80'), 1500);
      }

      function updateDashboardKpis() {
        const total = pendaftaranData.length;
        const pending = pendaftaranData.filter(p => p.status_berkas === 'Menunggu Verifikasi').length;
        const verified = pendaftaranData.filter(p => {
          if (p.status_berkas !== 'Diterima') return false;
          // Siswa yang sudah daftar ulang masuk kartu "Diterima", bukan "Terverifikasi"
          const duRow = document.getElementById('du-row-' + p.no_registrasi);
          if (duRow) {
            return duRow.getAttribute('data-du-status') !== 'Sudah Daftar Ulang';
          }
          return (p.status_daftar_ulang ?? '') !== 'Sudah Daftar Ulang';
        }).length;
        const revision = pendaftaranData.filter(p => p.status_berkas === 'Perbaikan Berkas').length;
        const rejected = pendaftaranData.filter(p => p.status_berkas === 'Ditolak').length;
        const accepted = pendaftaranData.filter(p => {
          if (p.status_berkas !== 'Diterima') return false;
          const duRow = document.getElementById('du-row-' + p.no_registrasi);
          if (duRow) {
            return duRow.getAttribute('data-du-status') === 'Sudah Daftar Ulang';
          }
          return (p.status_daftar_ulang ?? '') === 'Sudah Daftar Ulang';
        }).length;

        const kpiTotal = document.getElementById('kpi-total');
        const kpiPending = document.getElementById('kpi-pending');
        const kpiVerified = document.getElementById('kpi-verified');
        const kpiRevision = document.getElementById('kpi-revision');
        const kpiRejected = document.getElementById('kpi-rejected');
        const kpiAccepted = document.getElementById('kpi-accepted');

        if (kpiTotal) kpiTotal.textContent = total;
        if (kpiPending) kpiPending.textContent = pending;
        if (kpiVerified) kpiVerified.textContent = verified;
        if (kpiRevision) kpiRevision.textContent = revision;
        if (kpiRejected) kpiRejected.textContent = rejected;
        if (kpiAccepted) kpiAccepted.textContent = accepted;
      }

      function updateJurusanCards() {
        document.querySelectorAll('[data-jurusan-card]').forEach(card => {
          const id = Number(card.dataset.jurusanCard);
          const quota = Number(card.dataset.quota) || 72;
          const registered = pendaftaranData.filter(p => Number(p.id_jurusan) === id).length;
          const accepted = pendaftaranData.filter(p => Number(p.id_jurusan) === id && p.status_berkas === 'Diterima').length;
          const acceptedPercent = quota > 0 ? Math.min(100, Math.round((accepted / quota) * 100)) : 0;

          card.dataset.registered = registered;
          card.dataset.accepted = accepted;

          const countElem = card.querySelector('[data-jurusan-count]');
          const progressElem = card.querySelector('[data-jurusan-progress]');
          const occupancyElem = card.querySelector('[data-jurusan-occupancy]');
          const percentElem = card.querySelector('[data-jurusan-percent]');
          const warningElem = card.querySelector('[data-quota-warning]');

          if (countElem) countElem.textContent = `${accepted} / ${quota}`;
          if (progressElem) progressElem.style.width = `${acceptedPercent}%`;
          if (occupancyElem) occupancyElem.textContent = registered;
          if (percentElem) percentElem.textContent = `${acceptedPercent}% Terisi`;
          if (warningElem) {
            warningElem.classList.toggle('hidden', accepted < quota);
            const msg = warningElem.querySelector('[data-quota-warning-message]');
            if (msg) msg.textContent = `Kuota penerimaan penuh (${accepted}/${quota})`;
          }
        });
      }

      function updateApplicantInMemory(item) {
        currentApplicant = item;
        const applicantIndex = pendaftaranData.findIndex(p => String(p.no_registrasi) === String(item.no_registrasi));
        if (applicantIndex !== -1) pendaftaranData[applicantIndex] = item;
        populateApplicantDrawer(item);
        updateTableRow(item);
        updateDashboardKpis();
        updateJurusanCards();
        if (typeof window.syncNotificationsWithTable === 'function') {
          window.syncNotificationsWithTable(true);
        }
        if (typeof window.syncDaftarUlangRealtime === 'function') {
          window.syncDaftarUlangRealtime(item, item.status_berkas);
        }
      }

      function openReviewDrawer(noReg) {
        const item = pendaftaranData.find(p => String(p.no_registrasi) === String(noReg));
        if (!item) return;

        currentNoRegistrasi = String(noReg);
        currentApplicant = item;
        if (!drawerChangeHistory.has(currentNoRegistrasi)) {
          drawerChangeHistory.set(currentNoRegistrasi, { undo: [], redo: [] });
        }
        updateDrawerHistoryButtons();
        cancelEditStudentData();
        ['drawer-data-status', 'drawer-docs-status'].forEach(id => {
          const status = document.getElementById(id);
          if (status) {
            status.replaceChildren();
            status.className = 'hidden text-xs text-emerald-700';
          }
        });
        try {
          populateApplicantDrawer(item);
        } catch (err) {
          console.error('Gagal mengisi data drawer:', err);
        }
        const backdrop = document.getElementById('drawer-backdrop');
        if (backdrop) {
          backdrop.classList.remove('hidden');
        }
        document.body.classList.add('overflow-hidden');
      }

      function closeReviewDrawer() {
        document.getElementById('drawer-backdrop').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
      }

      function confirmDeleteRegistration(form) {
        const registrationNumber = form.dataset.registration;
        const row = document.getElementById(`applicant-row-${registrationNumber}`);
        const totalPaid = row ? parseInt(row.getAttribute('data-total-paid') || '0', 10) : 0;

        const ensureRedirectInputs = () => {
          let hiddenRedirect = form.querySelector('input[name="redirect_to"]');
          if (!hiddenRedirect) {
            hiddenRedirect = document.createElement('input');
            hiddenRedirect.type = 'hidden';
            hiddenRedirect.name = 'redirect_to';
            form.appendChild(hiddenRedirect);
          }
          hiddenRedirect.value = window.location.href;

          let hiddenHalaman = form.querySelector('input[name="halaman"]');
          if (!hiddenHalaman) {
            hiddenHalaman = document.createElement('input');
            hiddenHalaman.type = 'hidden';
            hiddenHalaman.name = 'halaman';
            form.appendChild(hiddenHalaman);
          }
          hiddenHalaman.value = window.currentAdminView || 'tabel-jurusan';
        };

        let modalMessage = `Hapus data pendaftaran <strong>#${registrationNumber}</strong> beserta seluruh berkas dan data verifikasinya? Tindakan ini akan menghapus data pendaftaran secara permanen dari database.`;
        if (totalPaid > 0) {
          modalMessage += `<br><span class="inline-block mt-2.5 text-xs font-medium text-amber-800 bg-amber-50 p-2.5 rounded-xl border border-amber-200/80 text-left w-full"><strong>Perhatian:</strong> Siswa ini memiliki transaksi pembayaran aktif (Rp ${totalPaid.toLocaleString('id-ID')}). Riwayat pembayaran dan tagihannya akan sekaligus dibersihkan dari sistem.</span>`;
        }

        if (typeof window.openDeleteConfirmModal === 'function') {
          window.openDeleteConfirmModal({
            title: 'Hapus Data Pendaftar',
            message: modalMessage,
            confirmText: 'Ya, Hapus Data',
            onConfirm: () => {
              ensureRedirectInputs();
              form.submit();
            }
          });
          return false;
        }

        const confirmed = window.confirm(
          `Hapus pendaftaran ${registrationNumber} beserta berkas dan data verifikasinya?` +
          (totalPaid > 0 ? `\n\nPerhatian: Siswa ini memiliki transaksi pembayaran (Rp ${totalPaid.toLocaleString('id-ID')}) yang akan ikut dibersihkan.` : '')
        );
        if (confirmed) {
          ensureRedirectInputs();
          form.submit();
        }
        return false;
      }

      function updateApplicantStatusAction(status) {
        if (!currentNoRegistrasi) {
          showToast('Pilih calon siswa terlebih dahulu.', 'warning');
          return;
        }

        if (status === 'Diterima' && currentApplicant.status_berkas !== 'Diterima') {
          const jurusanCard = document.querySelector(`[data-jurusan-card="${currentApplicant.id_jurusan}"]`);
          if (jurusanCard && Number(jurusanCard.dataset.accepted) >= Number(jurusanCard.dataset.quota)) {
            showToast(jurusanCard.querySelector('[data-quota-warning-message]').textContent + '. Tambah kuota maksimal untuk menerima pendaftar lain.', 'warning');
            return;
          }
        }

        const note = document.getElementById('drawer-notes-input').value.trim();
        if (status === 'Perbaikan Berkas' && !note) {
          showToast('Mohon cantumkan catatan verifikator mengenai berkas apa yang perlu diperbaiki oleh siswa.', 'warning');
          document.getElementById('drawer-notes-input').focus();
          return;
        }

        if (!confirm(`Konfirmasi: Apakah Anda yakin ingin menetapkan status pendaftaran ${currentNoRegistrasi} menjadi "${status}"?`)) {
          return;
        }

        const form = document.getElementById('admin-verification-form');
        const csrfToken = form ? (form.querySelector('input[name="_token"]')?.value || '{{ csrf_token() }}') : '{{ csrf_token() }}';

        // Lakukan verifikasi secara realtime via AJAX
        fetch(`/admin/verifikasi/${encodeURIComponent(currentNoRegistrasi)}`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          },
          body: JSON.stringify({
            status_akhir: status,
            catatan_verifikator: note
          })
        })
        .then(async res => {
          if (!res.ok) {
            const errData = await res.json().catch(() => ({}));
            throw new Error(errData.message || 'Gagal memperbarui status verifikasi.');
          }
          return res.json();
        })
        .then(data => {
          showToast(`Status pendaftaran ${currentNoRegistrasi} berhasil diperbarui menjadi "${status}".`, 'success');

          if (currentApplicant) {
            currentApplicant.status_berkas = status;
            if (data.applicant) {
              currentApplicant = Object.assign(currentApplicant, data.applicant);
            }
            updateApplicantInMemory(currentApplicant);
          }

          if (typeof window.syncDaftarUlangRealtime === 'function') {
            window.syncDaftarUlangRealtime(currentApplicant, status);
          }

          if (data && data.log && typeof window.appendAdminActivityLog === 'function') {
            window.appendAdminActivityLog(data.log);
          }

          closeReviewDrawer();
        })
        .catch(err => {
          console.warn('Verifikasi realtime fallback submit form:', err);
          if (form) {
            form.action = `/admin/verifikasi/${currentNoRegistrasi}`;
            document.getElementById('form-status-akhir').value = status;
            document.getElementById('form-catatan-verifikator').value = note;
            form.submit();
          }
        });
      }

      function openKartuPageFromAdmin() {
        if (!currentNoRegistrasi) return;
        window.open(`{{ url('admin/kartu') }}/${encodeURIComponent(currentNoRegistrasi)}`, '_blank');
      }

      function printApplicantCardFromAdmin() {
        if (!currentApplicant) return;
        const item = currentApplicant;

        document.getElementById('admin-print-reg-no').textContent = item.no_registrasi;
        const registrationDate = item.tgl_daftar ? new Date(`${String(item.tgl_daftar).slice(0, 10)}T00:00:00`) : new Date();
        document.getElementById('admin-print-date').textContent = registrationDate.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
        document.getElementById('admin-print-status').textContent = item.status_berkas || 'Tersimpan';

        document.getElementById('admin-print-nama').textContent = item.calon_siswa?.nama_lengkap || '-';
        document.getElementById('admin-print-nisn').textContent = item.calon_siswa?.nisn || item.calon_siswa?.user?.email?.split('@')[0] || '-';
        document.getElementById('admin-print-ttl').textContent = [item.calon_siswa?.tempat_lahir, item.calon_siswa?.tanggal_lahir].filter(Boolean).join(', ') || '-';
        document.getElementById('admin-print-jk').textContent = item.calon_siswa?.jenis_kelamin || '-';
        document.getElementById('admin-print-agama').textContent = item.calon_siswa?.agama || '-';
        document.getElementById('admin-print-sekolah').textContent = item.calon_siswa?.asal_sekolah || '-';
        document.getElementById('admin-print-alamat').textContent = item.calon_siswa?.alamat_domisili || '-';
        document.getElementById('admin-print-wa-siswa').textContent = item.calon_siswa?.no_whatsapp || '-';
        document.getElementById('admin-print-email-siswa').textContent = item.calon_siswa?.user?.email || '-';

        document.getElementById('admin-print-bapak').textContent = item.calon_siswa?.nama_bapak || '-';
        document.getElementById('admin-print-pekerjaan-bapak').textContent = item.calon_siswa?.pekerjaan_bapak || '-';
        document.getElementById('admin-print-ibu').textContent = item.calon_siswa?.nama_ibu || '-';
        document.getElementById('admin-print-pekerjaan-ibu').textContent = item.calon_siswa?.pekerjaan_ibu || '-';
        document.getElementById('admin-print-no-wa-ayah').textContent = item.calon_siswa?.no_wa_ayah || '-';
        document.getElementById('admin-print-no-wa-ibu').textContent = item.calon_siswa?.no_wa_ibu || '-';
        document.getElementById('print-no-wa-ayah').textContent = item.calon_siswa?.no_wa_ayah || '-';
        document.getElementById('print-no-wa-ibu').textContent = item.calon_siswa?.no_wa_ibu || '-';
        document.getElementById('admin-print-alamat-ortu').textContent = item.calon_siswa?.alamat_ortu || '-';

        document.getElementById('admin-print-jurusan').textContent = item.jurusan?.nama_jurusan || '-';
        document.getElementById('admin-print-jalur').textContent = item.jalur?.nama_jalur || '-';
        document.getElementById('admin-print-gelombang').textContent = item.gelombang?.nama_gelombang || '-';
        document.getElementById('admin-print-ttd-nama').textContent = '( ' + (item.calon_siswa?.nama_lengkap || 'Calon Siswa') + ' )';

        const photoBox = document.getElementById('admin-print-photo-box');
        const foto = (item.berkas || []).find(b => (b.jenis_berkas || '').toLowerCase().includes('foto'));
        photoBox.replaceChildren();
        const photoUrl = foto ? getApplicantFileUrl(foto.path_file) : null;
        const photoFallbackUrl = foto ? getFallbackFileUrl(foto.path_file) : null;

        const printArea = document.getElementById('printable-id-card');
        const triggerPrint = () => {
          printArea.classList.remove('hidden');
          setTimeout(() => {
            window.print();
            printArea.classList.add('hidden');
          }, 80);
        };

        if (photoUrl) {
          const photo = document.createElement('img');
          photo.className = 'w-full h-full object-cover';
          photo.alt = 'Foto';

          let hasTriggered = false;
          const onReady = () => {
            if (!hasTriggered) {
              hasTriggered = true;
              triggerPrint();
            }
          };

          photo.onload = onReady;
          photo.onerror = function() {
            if (photoFallbackUrl && !this.dataset.fallbackTried) {
              this.dataset.fallbackTried = '1';
              this.src = photoFallbackUrl;
              return;
            }
            onReady();
          };

          photo.src = photoUrl + (photoUrl.includes('?') ? '&' : '?') + 't=' + Date.now();
          photoBox.appendChild(photo);

          if (photo.complete) {
            onReady();
          } else {
            setTimeout(onReady, 1200);
          }
        } else {
          const placeholder = document.createElement('span');
          placeholder.className = 'text-[9px] text-slate-400 font-bold uppercase';
          placeholder.textContent = 'Pasfoto 3x4';
          photoBox.appendChild(placeholder);
          triggerPrint();
        }
      }

      function setTemplateNote(text) {
        document.getElementById('drawer-notes-input').value = text;
      }

      function viewDocModal(title, url, isImage) {
        document.getElementById('doc-modal-title').textContent = title;
        const body = document.getElementById('doc-modal-body');
        const safeUrl = getApplicantFileUrl(url);
        const fallbackUrl = getFallbackFileUrl(url);
        if (!safeUrl) return;

        body.replaceChildren();
        if (isImage) {
          const image = document.createElement('img');
          image.src = safeUrl + (safeUrl.includes('?') ? '&' : '?') + 't=' + Date.now();
          image.className = 'max-h-[70vh] mx-auto rounded-2xl shadow-md border border-slate-100 object-contain';
          image.alt = title;
          image.onerror = function() {
            if (fallbackUrl && !this.dataset.fallbackTried) {
              this.dataset.fallbackTried = '1';
              this.src = fallbackUrl;
              return;
            }
            body.innerHTML = '<div class="p-6 text-center text-rose-500 font-semibold"><p>Gagal memuat pratinjau gambar.</p><p class="text-xs text-slate-400 mt-1">Pastikan berkas gambar valid.</p></div>';
          };
          body.appendChild(image);
        } else {
          const frame = document.createElement('iframe');
          frame.src = safeUrl;
          frame.title = title;
          frame.className = 'w-full h-[70vh] rounded-2xl border border-slate-200';
          body.appendChild(frame);
        }
        const docModal = document.getElementById('doc-modal');
        if (docModal) {
          docModal.classList.remove('hidden');
          docModal.classList.add('flex');
        }
      }

      function closeDocModal() {
        const docModal = document.getElementById('doc-modal');
        if (docModal) {
          docModal.classList.add('hidden');
          docModal.classList.remove('flex');
        }
        document.getElementById('doc-modal-body').replaceChildren();
      }

      const appBaseUrl = "{{ asset('') }}";
      const storageRouteTemplate = "{{ route('storage.berkas', ['filename' => '__FILENAME__']) }}";

      function getApplicantFileUrl(path) {
        if (!path) return null;
        const normalizedPath = String(path).replace(/\\/g, '/').replace(/^\/+/, '');
        if (!normalizedPath || normalizedPath.split('/').includes('..')) return null;

        const encodedPath = normalizedPath.split('/').map(segment => encodeURIComponent(segment)).join('/');
        try {
          return new URL(encodedPath, appBaseUrl).href;
        } catch (e) {
          return appBaseUrl + encodedPath;
        }
      }

      function getFallbackFileUrl(path) {
        if (!path) return null;
        const filename = String(path).replace(/\\/g, '/').split('/').pop();
        if (!filename) return null;
        return storageRouteTemplate.replace('__FILENAME__', encodeURIComponent(filename));
      }

      const applicantDocumentTypes = (() => {
        try {
          const raw = document.getElementById('admin-doc-types')?.textContent;
          return raw ? JSON.parse(raw) : [];
        } catch (e) {
          return [];
        }
      })();

      function getApplicableDocumentTypes(berkas) {
        const baseKeys = ['berkas_foto', 'berkas_kk', 'berkas_akta'];
        const types = [];
        applicantDocumentTypes.forEach(def => {
          const isUploaded = (berkas || []).some(item => item.jenis_berkas === def.label);
          if (baseKeys.includes(def.inputName) || isUploaded) {
            types.push(def);
          }
        });
        return types;
      }

      function renderApplicantDocuments(berkas) {
        const docsContainer = document.getElementById('drawer-docs-list');
        docsContainer.replaceChildren();

        const activeDocTypes = getApplicableDocumentTypes(berkas);
        activeDocTypes.forEach(documentType => {
          const berkasItem = berkas.find(item => item.jenis_berkas === documentType.label);
          const fileUrl = berkasItem ? getApplicantFileUrl(berkasItem.path_file) : null;
          const fallbackUrl = berkasItem ? getFallbackFileUrl(berkasItem.path_file) : null;
          const isPdf = (berkasItem?.path_file || '').toLowerCase().endsWith('.pdf');
          const isImageDoc = berkasItem && !isPdf && fileUrl;

          const docCard = document.createElement('div');
          docCard.className = 'flex items-center justify-between gap-3 p-3 rounded-xl border border-blue-100/80 bg-blue-50/30 text-xs';

          const fileInfo = document.createElement('div');
          fileInfo.className = 'flex items-center gap-3 min-w-0';

          const fileType = document.createElement('div');
          fileType.className = 'w-8 h-8 rounded-xl bg-blue-600 border-2 border-white shadow-sm flex items-center justify-center shrink-0 overflow-hidden';
          const categoryIcon = document.createElement('img');
          categoryIcon.src = documentType.icon;
          categoryIcon.alt = '';
          categoryIcon.className = 'w-4 h-4 object-contain brightness-0 invert';

          if (isImageDoc) {
            const photoThumb = document.createElement('img');
            photoThumb.src = fileUrl + (fileUrl.includes('?') ? '&' : '?') + 't=' + Date.now();
            photoThumb.alt = documentType.label;
            photoThumb.className = 'w-full h-full object-cover';
            photoThumb.onerror = function() {
              if (fallbackUrl && !this.dataset.fallbackTried) {
                this.dataset.fallbackTried = '1';
                this.src = fallbackUrl;
                return;
              }
              this.replaceWith(categoryIcon);
            };
            fileType.appendChild(photoThumb);
          } else {
            fileType.appendChild(categoryIcon);
          }

          const fileLabels = document.createElement('div');
          fileLabels.className = 'min-w-0';
          const fileName = document.createElement('span');
          fileName.className = 'block truncate font-medium text-slate-800';
          fileName.textContent = documentType.label;
          const fileMetadata = document.createElement('span');
          fileMetadata.className = 'block truncate text-[11px] text-slate-500';
          fileMetadata.textContent = berkasItem
            ? `${berkasItem.nama_file || 'Berkas'} · ${Math.round((berkasItem.ukuran_file || 0) / 1024)} KB`
            : 'Belum ada dokumen';
          fileLabels.append(fileName, fileMetadata);
          fileInfo.append(fileType, fileLabels);

          const fileActions = document.createElement('div');
          fileActions.className = 'flex items-center gap-1.5 shrink-0';
          let downloadButton = null;

          if (berkasItem && fileUrl) {
            const previewButton = document.createElement('button');
            previewButton.type = 'button';
            previewButton.className = 'px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition';
            previewButton.textContent = 'Lihat';
            previewButton.addEventListener('click', () => viewDocModal(documentType.label, berkasItem.path_file, !isPdf));
            fileActions.appendChild(previewButton);

            if (!isPdf) {
              downloadButton = document.createElement('a');
              downloadButton.href = fileUrl;
              downloadButton.download = berkasItem.nama_file || `${documentType.label}.jpg`;
              downloadButton.title = `Unduh ${documentType.label}`;
              downloadButton.setAttribute('aria-label', `Unduh ${documentType.label}`);
              downloadButton.className = 'inline-flex items-center justify-center w-7 h-7 rounded-lg bg-white text-slate-700 hover:bg-slate-50 border border-slate-200 transition';

              const downloadIcon = document.createElement('img');
              downloadIcon.src = downloadIconSource;
              downloadIcon.alt = '';
              downloadIcon.className = 'w-3.5 h-3.5 object-contain';
              downloadButton.appendChild(downloadIcon);
            }
          }

          const fileInput = document.createElement('input');
          fileInput.type = 'file';
          fileInput.accept = documentType.accept;
          fileInput.className = 'hidden';

          const replaceButton = document.createElement('button');
          replaceButton.type = 'button';
          replaceButton.className = 'px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-white text-slate-700 hover:bg-slate-50 border border-slate-200 transition disabled:opacity-50';
          replaceButton.textContent = berkasItem ? 'Ganti File' : 'Unggah File';
          replaceButton.addEventListener('click', () => fileInput.click());
          fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
              replaceApplicantDocument(fileInput, documentType, replaceButton, fileType);
            }
          });

          fileActions.append(fileInput, replaceButton);
          if (downloadButton) {
            fileActions.appendChild(downloadButton);
          }
          docCard.append(fileInfo, fileActions);
          docsContainer.appendChild(docCard);
        });
      }

      function setUndoStatus(element, undoToken = null) {
        element.replaceChildren();
        element.className = 'hidden text-xs text-emerald-700';

        if (undoToken) {
          const history = drawerChangeHistory.get(currentNoRegistrasi);
          history.undo.push(undoToken);
          history.redo = [];
          updateDrawerHistoryButtons();
        }
      }

      function updateDrawerHistoryButtons() {
        const history = drawerChangeHistory.get(currentNoRegistrasi) || { undo: [], redo: [] };
        const undoButton = document.getElementById('drawer-undo-button');
        const redoButton = document.getElementById('drawer-redo-button');
        undoButton.disabled = historyRequestPending || history.undo.length === 0;
        redoButton.disabled = historyRequestPending || history.redo.length === 0;
      }

      async function applyDrawerHistoryChange(fromStack, toStack, direction) {
        const history = drawerChangeHistory.get(currentNoRegistrasi);
        if (!history || historyRequestPending || history[fromStack].length === 0) return;

        historyRequestPending = true;
        updateDrawerHistoryButtons();

        try {
          const response = await fetch(document.getElementById('drawer-backdrop').dataset.undoUrl, {
            method: 'POST',
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('#admin-verification-form input[name="_token"]').value,
            },
            body: JSON.stringify({ undo_token: history[fromStack][history[fromStack].length - 1] }),
          });
          const result = await response.json().catch(() => ({}));
          if (!response.ok) throw new Error(result.message || `Perubahan gagal di-${direction}.`);

          history[fromStack].pop();
          history[toStack].push(result.reverse_token);
          updateApplicantInMemory(result.applicant);
          const message = `Perubahan berhasil di-${direction}.`;
          showToast(message, 'info');
        } catch (error) {
          showToast(error.message || `Perubahan gagal di-${direction}.`, 'error');
        } finally {
          historyRequestPending = false;
          updateDrawerHistoryButtons();
        }
      }

      function undoDrawerChange() {
        applyDrawerHistoryChange('undo', 'redo', 'undo');
      }

      function redoDrawerChange() {
        applyDrawerHistoryChange('redo', 'undo', 'redo');
      }

      async function replaceApplicantDocument(fileInput, documentType, replaceButton, fileTypeDiv) {
        const file = fileInput.files[0];
        const status = document.getElementById('drawer-docs-status');
        if (!file) return;

        if (file.size > 10 * 1024 * 1024) {
          status.className = 'text-xs text-rose-600';
          status.textContent = 'Ukuran file maksimal 10 MB.';
          showToast('Ukuran file maksimal 10 MB.', 'error');
          fileInput.value = '';
          return;
        }

        const isImage = (file.type && file.type.startsWith('image/')) || /\.(jpe?g|png|webp|gif|bmp|svg)$/i.test(file.name);
        if (isImage && fileTypeDiv) {
          const reader = new FileReader();
          reader.onload = function(e) {
            fileTypeDiv.replaceChildren();
            const localImg = document.createElement('img');
            localImg.src = e.target.result;
            localImg.className = 'w-full h-full object-cover';
            fileTypeDiv.appendChild(localImg);

            if (documentType.inputName === 'berkas_foto') {
              const avatarImg = document.getElementById('drawer-student-avatar-img');
              const avatarIcon = document.getElementById('drawer-student-avatar-icon');
              if (avatarImg) {
                avatarImg.src = e.target.result;
                avatarImg.classList.remove('hidden');
                if (avatarIcon) avatarIcon.classList.add('hidden');
              }
            }
          };
          reader.readAsDataURL(file);
        }

        replaceButton.disabled = true;
        status.className = 'text-xs text-slate-500';
        status.textContent = `Mengunggah ${documentType.label}...`;

        try {
          const docsContainer = document.getElementById('drawer-docs-list');
          const endpoint = docsContainer.dataset.urlTemplate.replace(
            '__REGISTRATION__',
            encodeURIComponent(currentNoRegistrasi),
          );
          const formData = new FormData();
          formData.append(documentType.inputName, file);
          formData.append('_token', document.getElementById('drawer-docs-csrf').value);
          const response = await fetch(endpoint, {
            method: 'POST',
            headers: { 'Accept': 'application/json' },
            body: formData,
          });
          const result = await response.json().catch(() => ({}));
          if (!response.ok) {
            const validationMessage = Object.values(result.errors || {}).flat()[0];
            throw new Error(validationMessage || result.message || 'Dokumen gagal diganti.');
          }

          updateApplicantInMemory(result.applicant);
          setUndoStatus(document.getElementById('drawer-docs-status'), result.undo_token);
          showToast(`${documentType.label} berhasil disimpan realtime.`, 'success');
        } catch (error) {
          status.className = 'text-xs text-rose-600';
          status.textContent = error.message || 'Dokumen gagal diganti.';
          showToast(error.message || 'Dokumen gagal diganti.', 'error');
        } finally {
          replaceButton.disabled = false;
          fileInput.value = '';
        }
      }

      function formatExportDateDisplay(dateStr) {
        if (!dateStr) return '-';

        const raw = String(dateStr).split('T')[0].split(' ')[0];
        const parts = raw.split('-');
        if (parts.length !== 3) return String(dateStr);

        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const month = months[Number.parseInt(parts[1], 10) - 1] || parts[1];

        return `${parts[2]} ${month} ${parts[0]}`;
      }

      window.exportRowsToExcel = function (headers, rows, filename, sheetName) {
        if (!window.XLSX) {
          showToast('Library ekspor Excel belum tersedia. Silakan muat ulang halaman.', 'error');
          return;
        }

        const cleanText = value => {
          const text = String(value ?? '').replace(/\s+/g, ' ').trim();
          return /^\d{4}-\d{2}-\d{2}T/.test(text) ? formatExportDateDisplay(text) : text;
        };
        const cleanRows = rows.map(row => row.map(value => (
          typeof value === 'string' ? cleanText(value) : value ?? ''
        )));
        const worksheet = XLSX.utils.aoa_to_sheet([
          headers.map(cleanText),
          ...cleanRows,
        ]);

        worksheet['!cols'] = headers.map((header, columnIndex) => {
          const longestValue = [header, ...cleanRows.map(row => row[columnIndex])]
            .reduce((longest, value) => Math.max(longest, Array.from(cleanText(value)).length), 0);

          return { wch: Math.max(longestValue, 1) };
        });

        cleanRows.forEach((row, rowIndex) => {
          row.forEach((value, columnIndex) => {
            const textValue = String(value ?? '');
            if (!/^\d+$/.test(textValue) || (!textValue.startsWith('0') && textValue.length < 10)) {
              return;
            }

            const cellAddress = XLSX.utils.encode_cell({ r: rowIndex + 1, c: columnIndex });
            worksheet[cellAddress] = {
              ...(worksheet[cellAddress] || {}),
              t: 's',
              v: textValue,
              z: '@',
            };
          });
        });

        try {
          const workbook = XLSX.utils.book_new();
          XLSX.utils.book_append_sheet(workbook, worksheet, sheetName.slice(0, 31));
          XLSX.writeFile(workbook, filename);
        } catch (error) {
          console.error('Gagal mengekspor data ke Excel:', error);
          showToast('Data gagal diekspor ke Excel. Silakan coba lagi.', 'error');
        }
      };

      function getExportPathValue(record, path) {
        return path.split('.').reduce((value, key) => value?.[key], record);
      }

      function exportToExcel() {
        if (!pendaftaranData || pendaftaranData.length === 0) {
          showToast('Tidak ada data untuk diekspor.', 'warning');
          return;
        }

        let targetData = pendaftaranData;
        if (adminTableBody) {
          const selectedCheckboxes = Array.from(adminTableBody.querySelectorAll('[data-applicant-select]:checked'));
          if (selectedCheckboxes.length > 0) {
            const selectedSet = new Set(selectedCheckboxes.map(cb => cb.value));
            targetData = pendaftaranData.filter(p => selectedSet.has(p.no_registrasi));
          } else {
            const visibleRows = Array.from(adminTableBody.querySelectorAll('tr[data-registration]:not([hidden])'));
            if (visibleRows.length > 0 && visibleRows.length < pendaftaranData.length) {
              const visibleSet = new Set(visibleRows.map(r => r.dataset.registration));
              targetData = pendaftaranData.filter(p => visibleSet.has(p.no_registrasi));
            }
          }
        }

        if (targetData.length === 0) {
          showToast('Tidak ada data yang cocok untuk diekspor.', 'warning');
          return;
        }

        const exportColumns = [
          { label: 'No. Registrasi', paths: ['no_registrasi'] },
          { label: 'Nama Lengkap Siswa', paths: ['calon_siswa.nama_lengkap'] },
          {
            label: 'NISN / ID Login',
            paths: ['calon_siswa.nisn'],
            fallbackPath: 'calon_siswa.user.email',
            format: 'email-prefix',
          },
          { label: 'WhatsApp Siswa', paths: ['calon_siswa.no_whatsapp'] },
          { label: 'Tempat Lahir', paths: ['calon_siswa.tempat_lahir'] },
          { label: 'Tanggal Lahir', paths: ['calon_siswa.tanggal_lahir'], format: 'date-display' },
          { label: 'Jenis Kelamin', paths: ['calon_siswa.jenis_kelamin'] },
          { label: 'Agama', paths: ['calon_siswa.agama'] },
          { label: 'Asal SMP/MTs', paths: ['calon_siswa.asal_sekolah'] },
          { label: 'Alamat Domisili Siswa', paths: ['calon_siswa.alamat_domisili'] },
          { label: 'Nama Ayah / Wali', paths: ['calon_siswa.nama_bapak'] },
          { label: 'Pekerjaan Ayah', paths: ['calon_siswa.pekerjaan_bapak'] },
          { label: 'Nama Ibu Kandung', paths: ['calon_siswa.nama_ibu'] },
          { label: 'Pekerjaan Ibu', paths: ['calon_siswa.pekerjaan_ibu'] },
          { label: 'No. WhatsApp Ayah', paths: ['calon_siswa.no_wa_ayah'] },
          { label: 'No. WhatsApp Ibu', paths: ['calon_siswa.no_wa_ibu'] },
          { label: 'Alamat Orang Tua / Wali', paths: ['calon_siswa.alamat_ortu'] },
          { label: 'Kompetensi Keahlian (Jurusan)', paths: ['jurusan.nama_jurusan'] },
          { label: 'Jalur Pendaftaran', paths: ['jalur.nama_jalur'] },
          { label: 'Gelombang', paths: ['gelombang.nama_gelombang'] },
          { label: 'Status Verifikasi Berkas', paths: ['status_berkas'] },
          { label: 'Tanggal Pendaftaran', paths: ['tgl_daftar'], format: 'date-display' },
          { label: 'Catatan Verifikator', paths: ['verifikasi.catatan_verifikator'] },
        ];

        const headers = exportColumns.map(column => column.label);
        const rows = targetData.map(applicant => exportColumns.map(column => {
          let value = column.paths
            .map(path => getExportPathValue(applicant, path))
            .filter(value => value !== null && value !== undefined && value !== '')
            .join(' - ');

          if (!value && column.fallbackPath) {
            value = getExportPathValue(applicant, column.fallbackPath) || '';
          }

          if (column.format === 'email-prefix' && typeof value === 'string') {
            value = value.split('@', 1)[0];
          } else if (column.format === 'status-berkas') {
            value = ({
              Diterima: 'Terverifikasi',
              Ditolak: 'Ditolak',
              'Perbaikan Berkas': 'Perbaikan',
            })[value] || 'Menunggu';
          } else if (column.format === 'date-display') {
            value = formatExportDateDisplay(value);
          }

          return value;
        }));

        window.exportRowsToExcel(
          headers,
          rows,
          `rekap_spmb_${new Date().toISOString().slice(0, 10)}.xlsx`,
          'Data Pendaftar'
        );
      }

      function resetDemoData() {
        showToast('Sistem terhubung langsung dengan database. Data yang ditampilkan adalah data riil.', 'info');
      }

      document.querySelectorAll('[data-quota-form]').forEach(form => {
        const input = form.elements.kuota;
        const button = form.querySelector('button[type="submit"]');
        const cancelButton = form.querySelector('[data-quota-cancel]');
        const status = form.querySelector('[data-quota-status]');
        const editor = form.closest('[data-quota-editor]');
        const card = form.closest('[data-jurusan-card]');
        const editButton = card.querySelector('[data-quota-edit]');

        editButton.addEventListener('click', () => {
          status.textContent = '';
          editor.classList.remove('hidden');
          editButton.classList.add('hidden');
          input.focus();
          input.select();
        });

        cancelButton.addEventListener('click', () => {
          input.value = card.dataset.quota || input.defaultValue;
          status.textContent = '';
          editor.classList.add('hidden');
          editButton.classList.remove('hidden');
        });

        form.addEventListener('submit', async event => {
          event.preventDefault();

          const quota = Number(input.value);

          if (!Number.isInteger(quota) || quota < 1 || quota > 2147483647) {
            input.focus();
            return;
          }

          button.disabled = true;
          cancelButton.disabled = true;
          input.disabled = true;
          status.className = 'text-[10px] text-slate-500';
          status.textContent = 'Menyimpan...';

          try {
            const response = await fetch(form.action, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
              },
              body: JSON.stringify({ kuota: quota }),
            });
            const data = await response.json();

            if (!response.ok) {
              throw new Error(data.errors?.kuota?.[0] || 'Kuota gagal disimpan.');
            }
            if (!data.success || Number(data.kuota) !== quota) {
              throw new Error('Server tidak mengonfirmasi perubahan kuota.');
            }

            const registered = Number(card.dataset.registered);
            const accepted = Number(card.dataset.accepted);
            const acceptedPercentage = Math.min(100, Math.round((accepted / quota) * 100));
            card.dataset.quota = quota;
            card.querySelector('[data-jurusan-count]').textContent = `${accepted} / ${quota}`;
            card.querySelector('[data-jurusan-progress]').style.width = `${acceptedPercentage}%`;
            card.querySelector('[data-jurusan-occupancy]').textContent = registered;
            const cardPercentElem = card.querySelector('[data-jurusan-percent]');
            if (cardPercentElem) cardPercentElem.textContent = `${acceptedPercentage}% Terisi`;
            const warning = card.querySelector('[data-quota-warning]');
            warning.classList.toggle('hidden', accepted < quota);
            warning.querySelector('[data-quota-warning-message]').textContent = `Kuota penerimaan penuh (${accepted}/${quota})`;
            if (typeof window.updateKuotaJurusan === 'function') {
              window.updateKuotaJurusan(card.dataset.jurusanCard, quota);
            }
            status.textContent = '';
            editor.classList.add('hidden');
            editButton.classList.remove('hidden');
          } catch (error) {
            status.className = 'text-[10px] text-rose-600';
            status.textContent = error.message || 'Kuota gagal disimpan.';
          } finally {
            button.disabled = false;
            cancelButton.disabled = false;
            input.disabled = false;
          }
        });
      });

      function selectAdminWave(waveName) {
        const select = document.getElementById('admin-wave-select');
        if (!select || select.disabled || select.getAttribute('aria-busy') === 'true') return;
        if (select.dataset.currentWave === waveName) return;

        select.value = waveName;
        handleSetWave(select);
      }

      async function handleSetWave(select) {
        const waveName = select.value;
        const previousWave = select.dataset.currentWave;
        select.disabled = true;
        select.setAttribute('aria-busy', 'true');

        const miniCards = document.querySelectorAll('[data-wave-card]');
        miniCards.forEach(c => c.classList.add('pointer-events-none', 'opacity-60'));

        try {
          const res = await fetch("{{ route('admin.setWave') }}", {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'Accept': 'application/json'
            },
            body: JSON.stringify({ wave: waveName })
          });
          if (!res.ok) throw new Error(`HTTP ${res.status}`);

          const data = await res.json();
          if (!data.success || data.wave !== waveName) throw new Error('Server tidak mengonfirmasi perubahan gelombang.');

          select.dataset.currentWave = waveName;

          // 1. Update Main Card Title
          const mainTitle = document.getElementById('office-wave-main-title');
          if (mainTitle) {
            mainTitle.textContent = waveName === 'Tutup Gelombang' ? 'Pendaftaran Ditutup' : waveName;
          }

          // 2. Update Main Card Badge
          const badge = document.getElementById('office-wave-badge');
          const badgeText = document.getElementById('office-wave-badge-text');
          const badgeDot = document.getElementById('office-wave-badge-dot');
          if (badge) {
            if (waveName === 'Tutup Gelombang') {
              badge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200';
              if (badgeDot) badgeDot.className = 'w-1.5 h-1.5 rounded-full bg-rose-500';
              if (badgeText) badgeText.textContent = 'Pendaftaran Sedang Ditutup';
            } else {
              badge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200';
              if (badgeDot) badgeDot.className = 'w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse';
              if (badgeText) badgeText.textContent = 'Sedang Aktif Dibuka';
            }
          }

          // 2.5 Update Background Aura Glow
          const aura = document.getElementById('office-wave-bg-aura');
          if (aura) {
            if (waveName === 'Tutup Gelombang') {
              aura.className = 'absolute -top-24 left-1/2 -translate-x-1/2 w-80 h-80 bg-rose-500/10 rounded-full blur-3xl pointer-events-none transition-colors duration-500';
            } else {
              aura.className = 'absolute -top-24 left-1/2 -translate-x-1/2 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl pointer-events-none transition-colors duration-500';
            }
          }

          // 3. Update Main Card Date
          const dateEl = document.getElementById('office-wave-date');
          const dateTextEl = document.getElementById('office-wave-date-text');
          const dateIcon = dateEl ? dateEl.querySelector('svg') : null;
          const dateMap = {
            'Gelombang Khusus': '07 September 2026 – 01 Januari 2027',
            'Gelombang 1': '02 Januari 2027 – 11 Mei 2027',
            'Gelombang 2': '12 Mei 2027 – 19 Juli 2027'
          };
          if (dateEl) {
            if (waveName === 'Tutup Gelombang') {
              dateEl.classList.add('hidden');
              if (dateTextEl) dateTextEl.textContent = '';
            } else {
              dateEl.classList.remove('hidden');
              if (dateTextEl) dateTextEl.textContent = dateMap[waveName] || '';
              dateEl.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-slate-50 text-slate-600 border border-slate-200';
              if (dateIcon) dateIcon.className = 'w-3.5 h-3.5 text-blue-500 shrink-0';
            }
          }

          // 4. Update Main Card Description
          const desc = document.getElementById('office-wave-description');
          if (desc) {
            const descMap = {
              'Gelombang Khusus': 'Calon siswa dapat mendaftar melalui jalur khusus / prestasi sebelum gelombang reguler dibuka.',
              'Gelombang 1': 'Pendaftaran gelombang 1 dibuka untuk umum bagi seluruh program keahlian.',
              'Gelombang 2': 'Pendaftaran gelombang 2 sedang berlangsung hingga batas akhir penutupan kuota.',
              'Tutup Gelombang': 'Portal registrasi online SPMB ditutup sementara untuk calon siswa baru.'
            };
            desc.textContent = descMap[waveName] || '';
          }

          // 5. Update the 4 Mini Cards active styles
          miniCards.forEach(card => {
            const cardWave = card.dataset.waveCard;
            const isTarget = cardWave === waveName;
            const pill = card.querySelector('.wave-card-pill');
            const iconBox = card.querySelector('div > div:first-child');
            const titleEl = card.querySelector('h4');
            const dateRow = card.querySelector('.wave-card-date');

            if (isTarget) {
              if (waveName === 'Tutup Gelombang') {
                card.className = 'group relative flex flex-col justify-between p-3.5 sm:p-4 rounded-2xl text-left bg-white border-2 border-rose-500 border-b-4 border-b-rose-600 shadow-sm ring-4 ring-rose-500/10 cursor-pointer transition-all duration-150 active:translate-y-[2px] active:border-b-2 outline-none focus:outline-none';
                if (pill) {
                  pill.className = 'wave-card-pill inline-flex items-center gap-1 text-[10px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-200';
                  pill.textContent = 'Aktif';
                }
                if (iconBox) iconBox.className = 'w-8 h-8 rounded-xl flex items-center justify-center bg-rose-50 text-rose-600 transition-colors';
                if (titleEl) titleEl.className = 'text-sm font-extrabold text-slate-900 group-hover:text-rose-600 transition-colors';
                if (dateRow) dateRow.className = 'wave-card-date mt-1 flex items-center gap-1.5 text-[11px] font-medium text-rose-600 transition-colors';
              } else {
                card.className = 'group relative flex flex-col justify-between p-3.5 sm:p-4 rounded-2xl text-left bg-white border-2 border-blue-600 border-b-4 border-b-blue-700 shadow-sm ring-4 ring-blue-500/10 cursor-pointer transition-all duration-150 active:translate-y-[2px] active:border-b-2 outline-none focus:outline-none';
                if (pill) {
                  pill.className = 'wave-card-pill inline-flex items-center gap-1 text-[10px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200';
                  pill.textContent = 'Aktif';
                }
                if (iconBox) iconBox.className = 'w-8 h-8 rounded-xl flex items-center justify-center bg-blue-50 text-blue-600 transition-colors';
                if (titleEl) titleEl.className = 'text-sm font-extrabold text-slate-900 group-hover:text-blue-600 transition-colors';
                if (dateRow) dateRow.className = 'wave-card-date mt-1 flex items-center gap-1.5 text-[11px] font-medium text-blue-600 transition-colors';
              }
            } else {
              card.className = cardWave === 'Tutup Gelombang'
                ? 'group relative flex flex-col justify-between p-3.5 sm:p-4 rounded-2xl text-left bg-white border-2 border-slate-200/90 border-b-4 border-b-slate-300 hover:border-rose-400 hover:border-b-rose-500 hover:shadow-sm cursor-pointer transition-all duration-150 active:translate-y-[2px] active:border-b-2 outline-none focus:outline-none'
                : 'group relative flex flex-col justify-between p-3.5 sm:p-4 rounded-2xl text-left bg-white border-2 border-slate-200/90 border-b-4 border-b-slate-300 hover:border-blue-400 hover:border-b-blue-500 hover:shadow-sm cursor-pointer transition-all duration-150 active:translate-y-[2px] active:border-b-2 outline-none focus:outline-none';
              if (pill) {
                pill.className = cardWave === 'Tutup Gelombang'
                  ? 'wave-card-pill inline-flex items-center gap-1 text-[10px] font-medium text-slate-400 bg-slate-50 px-2 py-0.5 rounded-full border border-slate-100 group-hover:text-rose-600 group-hover:border-rose-200'
                  : 'wave-card-pill inline-flex items-center gap-1 text-[10px] font-medium text-slate-400 bg-slate-50 px-2 py-0.5 rounded-full border border-slate-100 group-hover:text-slate-600';
                pill.textContent = 'Pilih';
              }
              if (iconBox) {
                iconBox.className = cardWave === 'Tutup Gelombang'
                  ? 'w-8 h-8 rounded-xl flex items-center justify-center bg-slate-50 text-slate-500 group-hover:text-rose-600 group-hover:bg-rose-50 transition-colors'
                  : 'w-8 h-8 rounded-xl flex items-center justify-center bg-slate-50 text-slate-500 group-hover:text-blue-600 group-hover:bg-blue-50 transition-colors';
              }
              if (titleEl) titleEl.className = cardWave === 'Tutup Gelombang'
                ? 'text-sm font-extrabold text-slate-900 group-hover:text-rose-600 transition-colors'
                : 'text-sm font-extrabold text-slate-900 group-hover:text-blue-600 transition-colors';
              if (dateRow) dateRow.className = `wave-card-date mt-1 flex items-center gap-1.5 text-[11px] font-medium text-slate-400 ${cardWave === 'Tutup Gelombang' ? 'group-hover:text-rose-600' : 'group-hover:text-blue-600'} transition-colors`;
            }
          });
        } catch (err) {
          console.error('Gagal menyimpan gelombang:', err);
          select.value = previousWave;
          showToast('Perubahan gelombang gagal disimpan. Silakan coba kembali.', 'error');
        } finally {
          select.disabled = false;
          select.removeAttribute('aria-busy');
          miniCards.forEach(c => c.classList.remove('pointer-events-none', 'opacity-60'));
        }
      }

      function startEditStudentData(section) {
        if (!currentApplicant) return;
        const item = currentApplicant;
        const student = item.calon_siswa || {};
        const editStatus = document.getElementById('drawer-edit-status');

        editStatus.textContent = '';
        document.getElementById('edit-student-nama').value = student.nama_lengkap || '';
        document.getElementById('edit-student-nisn').value = student.nisn || student.user?.email?.split('@')[0] || '';
        document.getElementById('edit-student-wa').value = student.no_whatsapp || '';
        document.getElementById('edit-student-tempat-lahir').value = student.tempat_lahir || '';
        document.getElementById('edit-student-tanggal-lahir').value = (student.tanggal_lahir || '').slice(0, 10);
        document.getElementById('edit-student-jk').value = student.jenis_kelamin || 'Laki-laki';
        document.getElementById('edit-student-agama').value = student.agama || 'Hindu';
        document.getElementById('edit-student-sekolah').value = student.asal_sekolah || '';
        document.getElementById('edit-student-alamat').value = student.alamat_domisili || '';
        document.getElementById('edit-student-nama-bapak').value = student.nama_bapak || '';
        document.getElementById('edit-student-nama-ibu').value = student.nama_ibu || '';
        document.getElementById('edit-student-pekerjaan-bapak').value = student.pekerjaan_bapak || '';
        document.getElementById('edit-student-pekerjaan-ibu').value = student.pekerjaan_ibu || '';
        document.getElementById('edit-student-no-wa-ayah').value = student.no_wa_ayah || '';
        document.getElementById('edit-student-no-wa-ibu').value = student.no_wa_ibu || '';
        document.getElementById('edit-student-alamat-ortu').value = student.alamat_ortu || '';
        document.getElementById('edit-student-major').value = String(item.id_jurusan || '');
        document.getElementById('edit-student-jalur').value = String(item.id_jalur || '');
        document.getElementById('edit-student-gelombang').value = String(item.id_gelombang || '');
        document.getElementById('edit-student-sub-jalur').value = item.sub_jalur || '';
        document.getElementById('edit-student-nama-sub-jalur').value = item.nama_sub_jalur || '';
        document.getElementById('edit-student-voucher-jenis').value = item.voucher_jenis || '';
        document.getElementById('edit-student-voucher-kode').value = item.voucher_kode || '';
        document.getElementById('edit-student-status').value = item.status_berkas || 'Menunggu Verifikasi';
        document.getElementById('edit-student-tgl-daftar').value = (item.tgl_daftar || '').slice(0, 10);
        document.getElementById('edit-student-notes').value = item.verifikasi?.catatan_verifikator || '';

        document.getElementById('drawer-view-mode').classList.add('hidden');
        document.getElementById('drawer-view-footer').classList.add('hidden');
        document.getElementById('drawer-edit-mode').classList.remove('hidden');
        const editFooter = document.getElementById('drawer-edit-footer');
        if (editFooter) {
          editFooter.classList.remove('hidden');
          editFooter.classList.add('flex');
        }

        if (section) {
          const targetSection = document.getElementById(`edit-section-${section}`);
          if (targetSection) {
            targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            const firstInput = targetSection.querySelector('input, select, textarea');
            if (firstInput) {
              setTimeout(() => firstInput.focus(), 100);
            }
          }
        }
      }

      function cancelEditStudentData() {
        document.getElementById('drawer-view-mode')?.classList.remove('hidden');
        document.getElementById('drawer-view-footer')?.classList.remove('hidden');
        document.getElementById('drawer-edit-mode')?.classList.add('hidden');
        const editFooter = document.getElementById('drawer-edit-footer');
        if (editFooter) {
          editFooter.classList.add('hidden');
          editFooter.classList.remove('flex');
        }
      }

      function saveStudentDataChanges() {
        const form = document.getElementById('drawer-edit-mode');
        const status = document.getElementById('drawer-edit-status');
        const saveButton = document.getElementById('drawer-edit-save-button');
        if (saveButton.disabled || !currentNoRegistrasi || !form.reportValidity()) return;

        const values = {
          nama_lengkap: document.getElementById('edit-student-nama').value.trim(),
          nisn: document.getElementById('edit-student-nisn').value.trim(),
          no_whatsapp: document.getElementById('edit-student-wa').value.trim(),
          tempat_lahir: document.getElementById('edit-student-tempat-lahir').value.trim(),
          tanggal_lahir: document.getElementById('edit-student-tanggal-lahir').value,
          jenis_kelamin: document.getElementById('edit-student-jk').value,
          agama: document.getElementById('edit-student-agama').value,
          asal_sekolah: document.getElementById('edit-student-sekolah').value.trim(),
          alamat_domisili: document.getElementById('edit-student-alamat').value.trim(),
          nama_bapak: document.getElementById('edit-student-nama-bapak').value.trim() || null,
          pekerjaan_bapak: document.getElementById('edit-student-pekerjaan-bapak').value.trim() || null,
          nama_ibu: document.getElementById('edit-student-nama-ibu').value.trim() || null,
          pekerjaan_ibu: document.getElementById('edit-student-pekerjaan-ibu').value.trim() || null,
          no_wa_ayah: document.getElementById('edit-student-no-wa-ayah').value.trim() || null,
          no_wa_ibu: document.getElementById('edit-student-no-wa-ibu').value.trim() || null,
          alamat_ortu: document.getElementById('edit-student-alamat-ortu').value.trim() || null,
          id_jurusan: document.getElementById('edit-student-major').value,
          id_jalur: document.getElementById('edit-student-jalur').value,
          id_gelombang: document.getElementById('edit-student-gelombang').value,
          sub_jalur: document.getElementById('edit-student-sub-jalur').value.trim() || null,
          nama_sub_jalur: document.getElementById('edit-student-nama-sub-jalur').value.trim() || null,
          voucher_jenis: document.getElementById('edit-student-voucher-jenis').value || null,
          voucher_kode: document.getElementById('edit-student-voucher-kode').value.trim() || null,
          status_berkas: document.getElementById('edit-student-status').value,
          tgl_daftar: document.getElementById('edit-student-tgl-daftar').value || null,
          catatan_verifikator: document.getElementById('edit-student-notes').value.trim() || null,
        };

        saveButton.disabled = true;
        saveButton.classList.add('opacity-50', 'cursor-wait');
        status.className = 'text-xs text-slate-500 font-medium';
        status.textContent = 'Menyimpan perubahan ke database...';

        const endpoint = form.dataset.urlTemplate.replace(
          '__REGISTRATION__',
          encodeURIComponent(currentNoRegistrasi),
        );

        fetch(endpoint, {
          method: 'POST',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('#admin-verification-form input[name="_token"]').value,
          },
          body: JSON.stringify(values),
        })
          .then(async response => {
            const result = await response.json().catch(() => ({}));
            if (!response.ok) {
              const validationMessage = Object.values(result.errors || {}).flat()[0];
              throw new Error(validationMessage || result.message || 'Perubahan gagal disimpan.');
            }

            cancelEditStudentData();
            updateApplicantInMemory(result.applicant);
            setUndoStatus(
              document.getElementById('drawer-data-status'),
              result.undo_token,
            );
            showToast(`Data calon siswa ${result.applicant.calon_siswa?.nama_lengkap || currentNoRegistrasi} berhasil disimpan realtime.`, 'success');
          })
          .catch(error => {
            status.className = 'text-xs text-rose-600 font-medium';
            status.textContent = error.message || 'Perubahan gagal disimpan.';
            showToast(error.message || 'Perubahan gagal disimpan.', 'error');
          })
          .finally(() => {
            saveButton.disabled = false;
            saveButton.classList.remove('opacity-50', 'cursor-wait');
          });
      }

      function handleAdminLogout(event) {
        if (event) event.preventDefault();
        if (typeof window.openLogoutConfirmModal === 'function') {
          window.openLogoutConfirmModal(async () => {
            const btn = document.getElementById('admin-logout-btn');
            const icon = document.getElementById('admin-logout-icon');
            const spinner = document.getElementById('admin-logout-spinner');
            const text = document.getElementById('admin-logout-text');

            if (btn) btn.disabled = true;
            if (icon) icon.classList.add('hidden');
            if (spinner) spinner.classList.remove('hidden');
            if (text) text.textContent = 'Keluar...';

            try {
              const res = await fetch("{{ route('logout') }}", {
                method: 'POST',
                headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}',
                  'Accept': 'application/json',
                  'Content-Type': 'application/json'
                }
              });

              const data = await res.json().catch(() => ({}));
              const targetUrl = data.redirect || "{{ route('login') }}";
              window.location.replace(targetUrl);
            } catch (err) {
              console.error('Logout error:', err);
              const form = document.getElementById('admin-navbar-logout-form');
              if (form) {
                form.submit();
              } else {
                window.location.replace("{{ route('login') }}");
              }
            }
          });
          return;
        }

        const form = document.getElementById('admin-navbar-logout-form');
        if (form) form.submit();
      }

      // Interaktivitas Dropdown Menu Akun Admin (Hover, Klik, Tutup saat klik di luar)
      (function initAdminUserDropdown() {
        const menuContainer = document.getElementById('admin-user-menu-container');
        const userDropdown = document.getElementById('admin-user-dropdown-menu');
        const userBtn = document.getElementById('admin-nav-user-btn');
        const chevron = document.getElementById('admin-user-chevron-icon');
        let hideUserDropdownTimer = null;

        function showUserDropdown() {
          if (hideUserDropdownTimer) {
            clearTimeout(hideUserDropdownTimer);
            hideUserDropdownTimer = null;
          }
          if (userDropdown) {
            userDropdown.classList.remove('invisible', 'opacity-0', 'translate-y-1.5', 'scale-95', 'pointer-events-none');
            userDropdown.classList.add('visible', 'opacity-100', 'translate-y-0', 'scale-100', 'pointer-events-auto');
          }
          if (chevron) {
            chevron.classList.add('rotate-180');
          }
          if (userBtn) {
            userBtn.setAttribute('aria-expanded', 'true');
          }
        }

        function hideUserDropdown() {
          if (userDropdown) {
            userDropdown.classList.add('invisible', 'opacity-0', 'translate-y-1.5', 'scale-95', 'pointer-events-none');
            userDropdown.classList.remove('visible', 'opacity-100', 'translate-y-0', 'scale-100', 'pointer-events-auto');
          }
          if (chevron) {
            chevron.classList.remove('rotate-180');
          }
          if (userBtn) {
            userBtn.setAttribute('aria-expanded', 'false');
          }
        }

        function scheduleHideUserDropdown() {
          hideUserDropdownTimer = setTimeout(() => {
            hideUserDropdown();
          }, 180);
        }

        if (menuContainer) {
          menuContainer.addEventListener('mouseenter', showUserDropdown);
          menuContainer.addEventListener('mouseleave', scheduleHideUserDropdown);
        }

        if (userBtn) {
          userBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            if (userDropdown && userDropdown.classList.contains('visible')) {
              hideUserDropdown();
            } else {
              showUserDropdown();
            }
          });
        }

        document.addEventListener('click', function (e) {
          if (menuContainer && !menuContainer.contains(e.target)) {
            hideUserDropdown();
          }
        });
      })();

      // Realtime Jam & Tanggal Navbar
      (function initNavbarRealtimeClock() {
        const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const shortMonthNames = [
          'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
          'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
        ];

        function updateNavClock() {
          const now = new Date();
          const dayName = dayNames[now.getDay()];
          const dateNum = now.getDate();
          const monthName = shortMonthNames[now.getMonth()];
          const yearNum = now.getFullYear();

          const hours = String(now.getHours()).padStart(2, '0');
          const minutes = String(now.getMinutes()).padStart(2, '0');

          const tzOffset = -now.getTimezoneOffset() / 60;
          let tzName = 'WITA';
          if (tzOffset === 7) tzName = 'WIB';
          else if (tzOffset === 8) tzName = 'WITA';
          else if (tzOffset === 9) tzName = 'WIT';

          const elDate = document.getElementById('nav-realtime-date');
          const elTime = document.getElementById('nav-realtime-time');
          const elTz = document.getElementById('nav-realtime-tz');

          if (elDate) elDate.textContent = `${dayName}, ${dateNum} ${monthName} ${yearNum}`;
          if (elTime) elTime.textContent = `${hours}:${minutes}`;
          if (elTz) elTz.textContent = tzName;

          // Status Work (07.05 - 18.00) vs Break (No badge)
          const elText = document.getElementById('nav-work-status-text');

          if (elText) {
            const totalMinutes = now.getHours() * 60 + now.getMinutes();
            const isWorkHour = totalMinutes >= 425 && totalMinutes <= 1080; // 07:05 (425m) s/d 18:00 (1080m)

            if (isWorkHour) {
              elText.textContent = 'Work';
              elText.title = 'Work (07.05 - 18.00 ' + tzName + ')';
              elText.className = 'font-sans font-semibold text-[11px] sm:text-xs text-emerald-600 status-work';
            } else {
              elText.textContent = 'Break';
              elText.title = 'Break (Di luar jam kerja 07.05 - 18.00 ' + tzName + ')';
              elText.className = 'font-sans font-semibold text-[11px] sm:text-xs text-amber-600 status-break';
            }
          }
        }

        updateNavClock();
        setInterval(updateNavClock, 1000);
      })();
    </script>
@endpush
