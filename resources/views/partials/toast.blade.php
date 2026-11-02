<!-- Global Modern Minimalist White Toast Notification Container -->
<div
  id="toast-container"
  class="fixed top-5 right-5 z-[9999] flex flex-col gap-2.5 max-w-sm w-full px-4 sm:px-0 pointer-events-none no-print"
></div>

@php
  $toastsToTrigger = [];
  if (session('success') && session('success') !== 'Selamat datang di Dasbor Panitia.') {
      $toastsToTrigger[] = ['msg' => (string) session('success'), 'type' => 'success'];
  }
  $flashToastMap = [
      'status' => 'info',
      'info' => 'info',
      'error' => 'error',
      'danger' => 'error',
      'warning' => 'warning',
      'quota_warning' => 'warning',
      'alert' => 'warning',
  ];
  foreach ($flashToastMap as $sessionKey => $toastType) {
      if (session($sessionKey)) {
          $toastsToTrigger[] = ['msg' => (string) session($sessionKey), 'type' => $toastType];
      }
  }
  if (isset($errors) && $errors->any()) {
      foreach (array_slice($errors->all(), 0, 3) as $errorMessage) {
          $toastsToTrigger[] = ['msg' => (string) $errorMessage, 'type' => 'error'];
      }
  }
@endphp
@if (!empty($toastsToTrigger))
<script id="flash-toasts-data" type="application/json">
  {!! json_encode($toastsToTrigger) !!}
</script>
@endif
<script>
  (function () {
    const icons = {
      success: `
        <div class="w-8 h-8 rounded-xl bg-emerald-50 border border-emerald-100/80 flex items-center justify-center shrink-0">
          <svg class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
        </div>
      `,
      error: `
        <div class="w-8 h-8 rounded-xl bg-rose-50 border border-rose-100/80 flex items-center justify-center shrink-0">
          <svg class="w-4 h-4 text-rose-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </div>
      `,
      warning: `
        <div class="w-8 h-8 rounded-xl bg-amber-50 border border-amber-100/80 flex items-center justify-center shrink-0">
          <svg class="w-4 h-4 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
            <line x1="12" y1="9" x2="12" y2="13"></line>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
          </svg>
        </div>
      `,
      info: `
        <div class="w-8 h-8 rounded-xl bg-blue-50 border border-blue-100/80 flex items-center justify-center shrink-0">
          <svg class="w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="16" x2="12" y2="12"></line>
            <line x1="12" y1="8" x2="12.01" y2="8"></line>
          </svg>
        </div>
      `
    };

    window.showToast = function (message, type = 'success', duration = 4500) {
      if (!message) return;

      const rawType = String(type || 'success').toLowerCase().trim();
      let resolvedType = 'success';
      if (rawType === 'error' || rawType === 'danger' || rawType === 'gagal' || rawType === 'failed') {
        resolvedType = 'error';
      } else if (rawType === 'warning' || rawType === 'warn' || rawType === 'peringatan') {
        resolvedType = 'warning';
      } else if (rawType === 'info' || rawType === 'status' || rawType === 'notice') {
        resolvedType = 'info';
      } else if (rawType in icons) {
        resolvedType = rawType;
      }

      let container = document.getElementById('toast-container');
      if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed top-5 right-5 z-[9999] flex flex-col gap-2.5 max-w-sm w-full px-4 sm:px-0 pointer-events-none no-print';
        document.body.appendChild(container);
      }

      // Hindari duplikasi toast identik dalam waktu bersamaan
      const existing = container.querySelectorAll('[data-toast-msg]');
      for (const el of existing) {
        if (el.dataset.toastMsg === String(message)) {
          return;
        }
      }

      const toast = document.createElement('div');
      toast.dataset.toastMsg = String(message);
      toast.className = 'pointer-events-auto bg-white rounded-2xl p-3.5 sm:p-4 shadow-xl shadow-slate-200/80 border border-slate-100 flex items-center justify-between gap-3 transition-all duration-300 translate-x-8 opacity-0';
      toast.setAttribute('role', 'alert');

      const leftContent = document.createElement('div');
      leftContent.className = 'flex items-center gap-3 min-w-0 flex-1';

      const iconHtml = icons[resolvedType] || icons.success;
      const iconWrapper = document.createElement('div');
      iconWrapper.innerHTML = iconHtml.trim();
      leftContent.appendChild(iconWrapper.firstElementChild);

      const msgEl = document.createElement('p');
      msgEl.className = 'text-xs sm:text-[13px] font-semibold text-slate-800 leading-snug break-words flex-1';
      msgEl.textContent = message;
      leftContent.appendChild(msgEl);

      toast.appendChild(leftContent);

      const closeBtn = document.createElement('button');
      closeBtn.type = 'button';
      closeBtn.className = 'text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer shrink-0 ml-1';
      closeBtn.setAttribute('aria-label', 'Tutup notifikasi');
      closeBtn.innerHTML = `
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      `;

      function dismiss() {
        toast.classList.add('opacity-0', 'translate-x-8');
        setTimeout(() => {
          if (toast.parentNode) {
            toast.remove();
          }
        }, 300);
      }

      closeBtn.onclick = dismiss;
      toast.appendChild(closeBtn);

      container.appendChild(toast);

      requestAnimationFrame(() => {
        toast.classList.remove('opacity-0', 'translate-x-8');
      });

      if (duration > 0) {
        setTimeout(dismiss, duration);
      }
    };

    // Alihkan alert bawaan browser ke toast notifikasi modern
    window.alert = function (message) {
      if (message !== undefined && message !== null && String(message).trim() !== '') {
        window.showToast(String(message), 'warning');
      }
    };

    // Otomatis muncul jika ada flash session atau validation error dari backend Laravel
    document.addEventListener('DOMContentLoaded', function () {
      const flashDataEl = document.getElementById('flash-toasts-data');
      if (flashDataEl) {
        try {
          const list = JSON.parse(flashDataEl.textContent || '[]');
          list.forEach(function (t) {
            window.showToast(t.msg, t.type);
          });
        } catch (e) {}
      }
    });
  })();
</script>
