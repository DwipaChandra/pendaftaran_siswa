@php
  $applicationStatus = $pendaftaran->status_berkas ?? 'Menunggu Verifikasi';
  $verificationNote = trim((string) ($pendaftaran->verifikasi?->catatan_verifikator ?? ''));
  $statusPresentation = match ($applicationStatus) {
      'Diterima' => [
          'panel' => 'bg-gradient-to-br from-emerald-50 to-lime-50',
          'icon' => 'bg-emerald-600 ring-emerald-100',
          'heading' => 'text-emerald-700',
          'title' => 'Selamat, '.$calonSiswa->nama_lengkap.'! Anda DITERIMA',
          'steps' => ['Cetak kartu', 'Masuk grup', 'Daftar ulang'],
      ],
      'Perbaikan Berkas' => [
          'panel' => 'bg-gradient-to-br from-amber-50 to-orange-50',
          'icon' => 'bg-amber-500 ring-amber-100',
          'heading' => 'text-amber-950',
          'title' => 'Ada Berkas yang Perlu Diperbaiki',
      ],
      'Ditolak' => [
          'panel' => 'bg-gradient-to-br from-rose-50 to-red-50',
          'icon' => 'bg-rose-600 ring-rose-100',
          'heading' => 'text-rose-950',
          'title' => 'Status Pendaftaran: Ditolak',
      ],
      default => [
          'panel' => 'bg-gradient-to-br from-blue-50 to-sky-50',
          'icon' => 'bg-blue-600 ring-blue-100',
          'heading' => 'text-blue-950',
          'title' => 'Berkasmu Sedang Diverifikasi',
      ],
  };

  $revisionFields = [
      ['name' => 'berkas_kk', 'label' => 'Kartu Keluarga (KK)', 'accept' => '.pdf,.jpg,.jpeg,.png,.webp'],
      ['name' => 'berkas_akta', 'label' => 'Akta Kelahiran', 'accept' => '.pdf,.jpg,.jpeg,.png,.webp'],
      ['name' => 'berkas_foto', 'label' => 'Pasfoto Formal', 'accept' => '.jpg,.jpeg,.png,.webp'],
  ];
@endphp

<section
  id="application-status"
  class="no-print border-t border-slate-200/80 {{ $statusPresentation['panel'] }} px-4 py-4 sm:px-5 sm:py-5"
  aria-labelledby="application-status-title"
