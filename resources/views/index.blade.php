@extends('layouts.app')

@section('title', 'SPMB 2026/2027 - SMK TI BALI GLOBAL BADUNG')
@section('html_class', 'h-full bg-slate-900 scroll-smooth')
@section('body_class', 'min-h-screen text-slate-100 antialiased selection:bg-blue-600 selection:text-white')

@section('body_attributes')
data-active-wave="{{ $activeWave ?? 'Gelombang Khusus' }}"
@endsection

@push('styles')
<style>
      body {
        font-family:
          "Plus Jakarta Sans",
          "DM Sans",
          system-ui,
          -apple-system,
          sans-serif;
      }

      .hero-title {
        font-family: "Bricolage Grotesque", sans-serif;
        font-weight: 800;
        letter-spacing: -0.04em;
      }

      .hero-title span {
        line-height: 0.94;
      }

      /* Minimalist Custom Scrollbar */
      html {
        scrollbar-width: thin;
        scrollbar-color: #475569 transparent;
      }
      ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
      }
      ::-webkit-scrollbar-track {
        background: transparent;
      }
      ::-webkit-scrollbar-thumb {
        background: #475569;
        border-radius: 9999px;
      }
      ::-webkit-scrollbar-thumb:hover {
        background: #00a8e8;
      }

      /* Date input UX: pointer cursor on calendar indicator */
      input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
      }

      /* Print Styles: Isolates Kartu Identitas Pendaftaran */
      @media print {
        body * {
          visibility: hidden !important;
        }
        #printable-id-card,
        #printable-id-card * {
          visibility: visible !important;
        }
        #printable-id-card {
          position: absolute;
          left: 0;
          top: 0;
          width: 100%;
          margin: 0;
          padding: 10mm;
          display: block !important;
          background: #ffffff !important;
        }
        .no-print {
          display: none !important;
        }
        @page {
          size: A4 portrait;
          margin: 10mm;
        }
      }

      /* Single Toast Shake Effect */
      @keyframes toastShake {
        0%,
        100% {
          transform: translateX(0);
        }
        15%,
        45%,
        75% {
          transform: translateX(-6px);
        }
        30%,
        60%,
        90% {
          transform: translateX(6px);
        }
      }
      .animate-toast-shake {
        animation: toastShake 0.4s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
      }

      /* Enhanced High-Contrast Technological Hero Background */
      .hero-bg {
        background:
          linear-gradient(
            180deg,
            rgba(11, 15, 25, 0.82) 0%,
            rgba(15, 23, 42, 0.78) 45%,
            rgba(11, 15, 25, 0.88) 100%
          ),
          url("{{ asset('assets/img/hero-bg.jpg') }}") center/cover no-repeat;
      }

      /* Navbar State Classes (Y = 0 transparent, Scrolled = Translucent Frosted Glass) */
      /* Navbar State Classes (Y = 0 Transparan Penuh Tanpa Background, Scrolled Opacity Penuh) */
      #main-navbar {
        transition: background-color 0.3s ease, backdrop-filter 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
      }
      #main-navbar.nav-at-top {
        background: transparent !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        border-bottom: 1px solid transparent !important;
        box-shadow: none !important;
      }
      #main-navbar.nav-scrolled,
      #main-navbar.nav-scrolled-hero {
        background: rgba(11, 15, 25, 0.85) !important;
        backdrop-filter: blur(16px) saturate(180%) !important;
        -webkit-backdrop-filter: blur(16px) saturate(180%) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.12) !important;
        box-shadow: 0 4px 24px -4px rgba(0, 0, 0, 0.35) !important;
      }

      /* Tombol Akun Konsisten di Y = 0 maupun Y != 0 */
      #nav-user-btn-auth {
        background-color: rgba(11, 15, 25, 0.70) !important;
        border: 1px solid rgba(255, 255, 255, 0.20) !important;
        backdrop-filter: blur(16px) !important;
        -webkit-backdrop-filter: blur(16px) !important;
      }
      #nav-user-btn-auth:hover {
        background-color: rgba(11, 15, 25, 0.85) !important;
        border-color: rgba(255, 255, 255, 0.35) !important;
      }
    </style>
@endpush

@section('content')
@php
  $registrationClosed = ($activeWave ?? 'Gelombang Khusus') === 'Tutup Gelombang';
@endphp

