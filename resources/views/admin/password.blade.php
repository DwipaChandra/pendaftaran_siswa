@extends('layouts.app')

@section('title', 'Ganti Password Admin - SMK TI Bali Global Badung')
@section('body_class', 'min-h-screen bg-[#f8fafc] text-slate-800 flex items-center justify-center px-4 py-8 sm:py-10 antialiased selection:bg-blue-600 selection:text-white')

@section('content')
    <main
      class="w-full max-w-[500px] -translate-x-[5px] -translate-y-[8px] bg-[#fefefe] rounded-[30px] shadow-[0_24px_70px_-20px_rgba(15,23,42,0.16)] border border-slate-100/90 px-6 pt-4 pb-8 sm:px-10 sm:pt-6 sm:pb-10"
    >
      <div class="flex mb-6">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2.5 text-sm font-semibold text-slate-600 hover:text-blue-600 transition">
          <img src="{{ asset('assets/img/icon/arrow-left.svg') }}" alt="" class="w-5 h-5 object-contain shrink-0" />
          <span>Dasbor</span>
        </a>
      </div>

      <img
        src="{{ asset('assets/img/alogo.png') }}"
        alt="Ganti Password Admin"
        class="mx-auto h-[96px] w-[96px] object-contain"
      />

      <h1 class="mt-4 text-3xl font-bold text-[#111827] text-center tracking-tight">
        Ganti Password Admin
      </h1>
      <p class="mt-2 text-sm text-slate-500 text-center mb-6">
        Perbarui password akun administrator Anda
      </p>

      @if($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-sm font-medium" role="alert">
          {{ $errors->first() }}
        </div>
      @endif

      <form
        id="admin-password-form"
        action="{{ route('admin.password.update') }}"
        method="POST"
        class="space-y-6"
      >
        @csrf
        @method('PUT')

        @php
          $passwordFields = [
              ['id' => 'admin-password', 'name' => 'password', 'label' => 'PASSWORD BARU', 'icon' => 'input-lock.svg', 'placeholder' => 'Minimal 8 karakter', 'toggle_label' => 'Tampilkan password', 'wrapper_class' => 'pt-3', 'has_feedback' => false],
              ['id' => 'admin-password-confirmation', 'name' => 'password_confirmation', 'label' => 'KONFIRMASI PASSWORD', 'icon' => 'input-shield.svg', 'placeholder' => 'Ulangi password', 'toggle_label' => 'Tampilkan konfirmasi password', 'wrapper_class' => '', 'has_feedback' => true],
          ];
        @endphp
        @foreach ($passwordFields as $field)
          <div @if($field['wrapper_class']) class="{{ $field['wrapper_class'] }}" @endif>
            <label
              for="{{ $field['id'] }}"
              class="block text-xs font-bold tracking-wider text-slate-600 uppercase mb-2"
            >
              {{ $field['label'] }}
            </label>
            <div class="relative flex items-center">
              <img
                src="{{ asset('assets/img/icon/' . $field['icon']) }}"
                alt=""
                class="w-5 h-5 object-contain absolute left-4 pointer-events-none"
              />
              <input
                type="password"
                name="{{ $field['name'] }}"
                id="{{ $field['id'] }}"
                required
                minlength="8"
                autocomplete="new-password"
                placeholder="{{ $field['placeholder'] }}"
                class="w-full rounded-[24px] border border-slate-200/90 bg-white pl-12 pr-14 py-4 text-sm sm:text-base placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-500/15 transition"
              />
              <button
                type="button"
                data-password-toggle
                aria-controls="{{ $field['id'] }}"
                aria-label="{{ $field['toggle_label'] }}"
                aria-pressed="false"
                title="{{ $field['toggle_label'] }}"
                class="absolute right-1.5 flex h-11 w-11 items-center justify-center rounded-xl text-slate-500 hover:text-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/30 transition-colors"
              >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <use href="{{ asset('assets/img/icon/sprite.svg#eye-open') }}" />
                  <path data-password-slash d="M4 4l16 16" />
                </svg>
              </button>
            </div>
            @if ($field['has_feedback'])
              <p id="password-match-feedback" class="mt-2 min-h-5 text-xs" aria-live="polite"></p>
            @endif
          </div>
        @endforeach

        <button
          type="submit"
          class="w-full py-4 px-6 rounded-[24px] bg-gradient-to-r from-blue-700 via-blue-600 to-cyan-600 hover:-translate-y-1 hover:shadow-xl hover:shadow-blue-600/40 text-white font-bold text-base shadow-lg shadow-blue-600/25 transition-transform duration-200 ease-out flex items-center justify-center cursor-pointer"
        >
          Simpan Password Baru
        </button>
      </form>
    </main>
@endsection

@push('scripts')
    <script>
      const passwordInput = document.getElementById('admin-password');
      const confirmationInput = document.getElementById('admin-password-confirmation');
      const matchFeedback = document.getElementById('password-match-feedback');

      function updatePasswordMatch() {
        const hasConfirmation = confirmationInput.value.length > 0;
        const matches = passwordInput.value === confirmationInput.value;

        confirmationInput.setCustomValidity(hasConfirmation && !matches ? 'Konfirmasi password tidak cocok.' : '');
        matchFeedback.textContent = !hasConfirmation
          ? ''
          : matches
            ? 'Konfirmasi password cocok.'
            : 'Konfirmasi password belum cocok.';
        matchFeedback.className = `mt-2 min-h-5 text-xs ${!hasConfirmation ? '' : matches ? 'text-emerald-600' : 'text-rose-600'}`;
      }

      passwordInput.addEventListener('input', updatePasswordMatch);
      confirmationInput.addEventListener('input', updatePasswordMatch);
    </script>
@endpush
