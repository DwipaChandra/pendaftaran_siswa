<!doctype html>
<html lang="id" class="@yield('html_class')" @yield('html_attributes')>
  <head>
    <!-- Early Admin Theme & Density Initializer (Zero Flicker) -->
    <script>
      (function() {
        try {
          var s = JSON.parse(localStorage.getItem('admin_theme_settings') || '{}');
          if (s.mode === 'dark') {
            document.documentElement.classList.add('admin-dark', 'dark');
          }
          if (s.density === 'compact') document.documentElement.classList.add('admin-compact');
          if (s.sidebarBg) document.documentElement.style.setProperty('--admin-sidebar-bg', s.sidebarBg);
          if (s.mainBg) document.documentElement.style.setProperty('--admin-bg-color', s.mainBg);
          if (s.cardBg) document.documentElement.style.setProperty('--admin-card-bg', s.cardBg);
        } catch(e) {}
      })();
    </script>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Dasbor Panitia SPMB 2027 - SMK TI Bali Global Badung')</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/alogo.png') }}" />
    <link rel="shortcut icon" href="{{ asset('assets/img/alogo.png') }}" type="image/x-icon" />

    <script>
      window.tailwind = window.tailwind || {};
      window.tailwind.config = { darkMode: 'class' };
    </script>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts: Plus Jakarta Sans & DM Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@100..1000&family=Plus+Jakarta+Sans:wght@200..800&display=swap"
      rel="stylesheet"
    />

    <!-- Leaflet CSS & JS for Beranda Interactive School Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <style>
      html {
        scrollbar-gutter: stable;
      }

      body {
        font-family: "Plus Jakarta Sans", "DM Sans", system-ui, -apple-system, sans-serif;
      }

      input:-webkit-autofill,
      input:-webkit-autofill:hover,
      input:-webkit-autofill:focus,
      input:-webkit-autofill:active {
        -webkit-box-shadow: 0 0 0 1000px #fff inset !important;
        -webkit-text-fill-color: #1e293b !important;
        caret-color: #1e293b;
      }

      .text-slate-900,
      [class~="text-slate-900"],
      [class~="text-slate-950"],
      [class~="text-[#111827]"],
      [class~="text-[#212121]"] {
        color: #141414;
      }

      ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
      }
      ::-webkit-scrollbar-track {
        background: #f1f5f9;
      }
      ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 9999px;
      }
      ::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
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

      /* Admin Sidebar & Main Content Layout Transitions (Anti-Jitter / Butter-Smooth) */
      #admin-sidebar {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        will-change: transform;
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        transform: translate3d(-100%, 0, 0);
      }

      #admin-sidebar.translate-x-0 {
        transform: translate3d(0, 0, 0) !important;
      }

      #admin-main-content {
        transition: padding-left 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        will-change: padding-left;
      }

      @media (min-width: 1024px) {
        #admin-sidebar {
          transform: translate3d(0, 0, 0);
        }

        /* When sidebar is collapsed on desktop: hide sidebar completely off-screen, full width content */
        #admin-sidebar.sidebar-collapsed {
          transform: translate3d(-100%, 0, 0) !important;
        }

        #admin-main-content.sidebar-collapsed {
          padding-left: 0 !important;
        }

        /* On desktop, only show navbar brand when sidebar is collapsed */
        #admin-main-content:not(.sidebar-collapsed) .sidebar-closed-brand {
          display: none !important;
        }

        #admin-main-content.sidebar-collapsed .sidebar-closed-brand {
          display: flex !important;
        }
      }

      /* Instant initial state if sidebar was previously collapsed (prevents reload flicker) */
      html.admin-sidebar-init-collapsed #admin-sidebar {
        transform: translate3d(-100%, 0, 0) !important;
        transition: none !important;
      }
      html.admin-sidebar-init-collapsed #admin-main-content {
        padding-left: 0 !important;
        transition: none !important;
      }
      html.admin-sidebar-init-collapsed .sidebar-closed-brand {
        display: flex !important;
      }

      #admin-top-sidebar-toggle,
      #admin-top-sidebar-toggle:focus,
      #admin-top-sidebar-toggle:active,
      #admin-top-sidebar-toggle:focus-visible,
      #admin-sidebar-collapse-btn,
      #admin-sidebar-collapse-btn:focus,
      #admin-sidebar-collapse-btn:active,
      #admin-sidebar-collapse-btn:focus-visible {
        outline: none !important;
        box-shadow: none !important;
        -webkit-tap-highlight-color: transparent !important;
      }

      .sidebar-email-btn:hover .sidebar-email-tooltip {
        opacity: 1 !important;
        visibility: visible !important;
      }
    </style>
    @stack('styles')
  </head>
  <body class="@yield('body_class', 'min-h-full flex flex-col text-slate-800 antialiased selection:bg-blue-600 selection:text-white') @yield('body_class_extra')" @yield('body_attributes')>
    @include('partials.toast')
    @include('partials.logout-modal')
    @yield('content')
    <script src="{{ asset('assets/js/phone-input.js') }}"></script>
    @stack('scripts')
  </body>
</html>
