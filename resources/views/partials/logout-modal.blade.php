<!-- Global Modern Logout Confirmation Modal -->
<div
  id="global-logout-modal"
  class="fixed inset-0 z-[9999] bg-slate-900/60 items-center justify-center p-4 transition-all duration-200 hidden opacity-0 pointer-events-none"
  role="dialog"
  aria-modal="true"
  aria-labelledby="logout-modal-title"
>
  <div
    id="global-logout-box"
    class="w-full max-w-sm bg-white rounded-3xl border border-slate-100 shadow-2xl p-6 text-center transition-all duration-200 scale-95"
  >
    <!-- Icon Exit/Logout -->
    <div class="w-14 h-14 mx-auto rounded-full bg-rose-50 border border-rose-100 text-rose-500 flex items-center justify-center mb-4">
      <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
      </svg>
    </div>

    <!-- Title & Description -->
    <h3 id="logout-modal-title" class="text-base sm:text-lg font-bold text-slate-800 tracking-tight">
      Yakin ingin keluar?
    </h3>
    <p class="text-xs sm:text-sm text-slate-500 mt-2 leading-relaxed">
      Sesi login Anda akan diakhiri. Anda perlu memasukkan email dan kata sandi kembali untuk masuk.
    </p>

    <!-- Actions -->
    <div class="flex items-center justify-center gap-3 mt-6">
      <button
        type="button"
        onclick="closeLogoutConfirmModal()"
        class="w-1/2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200/80 transition-colors cursor-pointer active:scale-95"
      >
        Batal
      </button>
      <button
        type="button"
        id="logout-modal-confirm-btn"
        onclick="executeLogoutConfirm()"
        class="w-1/2 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-white bg-rose-600 hover:bg-rose-700 shadow-md shadow-rose-600/20 transition-all cursor-pointer active:scale-95 disabled:opacity-50"
      >
        <span id="logout-modal-confirm-text">Ya, Keluar</span>
        <svg id="logout-modal-spinner" class="hidden w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </button>
    </div>
  </div>
</div>

<script>
  (function () {
    let pendingLogoutTarget = null;

    window.openLogoutConfirmModal = function (target) {
      pendingLogoutTarget = target || null;
      const modal = document.getElementById('global-logout-modal');
      const box = document.getElementById('global-logout-box');
      if (!modal || !box) {
        if (target && typeof target === 'string') {
          const form = document.getElementById(target);
          if (form) form.submit();
        }
        return;
      }

      // Reset button state
      const btn = document.getElementById('logout-modal-confirm-btn');
      const text = document.getElementById('logout-modal-confirm-text');
      const spinner = document.getElementById('logout-modal-spinner');
      if (btn) btn.disabled = false;
      if (text) text.textContent = 'Ya, Keluar';
      if (spinner) spinner.classList.add('hidden');

      modal.classList.replace('hidden', 'flex');
      modal.classList.remove('pointer-events-none');
      void modal.offsetWidth; // Force reflow
      modal.classList.remove('opacity-0');
      modal.classList.add('opacity-100');
      box.classList.remove('scale-95');
      box.classList.add('scale-100');
      document.body.style.overflow = 'hidden';
    };

    window.closeLogoutConfirmModal = function () {
      const modal = document.getElementById('global-logout-modal');
      const box = document.getElementById('global-logout-box');
      if (!modal || !box) return;

      modal.classList.remove('opacity-100');
      modal.classList.add('opacity-0');
      box.classList.remove('scale-100');
      box.classList.add('scale-95');
      setTimeout(() => {
        modal.classList.replace('flex', 'hidden');
        modal.classList.add('pointer-events-none');
        document.body.style.overflow = '';
        pendingLogoutTarget = null;
      }, 200);
    };

    window.executeLogoutConfirm = async function () {
      const btn = document.getElementById('logout-modal-confirm-btn');
      const text = document.getElementById('logout-modal-confirm-text');
      const spinner = document.getElementById('logout-modal-spinner');
      if (btn) btn.disabled = true;
      if (spinner) spinner.classList.remove('hidden');
      if (text) text.textContent = 'Keluar...';

      if (typeof pendingLogoutTarget === 'function') {
        pendingLogoutTarget();
        return;
      }

      if (typeof pendingLogoutTarget === 'string') {
        const form = document.getElementById(pendingLogoutTarget);
        if (form) {
          form.submit();
          return;
        }
      }

      // Fallback: POST fetch to logout endpoint
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
      } catch (e) {
        window.location.replace("{{ route('login') }}");
      }
    };

    // Close on Escape key
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        const modal = document.getElementById('global-logout-modal');
        if (modal && !modal.classList.contains('hidden')) {
          closeLogoutConfirmModal();
        }
      }
    });

    // Close on backdrop click
    document.addEventListener('click', function (e) {
      const modal = document.getElementById('global-logout-modal');
      if (modal && !modal.classList.contains('hidden') && e.target === modal) {
        closeLogoutConfirmModal();
      }
    });
  })();
</script>

