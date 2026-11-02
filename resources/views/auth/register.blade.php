@extends('layouts.app')

@section('title', 'Pendaftaran Siswa Baru - SMK TI Bali Global Badung')
@section('body_class', 'min-h-screen bg-[#f8fafc] text-slate-800 flex items-center justify-center px-4 py-8 sm:py-10 antialiased selection:bg-blue-600 selection:text-white')

@section('content')
    <!-- Card Daftar Akun -->
    <main
      class="w-full max-w-[500px] -translate-x-[5px] -translate-y-[8px] bg-[#fefefe] rounded-[30px] shadow-[0_24px_70px_-20px_rgba(15,23,42,0.16)] border border-slate-100/90 px-6 pt-4 pb-8 sm:px-10 sm:pt-6 sm:pb-10"
    >
      <div class="flex mb-5">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 text-sm font-semibold text-slate-600 hover:text-blue-600 transition">
          <img src="{{ asset('assets/img/icon/arrow-left.svg') }}" alt="" class="w-5 h-5 object-contain shrink-0" />
          <span>Beranda</span>
        </a>
      </div>

      <!-- Top Icon -->
      <img
        src="{{ asset('assets/img/alogo.png') }}"
        alt="Daftar Akun"
        class="mx-auto h-[108px] w-[108px] object-contain"
      />

      <!-- Heading & Subtitle -->
      <h1
        class="mt-4 -translate-y-0.5 text-3xl font-bold text-[#212121] text-center tracking-tight"
      >
        Daftar Akun
      </h1>
      <p class="mt-2 text-sm text-slate-500 text-center mb-6">
        Mulai perjalanan akademikmu di SMK TI Bali Global Badung
      </p>

      <!-- Alert Notifikasi Error -->
      @if(isset($errors) && $errors->any())
        <div class="mb-6 flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-700" role="alert">
          <svg class="mt-0.5 h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <use href="{{ asset('assets/img/icon/sprite.svg#alert-circle') }}" />
          </svg>
          <div class="text-sm font-medium leading-relaxed">
            {{ $errors->first() }}
          </div>
        </div>
      @endif

      <!-- Form Daftar Akun -->
      <form
        id="signup-form"
        action="{{ route('register.post') }}"
        method="POST"
        class="space-y-6"
      >
        @csrf

        <!-- Nama Lengkap -->
        <div class="pt-2">
          <label
            for="signup-name"
            class="block text-xs font-bold tracking-wider text-slate-600 uppercase mb-2"
          >
            NAMA LENGKAP
          </label>
          <div class="relative flex items-center">
            <img
              src="{{ asset('assets/img/icon/input-user.svg') }}"
              alt="Nama"
              class="w-5 h-5 object-contain absolute left-4 pointer-events-none"
            />
            <input
              type="text"
              name="nama_lengkap"
              id="signup-name"
              value="{{ old('nama_lengkap') }}"
              required
              placeholder="Masukkan nama sesuai ijazah"
              class="w-full rounded-[24px] border border-slate-200/90 bg-white pl-12 pr-4 py-4 text-sm sm:text-base placeholder-slate-400 hover:border-slate-300 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-500/15 transition"
            />
          </div>
        </div>

        <!-- Email -->
        <div>
          <label
            for="signup-email"
            class="block text-xs font-bold tracking-wider text-slate-600 uppercase mb-2"
          >
            EMAIL
          </label>
          <div class="relative flex items-center">
            <img
              src="{{ asset('assets/img/icon/input-mail.svg') }}"
              alt="Email"
              class="w-5 h-5 object-contain absolute left-4 pointer-events-none"
            />
            <input
              type="email"
              name="email"
              id="signup-email"
              value="{{ old('email') }}"
              required
              placeholder="nama@email.com"
              class="w-full rounded-[24px] border border-slate-200/90 bg-white pl-12 pr-4 py-4 text-sm sm:text-base placeholder-slate-400 hover:border-slate-300 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-500/15 transition"
            />
          </div>
        </div>

        <!-- Nomor WhatsApp -->
        <div>
          <label
            for="signup-whatsapp"
            class="block text-xs font-bold tracking-wider text-slate-600 uppercase mb-2"
          >
            NOMOR WHATSAPP
          </label>
          <div class="relative flex items-center">
            <img
              src="{{ asset('assets/img/icon/input-whatsapp.svg') }}"
              alt="WhatsApp"
              class="w-5 h-5 object-contain absolute left-4 pointer-events-none"
            />
            <input
              type="tel"
              name="no_whatsapp"
              id="signup-whatsapp"
              data-phone-input
              value="{{ old('no_whatsapp') }}"
              required
              inputmode="tel"
              pattern="[+]?[0-9]+"
              maxlength="20"
              title="Nomor WhatsApp hanya boleh berisi angka dan tanda + opsional di awal"
              placeholder="08XXXXXXXXXX"
              class="w-full rounded-[24px] border border-slate-200/90 bg-white pl-12 pr-4 py-4 text-sm sm:text-base placeholder-slate-400 hover:border-slate-300 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-500/15 transition"
            />
          </div>
        </div>

        @php
          $passwordFields = [
              ['id' => 'signup-password', 'name' => 'password', 'label' => 'PASSWORD', 'icon' => 'input-lock.svg', 'alt' => 'Password', 'placeholder' => 'Minimal 8 karakter', 'toggle_label' => 'Tampilkan password'],
              ['id' => 'signup-password-confirmation', 'name' => 'password_confirmation', 'label' => 'KONFIRMASI PASSWORD', 'icon' => 'input-shield.svg', 'alt' => 'Konfirmasi Password', 'placeholder' => 'Ulangi password', 'toggle_label' => 'Tampilkan konfirmasi password'],
          ];
        @endphp
        @foreach ($passwordFields as $field)
          <div>
            <label
              for="{{ $field['id'] }}"
              class="block text-xs font-bold tracking-wider text-slate-600 uppercase mb-2"
            >
              {{ $field['label'] }}
            </label>
            <div class="relative flex items-center">
              <img
                src="{{ asset('assets/img/icon/' . $field['icon']) }}"
                alt="{{ $field['alt'] }}"
                class="w-5 h-5 object-contain absolute left-4 pointer-events-none"
              />
              <input
                type="password"
                name="{{ $field['name'] }}"
                id="{{ $field['id'] }}"
                required
                minlength="8"
                placeholder="{{ $field['placeholder'] }}"
                class="w-full rounded-[24px] border border-slate-200/90 bg-white pl-12 pr-14 py-4 text-sm sm:text-base placeholder-slate-400 hover:border-slate-300 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-500/15 transition"
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
          </div>
        @endforeach

        <!-- Submit Button -->
        <div class="pt-2">
          <button
            type="submit"
            class="w-full py-4 px-6 rounded-[24px] bg-gradient-to-r from-blue-700 via-blue-600 to-cyan-600 hover:-translate-y-1 hover:shadow-xl hover:shadow-blue-600/40 text-white font-bold text-base shadow-lg shadow-blue-600/25 transition-transform duration-200 ease-out flex items-center justify-center cursor-pointer"
          >
            Buat Akun Sekarang
          </button>
        </div>
      </form>

      <!-- Bottom Login Link -->
      <p class="text-center text-sm text-slate-500 mt-6">
        Sudah punya akun?
        <a
          href="{{ route('login') }}"
          class="font-bold text-blue-700 hover:text-blue-800 hover:underline transition"
          >Login di sini</a
        >
      </p>
    </main>
@endsection