<!-- ============================================================== -->
    <!-- PORTAL GATE / BERANDA 100VH (FULL VIEWPORT GATEWAY)            -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- 1. STICKY NAVBAR: Clean, Translucent & Always Accessible       -->
    <!-- ============================================================== -->
    <header
      id="main-navbar"
      class="fixed top-0 left-0 right-0 z-40 w-full nav-at-top transition-all duration-300 no-print"
    >
      <nav
        class="w-full py-3 sm:py-3.5 px-4 sm:px-8 xl:px-12 2xl:px-16 max-w-7xl 2xl:max-w-[1440px] mx-auto flex items-center justify-between"
      >
        <!-- Brand Logo & Name -->
        <a
          href="#hero-home-section"
          class="flex items-center gap-3 xl:gap-3.5 group"
        >
          <div
            class="w-10 h-10 xl:w-11 xl:h-11 flex items-center justify-center shrink-0 transition-transform group-hover:scale-105"
          >
            <img
              src="{{ asset('assets/img/alogo.png') }}"
              alt="Logo SMK TI Bali Global"
              class="w-full h-full object-contain filter drop-shadow-[0_2px_8px_rgba(0,0,0,0.55)]"
            />
          </div>
          <div class="flex flex-col leading-none">
            <span
              class="tracking-widest text-xs sm:text-sm xl:text-base font-extrabold text-[#00A8E8]"
              style="line-height: 1.1; text-shadow: 0 1px 6px rgba(0, 0, 0, 0.65);"
            >
              SMK TI
            </span>
            <span
              id="nav-brand-subtitle"
              class="text-[11px] sm:text-xs xl:text-sm font-bold text-slate-100 -mt-0.5 transition-colors"
              style="line-height: 1.15; text-shadow: 0 1px 6px rgba(0, 0, 0, 0.65);"
            >
              Bali Global Badung
            </span>
          </div>
        </a>

        <!-- Desktop Quick Navigation Links -->
        <div
          id="nav-desktop-links"
          class="hidden lg:flex items-center gap-7 text-xs xl:text-sm font-semibold text-slate-100 transition-colors"
        >
          <a href="#hero-home-section" class="hover:text-[#00A8E8] transition"
            >Beranda</a
          >
          <a href="#section-cara-daftar" class="hover:text-[#00A8E8] transition"
            >Cara Pendaftaran</a
          >
          <a href="#section-jalur" class="hover:text-[#00A8E8] transition"
            >Jalur Pendaftaran</a
          >
          <a href="#section-jadwal" class="hover:text-[#00A8E8] transition"
            >Jadwal SPMB</a
          >
          <a href="#section-faq" class="hover:text-[#00A8E8] transition"
            >FAQ</a
          >
          <a href="#section-footer" class="hover:text-[#00A8E8] transition"
            >Kontak</a
          >
        </div>

        <!-- Wrapper Dropdown (Wajib relative agar posisi menu presisi) -->
        <div class="relative group" id="user-menu-container">
          @auth
            <!-- Tampilan jika SUDAH LOGIN -->
            <button
              id="nav-user-btn-auth"
              type="button"
              class="flex items-center gap-2 sm:gap-2.5 pl-1.5 pr-3.5 py-1 rounded-full bg-slate-950/70 hover:bg-slate-900/85 border border-white/20 hover:border-white/35 backdrop-blur-xl hover:shadow-sm transition-all duration-200 cursor-pointer"
              aria-expanded="false"
              aria-haspopup="true"
            >
              <div class="w-7 h-7 xl:w-8 xl:h-8 rounded-full bg-gradient-to-tr from-blue-600 to-cyan-500 flex items-center justify-center text-white text-xs font-bold ring-1 ring-white/25 shrink-0">
                {{ strtoupper(substr(Auth::user()->calonSiswa->nama_lengkap ?? Auth::user()->email, 0, 1)) }}
              </div>
              <span class="text-xs font-semibold text-white/90 group-hover:text-white max-w-[120px] truncate tracking-wide">
                {{ Auth::user()->calonSiswa->nama_lengkap ?? (Auth::user()->isAdmin() ? 'Admin' : Auth::user()->email) }}
              </span>
              <svg
                id="chevron-icon"
                class="w-3.5 h-3.5 text-white/80 group-hover:text-white transition-all duration-200 group-hover:rotate-180 shrink-0"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2.5"
              >
                <use href="{{ asset('assets/img/icon/sprite.svg#chevron-account') }}" />
              </svg>
            </button>

            <!-- POPUP DROPDOWN MENU -->
            <div
              id="user-dropdown-menu"
              class="invisible opacity-0 translate-y-1.5 scale-95 pointer-events-none group-hover:visible group-hover:opacity-100 group-hover:translate-y-0 group-hover:scale-100 group-hover:pointer-events-auto transition-all duration-200 ease-out absolute right-0 top-full mt-2 w-72 rounded-2xl bg-white text-slate-800 shadow-xl shadow-slate-200/60 border border-slate-100 p-3.5 z-50 origin-top-right"
            >
              <!-- Invisible bridge to keep hover active between button and menu -->
              <div class="absolute -top-3 left-0 right-0 h-3" aria-hidden="true"></div>

              <!-- Profile Info Header -->
              <div class="flex items-center gap-3 px-1 py-1">
                <div class="w-10 h-10 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-sm shrink-0 ring-2 ring-blue-50">
                  {{ strtoupper(substr(Auth::user()->calonSiswa->nama_lengkap ?? Auth::user()->email, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                  <h4 class="font-bold text-sm text-slate-800 leading-tight truncate">
                    {{ Auth::user()->calonSiswa->nama_lengkap ?? (Auth::user()->isAdmin() ? 'Administrator' : 'Calon Siswa') }}
                  </h4>
                  <p class="text-xs text-slate-400 font-normal truncate mt-0.5" title="{{ Auth::user()->email }}">
                    {{ Auth::user()->email }}
                  </p>
                </div>
                <span class="shrink-0 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-600 border border-blue-100/80">
                  {{ Auth::user()->isAdmin() ? 'Admin' : 'Siswa' }}
                </span>
              </div>

              <!-- Divider -->
              <div class="my-2.5 border-t border-slate-100"></div>

              <!-- Menu Items -->
              <div class="space-y-1">
                @if(Auth::user()->isAdmin())
                  <a
                    href="{{ route('admin.dashboard') }}"
                    class="group/item flex items-center gap-3 px-2.5 py-2 rounded-xl text-xs sm:text-sm font-medium text-slate-700 hover:text-blue-600 hover:bg-blue-50/60 transition-colors"
                  >
                    <svg class="w-4 h-4 text-blue-600 shrink-0 transition-transform duration-200 group-hover/item:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dasbor Panitia</span>
                  </a>
                @else
                  @php
                    $studentRegistration = Auth::user()->calonSiswa?->latestPendaftaran;
                  @endphp
                  <a
                    href="{{ $studentRegistration ? route('pendaftaran.card') : route('pendaftaran.step1') }}"
                    class="group/item flex items-center gap-3 px-2.5 py-2 rounded-xl text-xs sm:text-sm font-medium text-slate-700 hover:text-blue-600 hover:bg-blue-50/60 transition-colors"
                  >
                    <svg class="w-4 h-4 text-blue-600 shrink-0 transition-transform duration-200 group-hover/item:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Formulir Pendaftaran</span>
                  </a>
                @endif

                <a
                  href="{{ Auth::user()->isAdmin() ? route('admin.password.edit') : route('pendaftaran.password.edit') }}"
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
                <form id="landing-user-logout-form" action="{{ route('logout') }}" method="POST">
                  @csrf
                  <button
                    type="button"
                    onclick="openLogoutConfirmModal('landing-user-logout-form')"
                    class="group/item w-full flex items-center gap-3 px-2.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-rose-500 hover:bg-rose-50/80 hover:text-rose-600 transition-colors cursor-pointer text-left"
                  >
                    <svg class="w-4 h-4 text-rose-500 shrink-0 transition-transform duration-200 group-hover/item:-translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Keluar</span>
                  </button>
                </form>
              </div>
            </div>
          @else
            <!-- Tampilan jika BELUM LOGIN -->
            <div class="flex items-center gap-2">
              <a
                href="{{ route('login') }}"
                class="px-3.5 py-2 rounded-full text-xs sm:text-sm font-semibold text-white/90 hover:text-white transition-colors duration-200 inline-flex items-center gap-2 cursor-pointer"
              >
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <use href="{{ asset('assets/img/icon/sprite.svg#user-login') }}" />
                </svg>
                <span>Login</span>
              </a>
              @if ($registrationClosed)
                <span
                  aria-disabled="true"
                  class="px-4 py-2 rounded-full text-xs sm:text-sm font-bold text-slate-300 bg-slate-700/80 border border-white/10 cursor-not-allowed"
                >
                  Pendaftaran Ditutup
                </span>
              @else
                <a
                  href="{{ route('register') }}"
                  class="px-4 py-2 rounded-full text-xs sm:text-sm font-bold text-white bg-blue-600 hover:bg-blue-500 transition-all shadow-md shadow-blue-600/25 flex items-center gap-1.5 cursor-pointer"
                >
                  <span>Daftar</span>
                </a>
              @endif
            </div>
          @endauth
        </div>
      </nav>
    </header>

    <!-- ============================================================== -->
    <!-- 2. SECTION HERO (Tetap Dipertahankan dari Desain Sebelumnya)   -->
    <!-- ============================================================== -->
    <section
      id="hero-home-section"
      class="min-h-screen flex flex-col justify-center hero-bg relative overflow-hidden py-12 sm:py-16 xl:py-20 no-print"
    >
      <main
        class="flex-1 flex flex-col items-center justify-center text-center px-4 sm:px-6 xl:px-8 2xl:px-12 w-full max-w-5xl xl:max-w-6xl 2xl:max-w-[1300px] mx-auto z-10 my-auto"
      >
        <!-- B. Judul Utama (Hero Title) & Subjudul Singkat Satu Baris -->
        <div
          class="space-y-1.5 sm:space-y-2 xl:space-y-2.5 2xl:space-y-3.5 max-w-3xl xl:max-w-4xl 2xl:max-w-5xl mx-auto"
        >
          <!-- Eyebrow (Tanpa Background) -->
          <p class="text-xs sm:text-sm font-semibold tracking-wider text-cyan-400 select-none text-center">
            Sistem Penerimaan Murid Baru
          </p>

          <h1
            class="hero-title mx-auto text-center text-3xl sm:text-5xl lg:text-6xl xl:text-7xl leading-[0.98] text-white"
          >
            <span class="block">SMK TI Bali Global</span>
            <span class="block">Badung</span>
          </h1>
          <p
            class="mt-3 text-sm sm:text-base lg:text-lg text-slate-200 drop-shadow max-w-2xl mx-auto text-justify"
            style="text-align: justify; text-justify: inter-word;"
          >
            Sekolah kejuruan IT terdepan yang mencetak talenta digital berdaya saing global melalui kurikulum industri terkini dan program Teaching Factory unggul di Bali.
          </p>
        </div>

        <!-- Tombol Aksi Utama (CTAs) -->
        <div
          class="mt-6 sm:mt-8 mb-3 flex flex-col sm:flex-row items-center justify-center gap-3 w-full max-w-xl mx-auto"
        >
          <!-- Tombol 1: Daftar Sekarang -->
          @if (($activeWave ?? 'Gelombang Khusus') === 'Tutup Gelombang')
            <button
              type="button"
              onclick="if (typeof window.showToast === 'function') { window.showToast('Pendaftaran Sistem Penerimaan Murid Baru saat ini sedang ditutup. Silakan pantau pengumuman gelombang berikutnya pada jadwal pendaftaran di bawah.', 'warning'); } else { alert('Pendaftaran Sistem Penerimaan Murid Baru saat ini sedang ditutup.'); }"
              id="hero-btn-daftar"
              class="w-full sm:w-auto min-w-44 px-6 sm:px-7 py-3 sm:py-3.5 bg-slate-700/80 hover:bg-slate-700 text-slate-300 text-sm sm:text-base font-bold rounded-xl border border-white/10 shadow-lg transition flex items-center justify-center gap-2 cursor-pointer"
            >
              <span>Pendaftaran Ditutup</span>
            </button>
          @else
            <a
              href="@auth {{ route('pendaftaran.step1') }} @else {{ route('register') }} @endauth"
              id="hero-btn-daftar"
              class="w-full sm:w-auto min-w-44 px-6 sm:px-7 py-3 sm:py-3.5 bg-blue-600 hover:bg-blue-500 text-white text-sm sm:text-base font-bold rounded-xl shadow-lg shadow-blue-600/25 transition flex items-center justify-center gap-2 group"
            >
              <span id="hero-btn-daftar-text">Daftar Sekarang</span>
            </a>
          @endif
          <a
            href="#section-cara-daftar"
            class="w-full sm:w-auto min-w-56 px-6 sm:px-7 py-3 sm:py-3.5 border border-white/35 hover:bg-white/10 text-white text-sm sm:text-base font-semibold rounded-xl transition flex items-center justify-center gap-2"
          >
            <span>Lihat Cara Pendaftaran</span>
          </a>
        </div>
      </main>
    </section>

    <!-- ============================================================== -->
    <!-- 3. SECTION CARA PENDAFTARAN (Grid 2x2)                         -->
    <!-- ============================================================== -->
    <section
      id="section-cara-daftar"
      class="pt-12 sm:pt-14 pb-10 sm:pb-12 px-4 sm:px-6 lg:px-8 bg-slate-50 text-slate-900 relative no-print"
    >
      <div class="max-w-4xl mx-auto">
        <!-- Section Header -->
        <div class="text-center max-w-2xl mx-auto mb-7 sm:mb-8">
          <h2
            class="text-3xl sm:text-4xl font-black tracking-tight text-slate-900"
          >
            Cara Pendaftaran
          </h2>
          <p class="mt-2 text-sm sm:text-base text-slate-600 font-medium">
            Pilih salah satu cara pendaftaran yang paling mudah untukmu
          </p>
        </div>

        <!-- 2x2 Grid Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Kartu 1: Sudah Daftar? -->
          <div
            class="h-full bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/90 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between"
          >
            <div>
              <div
                class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center mb-3.5"
              >
                <svg
                  class="w-[22px] h-[22px]"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  viewBox="0 0 24 24"
                >
                  <use href="{{ asset('assets/img/icon/sprite.svg#login-arrow') }}" />
                </svg>
              </div>
              <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 mb-1.5">
                Sudah Daftar?
              </h3>
              <p class="text-sm text-slate-600 leading-relaxed mb-4">
                Masuk ke akunmu untuk melengkapi berkas pendaftaran
              </p>
            </div>
            <div>
              <a
                href="{{ route('login') }}"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-md shadow-indigo-600/20 transition cursor-pointer"
              >
                <span>Login Akun</span>
                <svg
                  class="w-4 h-4"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2.2"
                  viewBox="0 0 24 24"
                >
                  <use href="{{ asset('assets/img/icon/sprite.svg#arrow-right') }}" />
                </svg>
              </a>
            </div>
          </div>

          <!-- Kartu 2: Daftar Online -->
          <div
            class="h-full bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/90 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between"
          >
            <div>
              <div
                class="w-11 h-11 rounded-xl bg-cyan-50 text-[#00A8E8] border border-cyan-100 flex items-center justify-center mb-3.5"
              >
                <svg
                  class="w-[22px] h-[22px]"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  viewBox="0 0 24 24"
                >
                  <use href="{{ asset('assets/img/icon/sprite.svg#globe') }}" />
                </svg>
              </div>
              <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 mb-1.5">
                Daftar Online
              </h3>
              <p class="text-sm text-slate-600 leading-relaxed mb-4">
                Daftar melalui website resmi SPMB SMK TI Bali Global Badung
              </p>
            </div>
            <div>
              @if ($registrationClosed)
                <span class="inline-flex items-center px-5 py-2.5 bg-slate-200 text-slate-500 text-xs sm:text-sm font-bold rounded-xl cursor-not-allowed" aria-disabled="true">
                  Pendaftaran Ditutup
                </span>
              @else
                <a
                  href="@auth {{ route('pendaftaran.step1') }} @else {{ route('register') }} @endauth"
                  class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#00A8E8] hover:bg-cyan-600 text-white text-xs sm:text-sm font-bold rounded-xl shadow-md shadow-cyan-500/25 transition"
                >
                  <span>Daftar Sekarang</span>
                  <svg
                    class="w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.2"
                    viewBox="0 0 24 24"
                  >
                    <use href="{{ asset('assets/img/icon/sprite.svg#arrow-right') }}" />
                  </svg>
                </a>
              @endif
            </div>
          </div>

          <!-- Kartu 3: Via WhatsApp -->
          <div
            class="h-full bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/90 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between"
          >
            <div>
              <div
                class="w-11 h-11 rounded-xl bg-[#25D366] text-white border border-emerald-500 flex items-center justify-center mb-3.5 shadow-sm shadow-emerald-600/20"
              >
                <svg class="w-[22px] h-[22px] shrink-0" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <use href="{{ asset('assets/img/icon/sprite.svg#whatsapp') }}" />
                </svg>
              </div>
              <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 mb-1.5">
                Via WhatsApp
              </h3>
              <p class="text-sm text-slate-600 leading-relaxed mb-4">
                Hubungi kami melalui WhatsApp untuk konsultasi dan pendaftaran
              </p>
            </div>
            <div>
              <a
                href="https://wa.me/6282261900070?"
                target="_blank"
                rel="noopener noreferrer"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-md shadow-emerald-600/20 transition tabular-nums"
              >
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <use href="{{ asset('assets/img/icon/sprite.svg#whatsapp') }}" />
                </svg>
                <span>082261900070</span>
              </a>
            </div>
          </div>

          <!-- Kartu 4: Daftar Offline -->
          <div
            class="h-full bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/90 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between"
          >
            <div>
              <div
                class="w-11 h-11 rounded-xl bg-orange-50 text-orange-500 border border-orange-100 flex items-center justify-center mb-3.5"
              >
                <svg
                  class="w-[22px] h-[22px]"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  viewBox="0 0 24 24"
                >
                  <use href="{{ asset('assets/img/icon/sprite.svg#building') }}" />
                </svg>
              </div>
              <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 mb-1.5">
                Daftar Offline
              </h3>
              <p class="text-sm text-slate-600 leading-relaxed mb-4">
                Kunjungi sekolah kami di Jl. Tibungsari No. 1, Dalung, Kuta
                Utara, Badung, Bali (Jam 07.05 - 18.00 WITA)
              </p>
            </div>
            <div>
              <a
                href="https://maps.google.com/?q=SMK+TI+Bali+Global+Badung+Jl.+Tibungsari+No.+1+Dalung+Kuta+Utara+Badung+Bali"
                target="_blank"
                rel="noopener noreferrer"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white text-xs sm:text-sm font-bold rounded-xl shadow-md shadow-orange-500/20 transition"
              >
                <svg
                  class="w-4 h-4"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2.2"
                  viewBox="0 0 24 24"
                >
                  <use href="{{ asset('assets/img/icon/sprite.svg#map-pin') }}" />
                </svg>
                <span>Lihat Lokasi</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ============================================================== -->
    <!-- 4. SECTION JALUR PENDAFTARAN (5 Kolom Berjejer Rapi)           -->
    <!-- ============================================================== -->
    <section
      id="section-jalur"
      class="relative z-10 -mt-6 pt-[5.5rem] sm:pt-[6.5rem] pb-16 sm:pb-20 px-4 sm:px-6 lg:px-8 bg-white text-slate-900 border-y border-slate-200/80 no-print"
      style="border-top-left-radius: 1.5rem; border-top-right-radius: 1.5rem;"
    >
      <div class="max-w-6xl mx-auto">
        <div class="text-center max-w-2xl mx-auto mb-10">
          <h2
            class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900"
          >
            Pilihan Jalur Pendaftaran
          </h2>
          <p class="mt-2 text-sm text-slate-600">
            Tersedia 5 jalur penerimaan siswa baru sesuai kategori dan
            kualifikasi calon peserta didik
          </p>
          @if ($registrationClosed)
            <p class="mt-3 text-sm font-semibold text-rose-600" role="status">
              Pendaftaran sedang ditutup.
            </p>
          @endif
        </div>

        <!-- Kontainer Lebar Memanjang Berisi 5 Kolom Jalur Berjejer Rapi -->
        <div
          class="bg-slate-50/90 border border-slate-200 rounded-2xl p-3 sm:p-4"
        >
          @php
            $jalurList = [
              [
                'slug' => 'prestasi',
                'name' => 'Prestasi',
                'desc' => '(Akademik & Non-Akademik)',
                'bg' => 'bg-amber-50',
                'text' => 'text-amber-500',
                'icon' => 'sparkles',
                'name_class' => 'text-xs sm:text-sm',
                'desc_class' => 'text-[11px]',
              ],
              [
                'slug' => 'umum',
                'name' => 'Umum',
                'desc' => '(Reguler)',
                'bg' => 'bg-blue-50',
                'text' => 'text-blue-600',
                'icon' => 'users',
                'name_class' => 'text-xs sm:text-sm',
                'desc_class' => 'text-[11px]',
              ],
              [
                'slug' => 'afirmasi',
                'name' => 'Afirmasi',
                'desc' => '(Kurang Mampu)',
                'bg' => 'bg-emerald-50',
                'text' => 'text-emerald-600',
                'icon' => 'heart',
                'name_class' => 'text-xs sm:text-sm',
                'desc_class' => 'text-[11px]',
              ],
              [
                'slug' => 'kip',
                'name' => 'KIP',
                'desc' => '(Kartu Indonesia Pintar)',
                'bg' => 'bg-purple-50',
                'text' => 'text-purple-600',
                'icon' => 'credit-card',
                'name_class' => 'text-xs sm:text-sm',
                'desc_class' => 'text-[11px]',
              ],
              [
                'slug' => 'khusus',
                'name' => 'Khusus',
                'desc' => '(Anak Guru/Tendik / Mitra)',
                'bg' => 'bg-cyan-50',
                'text' => 'text-[#00A8E8]',
                'icon' => 'shield-check',
                'name_class' => 'text-sm',
                'desc_class' => 'text-xs',
              ],
            ];
          @endphp
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            @foreach ($jalurList as $jalur)
              <a
                @unless ($registrationClosed) href="@auth {{ route('pendaftaran.step2') }}?jalur={{ $jalur['slug'] }} @else {{ route('register') }}?jalur={{ $jalur['slug'] }} @endauth" @else aria-disabled="true" tabindex="-1" @endunless
                class="group bg-white hover:bg-blue-600 border border-slate-200/90 hover:border-blue-600 rounded-xl p-3.5 sm:p-4 flex flex-col items-center text-center transition-all duration-300 hover:shadow-lg hover:-translate-y-1 {{ $registrationClosed ? 'pointer-events-none opacity-60 cursor-not-allowed' : '' }}"
              >
                <div
                  class="w-10 h-10 rounded-xl {{ $jalur['bg'] }} {{ $jalur['text'] }} group-hover:bg-white/20 group-hover:text-white flex items-center justify-center mb-2.5 transition-colors"
                >
                  <svg
                    class="w-5 h-5"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                  >
                    <use href="{{ asset('assets/img/icon/sprite.svg#' . $jalur['icon']) }}" />
                  </svg>
                </div>
                <h3
                  class="{{ $jalur['name_class'] }} font-extrabold text-slate-900 group-hover:text-white transition-colors"
                >
                  {{ $jalur['name'] }}
                </h3>
                <p
                  class="{{ $jalur['desc_class'] }} text-slate-500 group-hover:text-blue-100 mt-1 transition-colors"
                >
                  {{ $jalur['desc'] }}
                </p>
              </a>
            @endforeach
          </div>
        </div>
      </div>
    </section>

    <!-- ============================================================== -->
    <!-- 5. SECTION JADWAL PENDAFTARAN (Vertical Timeline 1 - 4)        -->
    <!-- ============================================================== -->
    <section
      id="section-jadwal"
      class="relative z-20 -mt-6 pt-[6.5rem] sm:pt-[7.5rem] pb-20 sm:pb-24 px-4 sm:px-6 lg:px-8 bg-slate-50 text-slate-900 rounded-t-3xl no-print"
    >
      <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center max-w-2xl mx-auto mb-14">
          <h2
            class="text-3xl sm:text-4xl font-black tracking-tight text-slate-900"
          >
            Jadwal Pendaftaran
          </h2>
          <p class="mt-3 text-sm sm:text-base text-slate-600 font-medium">
            Jangan sampai ketinggalan! Daftarkan dirimu pada gelombang yang
            tersedia
          </p>
        </div>

        <!-- Vertical Timeline Container -->
        <div class="relative pl-6 sm:pl-10">
          <!-- Garis Penghubung Vertikal di Sebelah Kiri -->
          <div
            class="absolute left-5 sm:left-8 top-4 bottom-4 w-0.5 bg-gradient-to-b from-blue-600 via-slate-300 to-slate-200"
          ></div>

          @php
            $currentWaveName = $activeWave ?? 'Gelombang Khusus';
            $waveRankMap = [
                'Gelombang Khusus' => 1,
                'Gelombang 1' => 2,
                'Gelombang 2' => 3,
                'Tutup Gelombang' => 99,
            ];
            $currentRank = $waveRankMap[$currentWaveName] ?? 1;

            $timelineCards = [
                [
                    'num' => 1,
                    'name' => 'Gelombang Khusus',
                    'date' => '07 September 2026 – 01 Januari 2027',
                    'desc' => 'Pendaftaran dibuka khusus gelombang khusus potongan DPP 50%',
                ],
                [
                    'num' => 2,
                    'name' => 'Gelombang 1',
                    'date' => '02 Januari 2027 – 11 Mei 2027',
                    'desc' => 'Pendaftaran reguler gelombang pertama',
                ],
                [
                    'num' => 3,
                    'name' => 'Gelombang 2',
                    'date' => '12 Mei 2027 – 19 Juli 2027',
                    'desc' => 'Pendaftaran reguler gelombang kedua',
                ],
            ];
          @endphp

          <div class="space-y-8">
            @foreach ($timelineCards as $card)
              @php
                $isCurrent = ($card['num'] === $currentRank);
                $isPast = ($card['num'] < $currentRank);
                $isUpcoming = ($card['num'] > $currentRank);
              @endphp

              <div class="relative pl-6 sm:pl-8">
                <!-- Nomor Lingkaran -->
                @if ($isCurrent)
                  <div
                    class="absolute -left-6 sm:-left-7 top-0 w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-blue-600 text-white font-black text-sm sm:text-base flex items-center justify-center ring-4 ring-blue-100 shadow-md shadow-blue-600/30 z-10"
                  >
                    {{ $card['num'] }}
                  </div>
                @elseif ($isPast)
                  <div
                    class="absolute -left-6 sm:-left-7 top-0 w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-slate-200 text-slate-600 font-black text-sm sm:text-base flex items-center justify-center border-2 border-slate-300 z-10"
                  >
                    {{ $card['num'] }}
                  </div>
                @else
                  <div
                    class="absolute -left-6 sm:-left-7 top-0 w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-white text-slate-700 font-black text-sm sm:text-base flex items-center justify-center border-2 border-slate-300 z-10"
                  >
                    {{ $card['num'] }}
                  </div>
                @endif

                <!-- Kartu Langkah -->
                <div
                  class="bg-white rounded-3xl p-6 sm:p-7 transition-all {{ $isCurrent ? 'border-2 border-blue-500 shadow-lg shadow-blue-500/10' : ($isPast ? 'border border-slate-200/80 opacity-85' : 'border border-slate-200/90 hover:border-slate-300') }}"
                >
                  <div
                    class="flex flex-wrap items-center justify-between gap-2 mb-2.5"
                  >
                    <!-- Status Badge -->
                    @if ($isCurrent)
                      <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"
                      >
                        <span
                          class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"
                        ></span>
                        <span>Sedang Berlangsung</span>
                      </span>
                    @elseif ($isPast)
                      <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200"
                      >
                        Selesai
                      </span>
                    @else
                      <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200"
                      >
                        Mendatang
                      </span>
                    @endif

                    <!-- Tanggal -->
                    <span
                      class="text-xs sm:text-sm {{ $isCurrent ? 'font-bold text-blue-600' : ($isPast ? 'font-medium text-slate-400' : 'font-semibold text-slate-500') }} flex items-center gap-1.5"
                    >
                      <svg
                        class="w-4 h-4 shrink-0 {{ $isPast ? 'text-slate-400' : '' }}"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                      >
                        <use href="{{ asset('assets/img/icon/sprite.svg#calendar') }}" />
                      </svg>
                      <span>{{ $card['date'] }}</span>
                    </span>
                  </div>

                  <h3
                    class="{{ $isCurrent ? 'text-lg sm:text-xl font-extrabold text-slate-900 mb-1.5' : 'text-lg font-extrabold ' . ($isPast ? 'text-slate-700' : 'text-slate-900') . ' mb-1' }}"
                  >
                    {{ $card['name'] }}
                  </h3>
                  <p
                    class="text-sm {{ $isPast ? 'text-slate-500' : 'text-slate-600' }} leading-relaxed"
                  >
                    {{ $card['desc'] }}
                  </p>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </section>

    <!-- ============================================================== -->
    <!-- 6. SECTION CTA BANNER (Gradient Biru ke Ungu Terang)           -->
    <!-- ============================================================== -->
    <section
      id="section-cta"
      class="relative z-30 -mt-6 pt-[5.5rem] sm:pt-[6.5rem] pb-16 sm:pb-20 px-4 sm:px-6 lg:px-8 bg-white rounded-t-3xl no-print"
    >
      <div class="max-w-6xl mx-auto">
        <div
          class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 px-6 py-14 sm:px-12 sm:py-16 text-center text-white shadow-2xl shadow-indigo-600/25"
        >
          <!-- Decorative Glow Orbs -->
          <div
            class="pointer-events-none absolute -top-24 -left-24 w-72 h-72 rounded-full bg-white/10 blur-3xl"
          ></div> 
          <div
            class="pointer-events-none absolute -bottom-24 -right-24 w-72 h-72 rounded-full bg-cyan-400/20 blur-3xl"
          ></div>

          <div class="relative z-10 max-w-3xl mx-auto space-y-4">
            <h2
              class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight"
            >
              Siap Bergabung dengan Kami?
            </h2>
            <p
              class="text-sm sm:text-base lg:text-lg text-blue-100 font-medium max-w-2xl mx-auto"
            >
              Raih masa depan gemilang di bidang teknologi bersama SMK TI Bali
              Global Badung
            </p>

            <div
              class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-3.5 sm:gap-4"
            >
              <!-- Tombol 1: Daftar Sekarang (Solid Putih) -->
              @if ($registrationClosed)
                <span class="w-full sm:w-auto px-8 py-3.5 bg-white/70 text-blue-900/60 text-sm sm:text-base font-extrabold rounded-2xl cursor-not-allowed" aria-disabled="true">
                  Pendaftaran Ditutup
                </span>
              @else
                <a
                  href="@auth {{ route('pendaftaran.step1') }} @else {{ route('register') }} @endauth"
                  class="w-full sm:w-auto px-8 py-3.5 bg-white hover:bg-blue-50 text-blue-700 text-sm sm:text-base font-extrabold rounded-2xl shadow-lg transition flex items-center justify-center gap-2"
                >
                  <span>Daftar Sekarang</span>
                  <svg
                    class="w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                    viewBox="0 0 24 24"
                  >
                    <use href="{{ asset('assets/img/icon/sprite.svg#arrow-right') }}" />
                  </svg>
                </a>
              @endif

              <!-- Tombol 2: Hubungi Kami (Transparan / Outline Putih) -->
              <a
                href="https://wa.me/6282261900070?"
                target="_blank"
                rel="noopener noreferrer"
                class="w-full sm:w-auto px-8 py-3.5 bg-white/10 hover:bg-white/20 text-white border-2 border-white/80 text-sm sm:text-base font-bold rounded-2xl transition flex items-center justify-center gap-2"
              >
                <span>Hubungi Kami</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ============================================================== -->
    <!-- 7. SECTION FAQ (Sebelum Kontak)                                  -->
    <!-- ============================================================== -->
    <section
      id="section-faq"
      class="relative z-10 -mt-6 pt-[5.5rem] sm:pt-[6.5rem] pb-16 sm:pb-20 px-4 sm:px-6 lg:px-8 bg-slate-50 text-slate-900 border-t border-slate-200/80 no-print"
    >
      <div class="max-w-5xl mx-auto">
        <div class="text-center max-w-2xl mx-auto mb-10">
          <p class="text-sm font-bold uppercase tracking-[0.2em] text-[#00A8E8]">
            FAQ
          </p>
          <h2 class="mt-3 text-3xl sm:text-4xl font-black tracking-tight text-slate-900">
            Pertanyaan yang Sering Diajukan
          </h2>
          <p class="mt-3 text-sm sm:text-base text-slate-600">
            Informasi penting sebelum Anda melengkapi pendaftaran siswa baru.
          </p>
        </div>

        <div class="space-y-4">
          <details class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm open:shadow-md transition-all" open>
            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-left text-base font-bold text-slate-900">
              <span>Bagaimana cara mendaftar di SMK TI Bali Global Badung?</span>
              <svg class="h-5 w-5 shrink-0 text-slate-500 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <use href="{{ asset('assets/img/icon/sprite.svg#faq-chevron') }}" />
              </svg>
            </summary>
            <p class="mt-4 text-sm leading-relaxed text-slate-600">
              Calon siswa dapat mendaftar melalui website resmi SPMB, lalu mengisi formulir pendaftaran, melengkapi berkas persyaratan, dan mengikuti proses verifikasi sesuai jadwal yang berlaku.
            </p>
          </details>

          <details class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm open:shadow-md transition-all">
            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-left text-base font-bold text-slate-900">
              <span>Apakah pendaftaran bisa dilakukan melalui WhatsApp?</span>
              <svg class="h-5 w-5 shrink-0 text-slate-500 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <use href="{{ asset('assets/img/icon/sprite.svg#faq-chevron') }}" />
              </svg>
            </summary>
            <p class="mt-4 text-sm leading-relaxed text-slate-600">
              Ya. Anda bisa berkonsultasi atau menanyakan informasi pendaftaran melalui WhatsApp resmi kami, lalu melanjutkan proses pendaftaran secara online di website.
            </p>
          </details>

          <details class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm open:shadow-md transition-all">
            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-left text-base font-bold text-slate-900">
              <span>Kapan jadwal pendaftaran dibuka?</span>
              <svg class="h-5 w-5 shrink-0 text-slate-500 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <use href="{{ asset('assets/img/icon/sprite.svg#faq-chevron') }}" />
              </svg>
            </summary>
            <p class="mt-4 text-sm leading-relaxed text-slate-600">
              Jadwal pendaftaran dapat berubah sesuai gelombang yang sedang dibuka. Informasi terbaru selalu tersedia di bagian jadwal SPMB dan status gelombang di halaman ini.
            </p>
          </details>

          <details class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm open:shadow-md transition-all">
            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-left text-base font-bold text-slate-900">
              <span>Dokumen apa saja yang diperlukan?</span>
              <svg class="h-5 w-5 shrink-0 text-slate-500 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <use href="{{ asset('assets/img/icon/sprite.svg#faq-chevron') }}" />
              </svg>
            </summary>
            <p class="mt-4 text-sm leading-relaxed text-slate-600">
              Biasanya diperlukan data diri, dokumen utama seperti foto, ijazah/rapor, dan berkas pendukung lainnya sesuai jalur pendaftaran yang dipilih. Semua persyaratan akan dijelaskan saat pengisian formulir.
            </p>
          </details>
        </div>
      </div>
    </section>

    <!-- ============================================================== -->
    <!-- 8. FOOTER UTAMA (Profil · Navigasi · Program Studi · Kontak)      -->
    <!-- ============================================================== -->
    <footer
      id="section-footer"
      class="relative overflow-hidden bg-slate-50 text-slate-600 border-t border-slate-200/80 pt-16 sm:pt-20 pb-8 px-4 sm:px-6 lg:px-8 no-print"
    >
      <div class="relative max-w-7xl mx-auto">
        <div
          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-10 lg:gap-8 pb-12"
        >
          <!-- Kolom 1: Profil Sekolah & Media Sosial -->
          <div class="lg:col-span-4 space-y-5">
            <a
              href="#hero-home-section"
              class="inline-flex items-center gap-3 group"
            >
              <img
                src="assets/img/alogo.png"
                alt="Logo SMK TI Bali Global Badung"
                class="h-12 w-12 object-contain shrink-0 transition-transform group-hover:scale-105"
              />
              <span
                class="text-base sm:text-lg font-extrabold text-slate-900 leading-tight"
              >
                SMK TI Bali Global Badung
              </span>
            </a>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
              Sekolah kejuruan berbasis teknologi informasi yang mencetak
              lulusan kompeten, berkarakter, dan siap bersaing di era digital.
            </p>
            <!-- Ikon Media Sosial: Facebook, Instagram, YouTube, Twitter -->
            <div class="flex items-center gap-2.5 pt-1">
              @php
                $socials = [
                  ['label' => 'Facebook', 'url' => 'https://facebook.com', 'icon' => 'facebook'],
                  ['label' => 'Instagram', 'url' => 'https://instagram.com', 'icon' => 'instagram'],
                  ['label' => 'YouTube', 'url' => 'https://youtube.com', 'icon' => 'youtube'],
                  ['label' => 'Twitter', 'url' => 'https://twitter.com', 'icon' => 'twitter'],
                ];
              @endphp
              @foreach ($socials as $soc)
                <a
                  href="{{ $soc['url'] }}"
                  target="_blank"
                  rel="noopener noreferrer"
                  aria-label="{{ $soc['label'] }}"
                  class="w-9 h-9 rounded-xl bg-white hover:bg-[#00A8E8] text-slate-500 hover:text-white border border-slate-200 hover:shadow-md flex items-center justify-center transition"
                >
                  <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                    <use href="{{ asset('assets/img/icon/sprite.svg#' . $soc['icon']) }}" />
                  </svg>
                </a>
              @endforeach
            </div>
          </div>
          <!-- Kolom 2: Navigasi -->
          <div class="lg:col-span-2">
            <h4 class="text-base font-bold text-slate-900 mb-4 sm:mb-5">
              Navigasi
            </h4>
            <ul class="space-y-3 text-xs sm:text-sm text-slate-600">
              <li>
                <a
                  href="#hero-home-section"
                  class="hover:text-[#00A8E8] transition"
                  >Beranda</a
                >
              </li>
              <li>
                <a
                  href="#section-cara-daftar"
                  class="hover:text-[#00A8E8] transition"
                  >Cara Pendaftaran</a
                >
              </li>
              <li>
                <a
                  href="#section-jalur"
                  class="hover:text-[#00A8E8] transition"
                  >Jalur Pendaftaran</a
                >
              </li>
              <li>
                <a
                  href="#section-jadwal"
                  class="hover:text-[#00A8E8] transition"
                  >Jadwal SPMB</a
                >
              </li>
              <li>
                <a
                  href="#section-faq"
                  class="hover:text-[#00A8E8] transition"
                  >FAQ</a
                >
              </li>
              <li>
                <a
                  href="#section-footer"
                  class="hover:text-[#00A8E8] transition"
                  >Kontak</a
                >
              </li>
            </ul>
          </div>

          <!-- Kolom 3: Program Studi -->
          <div class="lg:col-span-2">
            <h4 class="text-base font-bold text-slate-900 mb-4 sm:mb-5">
              Program Studi
            </h4>
            <ul class="space-y-3 text-xs sm:text-sm text-slate-600">
              <li>
                <a
                  @unless ($registrationClosed) href="@auth {{ route('pendaftaran.step1') }} @else {{ route('register') }} @endauth" @else aria-disabled="true" tabindex="-1" @endunless
                  class="hover:text-[#00A8E8] transition {{ $registrationClosed ? 'pointer-events-none text-slate-400' : '' }}"
                  >TJKT</a
                >
              </li>
              <li>
                <a
                  @unless ($registrationClosed) href="@auth {{ route('pendaftaran.step1') }} @else {{ route('register') }} @endauth" @else aria-disabled="true" tabindex="-1" @endunless
                  class="hover:text-[#00A8E8] transition {{ $registrationClosed ? 'pointer-events-none text-slate-400' : '' }}"
                  >PPLG</a
                >
              </li>
              <li>
                <a
                  @unless ($registrationClosed) href="@auth {{ route('pendaftaran.step1') }} @else {{ route('register') }} @endauth" @else aria-disabled="true" tabindex="-1" @endunless
                  class="hover:text-[#00A8E8] transition {{ $registrationClosed ? 'pointer-events-none text-slate-400' : '' }}"
                  >DKV</a
                >
              </li>
              <li>
                <a
                  @unless ($registrationClosed) href="@auth {{ route('pendaftaran.step1') }} @else {{ route('register') }} @endauth" @else aria-disabled="true" tabindex="-1" @endunless
                  class="hover:text-[#00A8E8] transition {{ $registrationClosed ? 'pointer-events-none text-slate-400' : '' }}"
                  >Bisnis Digital</a
                >
              </li>
              <li>
                <a
                  @unless ($registrationClosed) href="@auth {{ route('pendaftaran.step1') }} @else {{ route('register') }} @endauth" @else aria-disabled="true" tabindex="-1" @endunless
                  class="hover:text-[#00A8E8] transition {{ $registrationClosed ? 'pointer-events-none text-slate-400' : '' }}"
                  >TKC (Tata Kecantikan)</a
                >
              </li>
            </ul>
          </div>
          <!-- Kolom 4: Kontak -->
          <div class="lg:col-span-4">
            <h4 class="text-base font-bold text-slate-900 mb-4 sm:mb-5">
              Kontak
            </h4>
            <ul class="space-y-3 text-xs sm:text-sm text-slate-600">
              <li class="flex items-start gap-2.5">
                <svg
                  class="w-4 h-4 text-[#00A8E8] shrink-0 mt-0.5"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  viewBox="0 0 24 24"
                >
                  <use href="{{ asset('assets/img/icon/sprite.svg#map-pin') }}" />
                </svg>
                <a
                  href="https://maps.google.com/?q=SMK+TI+Bali+Global+Badung+Jl.+Tibungsari+No.+1+Dalung+Kuta+Utara+Badung+Bali"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="hover:text-[#00A8E8] transition leading-relaxed"
                >
                  Jl. Tibungsari No. 1, Desa Dalung, Kuta Utara, Badung, Bali
                </a>
              </li>
              <li class="flex items-center gap-2.5">
                <svg
                  class="w-4 h-4 text-[#00A8E8] shrink-0"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  viewBox="0 0 24 24"
                >
                  <use href="{{ asset('assets/img/icon/sprite.svg#phone') }}" />
                </svg>
                <a
                  href="tel:+62361419097"
                  class="hover:text-[#00A8E8] transition tabular-nums"
                  >+62 361-419097</a
                >
              </li>
              <li class="flex items-center gap-2.5">
                <svg
                  class="w-4 h-4 text-[#00A8E8] shrink-0 fill-current"
                  viewBox="0 0 24 24"
                >
                  <use href="{{ asset('assets/img/icon/sprite.svg#whatsapp-contact') }}" />
                </svg>
                <a
                  href="https://wa.me/6282261900070"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="hover:text-[#00A8E8] transition tabular-nums"
                  >+62 822-61-9000-70</a
                >
              </li>
              <li class="flex items-center gap-2.5">
                <svg
                  class="w-4 h-4 text-[#00A8E8] shrink-0"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  viewBox="0 0 24 24"
                >
                  <use href="{{ asset('assets/img/icon/sprite.svg#mail') }}" />
                </svg>
                <a
                  href="mailto:info@smktibaliglobalbadung.sch.id"
                  class="hover:text-[#00A8E8] transition break-all"
                  >info@smktibaliglobalbadung.sch.id</a
                >
              </li>
            </ul>
          </div>
        </div>

        <!-- Baris Bawah: Hak Cipta & Tautan Legal -->
        <div
          class="pt-6 border-t border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500"
        >
          <p class="text-center sm:text-left">
            &copy; 2027 SMK TI Bali Global Badung. All Rights Reserved.
          </p>
          <div class="flex items-center gap-6">
            <a
              href="{{ route('kebijakan-privasi') }}"
              class="hover:text-[#00A8E8] transition"
              >Kebijakan Privasi</a
            >
            <a
              href="{{ route('syarat-ketentuan') }}"
              class="hover:text-[#00A8E8] transition"
              >Syarat &amp; Ketentuan</a
            >
          </div>
        </div>
      </div>
    </footer>
@endsection

@push('scripts')
<!-- Scroll Listener & Navbar UI Scripts -->
    <script>
      (function () {
        const initialActiveWave = document.body.dataset.activeWave;

        window.setInterval(async function () {
          if (document.hidden) return;

          try {
            const response = await fetch("{{ route('home.wave-status') }}", {
              headers: { Accept: 'application/json' },
              cache: 'no-store',
            });
            if (!response.ok) return;

            const { wave } = await response.json();
            if (wave && wave !== initialActiveWave) {
              window.location.reload();
            }
          } catch {
            // A later poll can recover from temporary network errors.
          }
        }, 15000);

        const navbar = document.getElementById('main-navbar');
        if (!navbar) return;

        function updateNavbarBackground() {
          if (window.scrollY > 0) {
            navbar.classList.remove('nav-at-top');
            navbar.classList.add('nav-scrolled');
          } else {
            navbar.classList.remove('nav-scrolled');
            navbar.classList.add('nav-at-top');
          }
        }

        window.addEventListener('scroll', updateNavbarBackground, { passive: true });
        // Jalankan saat inisialisasi awal
        updateNavbarBackground();

        // Dropdown menu hover and interactive logic
        const menuContainer = document.getElementById('user-menu-container');
        const userDropdown = document.getElementById('user-dropdown-menu');
        const userBtn = document.getElementById('nav-user-btn-auth');
        const chevron = document.getElementById('chevron-icon');
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

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
          if (menuContainer && !menuContainer.contains(e.target)) {
            hideUserDropdown();
          }
        });
      })();
    </script>
@endpush
