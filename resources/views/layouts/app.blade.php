<!doctype html>
<html lang="id" class="@yield('html_class')" @yield('html_attributes')>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'SPMB 2027/2028 - SMK TI BALI GLOBAL BADUNG')</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/alogo.png') }}" />
    <link rel="shortcut icon" href="{{ asset('assets/img/alogo.png') }}" type="image/x-icon" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts: Plus Jakarta Sans, DM Sans, Fraunces, Bricolage Grotesque -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@800&family=DM+Sans:wght@100..1000&family=Fraunces:wght@600;700&family=Plus+Jakarta+Sans:wght@200..800&display=swap"
      rel="stylesheet"
    />

    <style>
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

      [class~="text-slate-900"],
      [class~="text-slate-950"],
      [class~="text-[#111827]"],
      [class~="text-[#212121]"] {
        color: #141414;
      }

      /* Minimalist Custom Scrollbar */
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
    </style>
    @stack('styles')
  </head>
  <body class="@yield('body_class', 'min-h-screen text-slate-800 antialiased selection:bg-blue-600 selection:text-white') @yield('body_class_extra')" @yield('body_attributes')>
    @include('partials.toast')
    @include('partials.logout-modal')
    @yield('content')
    <script>
      document.querySelectorAll('[data-password-toggle]').forEach(button => {
        const passwordInput = document.getElementById(button.getAttribute('aria-controls'));
        if (!passwordInput) return;

        button.addEventListener('click', () => {
          const isVisible = passwordInput.type === 'password';
          passwordInput.type = isVisible ? 'text' : 'password';
          button.setAttribute('aria-pressed', String(isVisible));
          button.setAttribute('aria-label', isVisible ? 'Sembunyikan password' : 'Tampilkan password');
          button.title = isVisible ? 'Sembunyikan password' : 'Tampilkan password';
          button.querySelector('[data-password-slash]')?.classList.toggle('hidden', isVisible);
        });
      });
    </script>
    <script src="{{ asset('assets/js/phone-input.js') }}"></script>
    @stack('scripts')
  </body>
</html>