>
  <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-center">
    <div class="flex min-w-0 items-start gap-3 sm:gap-4">
      <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full ring-4 {{ $statusPresentation['icon'] }} text-white shadow-sm" aria-hidden="true">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          @if ($applicationStatus === 'Diterima')
            <use href="{{ asset('assets/img/icon/sprite.svg#check') }}" />
          @elseif ($applicationStatus === 'Perbaikan Berkas')
            <use href="{{ asset('assets/img/icon/sprite.svg#status-alert') }}" />
          @elseif ($applicationStatus === 'Ditolak')
            <use href="{{ asset('assets/img/icon/sprite.svg#status-close') }}" />
          @else
            <use href="{{ asset('assets/img/icon/sprite.svg#status-clock') }}" />
          @endif
        </svg>
      </span>
      <h2 id="application-status-title" class="min-w-0 mt-1 text-lg font-black leading-snug {{ $statusPresentation['heading'] }} sm:text-xl">
        {{ $statusPresentation['title'] }}
      </h2>
    </div>

    @if ($applicationStatus === 'Diterima')
      <ol class="flex flex-wrap gap-2 lg:justify-end" aria-label="Langkah selanjutnya">
        @foreach ($statusPresentation['steps'] as $stepIndex => $step)
          <li class="inline-flex items-center gap-2 rounded-full border border-white/80 bg-white/80 py-1.5 pl-1.5 pr-3 text-[11px] font-bold text-slate-700 shadow-sm sm:text-xs">
            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full {{ $statusPresentation['icon'] }} text-[10px] text-white">{{ $stepIndex + 1 }}</span>
            {{ $step }}
          </li>
        @endforeach
      </ol>
    @endif
  </div>

  @if ($applicationStatus === 'Diterima')
    <div class="mt-5 grid gap-3 sm:grid-cols-2">
      <article class="rounded-2xl border border-white/90 bg-white/90 p-4 shadow-sm sm:p-5">
        <div class="flex items-start gap-3">
          <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700" aria-hidden="true">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <use href="{{ asset('assets/img/icon/sprite.svg#status-map-pin') }}" />
            </svg>
          </span>
          <div class="min-w-0">
            <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Alamat Sekolah</p>
            <h3 class="mt-1 text-sm font-bold leading-relaxed text-slate-900 sm:text-base">Daftar Ulang di Tempat</h3>
            <a
              href="https://maps.google.com/?q=SMK+TI+Bali+Global+Badung+Jl.+Tibungsari+No.+1+Dalung+Kuta+Utara+Badung+Bali"
              target="_blank"
              rel="noopener noreferrer"
              class="mt-3 inline-flex cursor-pointer items-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-bold text-blue-700 transition hover:bg-blue-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
            >
              Buka di Maps
              <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><use href="{{ asset('assets/img/icon/sprite.svg#external-link') }}" /></svg>
            </a>
          </div>
        </div>
      </article>

      <article class="rounded-2xl border border-white/90 bg-white/90 p-4 shadow-sm sm:p-5">
        <div class="flex items-start gap-3">
          <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700" aria-hidden="true">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <use href="{{ asset('assets/img/icon/sprite.svg#chat-bubble') }}" />
            </svg>
          </span>
          <div class="min-w-0">
            <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Kontak Panitia</p>
            <h3 class="mt-1 text-sm font-bold text-slate-900 sm:text-base">Chat Panitia via WhatsApp</h3>
            <div class="mt-3 flex flex-wrap gap-2">
              <a
                href="https://chat.whatsapp.com/I0516gJOhRe5bsQJEMAaxm?s=cl&amp;p=i&amp;mlu=4&amp;ilr=4"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex cursor-pointer items-center gap-2 rounded-lg bg-emerald-600 px-3 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2"
                title="Masuk Grup Calon Siswa"
              >
                <span>Masuk Grup Calon Siswa</span>
                <span class="text-[11px] opacity-90">(Masuk grup)</span>
              </a>
            </div>
          </div>
        </div>
      </article>
    </div>
  @endif

  @if (in_array($applicationStatus, ['Perbaikan Berkas', 'Ditolak', 'Diterima'], true))
    <div class="mt-4 rounded-xl border border-white/90 bg-white/85 p-3 sm:p-4">
      <p class="text-xs font-extrabold uppercase tracking-wide {{ $statusPresentation['heading'] }}">Catatan Resmi Verifikator</p>
      <p class="mt-1 text-sm leading-relaxed text-slate-700">
        {{ $verificationNote !== '' ? $verificationNote : 'Belum ada catatan resmi dari verifikator.' }}
      </p>
    </div>
  @endif

  @if ($applicationStatus === 'Perbaikan Berkas')
    <div class="mt-4 rounded-2xl border border-amber-200/90 bg-white/95 p-4 sm:p-5 shadow-sm">
      <h3 class="text-sm sm:text-base font-bold text-slate-900 mb-1">Formulir Unggah Ulang Berkas</h3>
      <p class="text-xs text-slate-500 mb-4">Silakan unggah ulang dokumen yang perlu diperbaiki sesuai catatan resmi verifikator di atas.</p>

      <form id="form-revisi-berkas" action="{{ route('pendaftaran.revisi-berkas') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div class="grid gap-3 sm:grid-cols-2">
          @foreach ($revisionFields as $field)
            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">{{ $field['label'] }}</label>
              <input type="file" name="{{ $field['name'] }}" accept="{{ $field['accept'] }}" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100" />
            </div>
          @endforeach
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-slate-100">
          <p class="text-[11px] text-slate-400">Format yang diterima: PDF / JPG / PNG (maks. 10MB).</p>
          <button type="submit" class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl transition active:scale-95 cursor-pointer">
            Kirim Perbaikan Berkas
          </button>
        </div>
      </form>
    </div>

    <div class="mt-4 rounded-2xl border border-amber-200/90 bg-white/95 p-4 sm:p-5 shadow-sm">
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h3 class="text-sm font-bold text-slate-900 sm:text-base">Ada berkas yang perlu diperbaiki?</h3>
          <p class="mt-1 text-xs text-slate-500">Hubungi admin melalui WhatsApp untuk mendapatkan arahan perbaikan berkas.</p>
          <p class="mt-2 text-sm font-bold text-slate-700">0822-6190-0070</p>
        </div>
        <a
          href="https://wa.me/6282261900070?text=Halo%20Admin%2C%20saya%20perlu%20memperbaiki%20berkas%20pendaftaran.%20Mohon%20arahan."
          target="_blank"
          rel="noopener noreferrer"
          class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-amber-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-amber-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500"
        >
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <use href="{{ asset('assets/img/icon/sprite.svg#whatsapp') }}" />
          </svg>
          Hubungi Admin via WhatsApp
        </a>
      </div>
    </div>
  @endif
</section>
