@extends('layouts.app')

@section('title', 'Login Calon Siswa - SMK TI Bali Global Badung')
@section('body_class', 'min-h-screen bg-[#f8fafc] text-slate-800 flex items-center justify-center px-4 py-8 sm:py-10 antialiased selection:bg-blue-600 selection:text-white')

@section('content')
    <!-- Card Login Calon Siswa -->
    <main
      class="w-full max-w-[500px] -translate-x-[5px] -translate-y-[8px] bg-[#fefefe] rounded-[30px] shadow-[0_24px_70px_-20px_rgba(15,23,42,0.16)] border border-slate-100/90 px-6 pt-4 pb-8 sm:px-10 sm:pt-6 sm:pb-10"
    >
      <div class="flex mb-6">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 text-sm font-semibold text-slate-600 hover:text-blue-600 transition">
          <img src="{{ asset('assets/img/icon/arrow-left.svg') }}" alt="" class="w-5 h-5 object-contain shrink-0" />
          <span>Beranda</span>
        </a>
      </div>

      <!-- Top Icon -->
      <img
        src="{{ asset('assets/img/alogo.png') }}"
        alt="Login Calon Siswa"
        class="mx-auto h-[96px] w-[96px] object-contain"
      />

      <!-- Heading & Subtitle -->
      <h1
        class="mt-4 text-3xl font-bold text-[#111827] text-center tracking-tight"
      >
        Login Pengguna
      </h1>
      <p class="mt-2 text-sm text-slate-500 text-center mb-6">
        Masuk untuk melengkapi berkas pendaftaran Anda
      </p>

      <!-- Form Login Calon Siswa -->
      <form
        id="login-form"
        action="{{ route('login.post') }}"
        method="POST"
        class="space-y-6"
      >
        @csrf

        <!-- Email -->
        <div class="pt-2">
          <label
            for="login-email"
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
              id="login-email"
              value="{{ old('email') }}"
              required
              placeholder="nama@email.com"
              class="w-full rounded-[24px] border border-slate-200/90 bg-white pl-12 pr-4 py-4 text-sm sm:text-base placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-500/15 transition"
            />
          </div>
        </div>

        <!-- Password -->
        <div>
          <label
            for="login-password"
            class="block text-xs font-bold tracking-wider text-slate-600 uppercase mb-2"
          >
            PASSWORD
          </label>
          <div class="relative flex items-center">
            <img
              src="{{ asset('assets/img/icon/input-lock.svg') }}"
              alt="Password"
              class="w-5 h-5 object-contain absolute left-4 pointer-events-none"
            />
            <input
              type="password"
              name="password"
              id="login-password"
              required
              placeholder="••••••••"
              class="w-full rounded-[24px] border border-slate-200/90 bg-white pl-12 pr-14 py-4 text-sm sm:text-base placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-500/15 transition"
            />
            <button
              type="button"
              data-password-toggle
              aria-controls="login-password"
              aria-label="Tampilkan password"
              aria-pressed="false"
              title="Tampilkan password"
              class="absolute right-1.5 flex h-11 w-11 items-center justify-center rounded-xl text-slate-500 hover:text-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/30 transition-colors"
            >
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <use href="{{ asset('assets/img/icon/sprite.svg#eye-open') }}" />
                <path data-password-slash d="M4 4l16 16" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Remember Me & Forgot Password Row -->
        <div class="flex items-center justify-between pt-2.5">
          <label
            for="login-remember"
            class="inline-flex items-center gap-2.5 cursor-pointer select-none"
          >
            <input
              type="checkbox"
              name="remember"
              id="login-remember"
              class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20 cursor-pointer"
            />
            <span class="text-sm text-slate-500">Ingat saya</span>
          </label>
          <a
            href="https://wa.me/6282261900070?text=Halo%20Admin,%20saya%20lupa%20password%20akun%20SPMB."
            target="_blank"
            class="text-sm font-bold text-blue-700 hover:text-blue-800 hover:underline transition"
          >
            Lupa Password?
          </a>
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          class="w-full py-4 px-6 rounded-[24px] bg-gradient-to-r from-blue-700 via-blue-600 to-cyan-600 hover:-translate-y-1 hover:shadow-xl hover:shadow-blue-600/40 text-white font-bold text-base shadow-lg shadow-blue-600/25 transition-transform duration-200 ease-out flex items-center justify-center cursor-pointer"
        >
          Masuk ke Dashboard
        </button>
      </form>

      <!-- Bottom Sign Up Link -->
      <p class="text-center text-sm text-slate-500 mt-6">
        Belum punya akun?
        <a
          href="{{ route('register') }}"
          class="font-bold text-blue-700 hover:text-blue-800 hover:underline transition"
          >Daftar sekarang</a
        >
      </p>
    </main>
@endsection
