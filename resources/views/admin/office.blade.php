<!-- ============================================================== -->
<!-- 1. HALAMAN GELOMBANG (1 KARTU UTAMA & 4 KARTU TOMBOL MINI)     -->
<!-- ============================================================== -->
<section
  id="section-office"
  class="space-y-4 sm:space-y-5 no-print transition-all {{ ($activePage ?? 'status-berkas') !== 'office' ? 'hidden' : '' }}"
>
  <span class="sr-only">Office Administrasi</span>

  <div class="max-w-4xl mx-auto space-y-4 sm:space-y-5">
    <!-- 1. KARTU UTAMA DI TENGAH (STATUS GELOMBANG AKTIF) -->
    <div class="bg-gradient-to-b from-white to-slate-50/40 rounded-2xl sm:rounded-3xl border-2 border-slate-200/90 border-b-4 border-b-slate-300/80 p-5 sm:p-6 shadow-sm text-center relative overflow-hidden transition-all">
      <!-- Background subtle gradient aura -->
      <div
        id="office-wave-bg-aura"
        class="absolute -top-24 left-1/2 -translate-x-1/2 w-80 h-80 {{ ($activeWave ?? 'Gelombang Khusus') === 'Tutup Gelombang' ? 'bg-rose-500/10' : 'bg-blue-500/10' }} rounded-full blur-3xl pointer-events-none transition-colors duration-500"
      ></div>

      <div class="relative z-10 flex flex-col items-center">
        <span class="sr-only">Gelombang Pendaftaran</span>

        <!-- Baris Status & Tanggal (Compact di atas Judul Utama) -->
        <div class="flex flex-wrap items-center justify-center gap-2 mb-2">
          <!-- Status Badge Indicator -->
          <span
            id="office-wave-badge"
            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ ($activeWave ?? 'Gelombang Khusus') === 'Tutup Gelombang' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}"
          >
            <span id="office-wave-badge-dot" class="w-1.5 h-1.5 rounded-full {{ ($activeWave ?? 'Gelombang Khusus') === 'Tutup Gelombang' ? 'bg-rose-500' : 'bg-blue-600 animate-pulse' }}"></span>
            <span id="office-wave-badge-text">{{ ($activeWave ?? 'Gelombang Khusus') === 'Tutup Gelombang' ? 'Pendaftaran Sedang Ditutup' : 'Sedang Aktif Dibuka' }}</span>
          </span>

          @php $isTutupWave = ($activeWave ?? 'Gelombang Khusus') === 'Tutup Gelombang'; @endphp
          <!-- Periode Tanggal Gelombang -->
          <span
            id="office-wave-date"
            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-slate-50 text-slate-600 border border-slate-200 {{ $isTutupWave ? 'hidden' : '' }}"
          >
            <svg class="w-3.5 h-3.5 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span id="office-wave-date-text">
              @if (($activeWave ?? 'Gelombang Khusus') === 'Gelombang Khusus')
                07 September 2026 – 01 Januari 2027
              @elseif (($activeWave ?? 'Gelombang Khusus') === 'Gelombang 1')
                02 Januari 2027 – 11 Mei 2027
              @elseif (($activeWave ?? 'Gelombang Khusus') === 'Gelombang 2')
                12 Mei 2027 – 19 Juli 2027
              @endif
            </span>
          </span>
        </div>

        <!-- Judul Besar Gelombang Aktif -->
        <h2 id="office-wave-main-title" class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mb-1.5">
          {{ ($activeWave ?? 'Gelombang Khusus') === 'Tutup Gelombang' ? 'Pendaftaran Ditutup' : ($activeWave ?? 'Gelombang Khusus') }}
        </h2>

        <!-- Deskripsi Status -->
        <p id="office-wave-description" class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto leading-relaxed">
          @if (($activeWave ?? 'Gelombang Khusus') === 'Tutup Gelombang')
            Portal registrasi online SPMB ditutup sementara untuk calon siswa baru.
          @elseif (($activeWave ?? 'Gelombang Khusus') === 'Gelombang Khusus')
            Calon siswa dapat mendaftar melalui jalur khusus / prestasi sebelum gelombang reguler dibuka.
          @elseif (($activeWave ?? 'Gelombang Khusus') === 'Gelombang 1')
            Pendaftaran gelombang 1 dibuka untuk umum bagi seluruh program keahlian.
          @else
            Pendaftaran gelombang 2 sedang berlangsung hingga batas akhir penutupan kuota.
          @endif
        </p>
      </div>

      <!-- Hidden compatibility select for existing controller/tests -->
      <div class="sr-only">
        <select
          id="admin-wave-select"
          aria-label="Pilih gelombang pendaftaran aktif"
          data-current-wave="{{ $activeWave ?? 'Gelombang Khusus' }}"
          onchange="handleSetWave(this)"
        >
          <option value="Gelombang Khusus" @selected(($activeWave ?? 'Gelombang Khusus') === 'Gelombang Khusus')>Gelombang Khusus</option>
          <option value="Gelombang 1" @selected(($activeWave ?? 'Gelombang Khusus') === 'Gelombang 1')>Gelombang 1</option>
          <option value="Gelombang 2" @selected(($activeWave ?? 'Gelombang Khusus') === 'Gelombang 2')>Gelombang 2</option>
          <option value="Tutup Gelombang" @selected(($activeWave ?? 'Gelombang Khusus') === 'Tutup Gelombang')>Tutup Gelombang</option>
        </select>
      </div>
    </div>

    <!-- 2. 4 KARTU MINI DI BAWAH (SEBAGAI TOMBOL PILIHAN) -->
    @php
      $currentWave = $activeWave ?? 'Gelombang Khusus';
      $waveCards = [
        [
          'value' => 'Gelombang Khusus',
          'title' => 'Gelombang Khusus',
          'dateText' => '07 Sep 2026 – 01 Jan 2027',
          'showCalendar' => true,
          'iconPath' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
          'activeCardClass' => 'bg-white border-2 border-blue-600 border-b-4 border-b-blue-700 shadow-sm ring-4 ring-blue-500/10',
          'inactiveCardClass' => 'bg-white border-2 border-slate-200/90 border-b-4 border-b-slate-300 hover:border-blue-400 hover:border-b-blue-500 hover:shadow-sm',
          'activeIconClass' => 'bg-blue-50 text-blue-600',
          'inactiveIconClass' => 'bg-slate-50 text-slate-500 group-hover:text-blue-600 group-hover:bg-blue-50',
          'activePillClass' => 'text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200',
          'inactivePillClass' => 'text-slate-400 bg-slate-50 px-2 py-0.5 rounded-full border border-slate-100 group-hover:text-slate-600',
          'titleClass' => 'text-sm font-extrabold text-slate-900 group-hover:text-blue-600 transition-colors',
          'activeDateClass' => 'text-blue-600',
          'inactiveDateClass' => 'text-slate-400 group-hover:text-blue-600',
          'rightHrClass' => 'hidden sm:flex absolute -right-4 lg:-right-5 top-1/2 -translate-y-1/2 w-4 lg:w-5 px-1 items-center justify-center pointer-events-none z-10',
          'hasBottomHr' => true,
        ],
        [
          'value' => 'Gelombang 1',
          'title' => 'Gelombang 1',
          'dateText' => '02 Jan 2027 – 11 Mei 2027',
          'showCalendar' => true,
          'iconPath' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
          'activeCardClass' => 'bg-white border-2 border-blue-600 border-b-4 border-b-blue-700 shadow-sm ring-4 ring-blue-500/10',
          'inactiveCardClass' => 'bg-white border-2 border-slate-200/90 border-b-4 border-b-slate-300 hover:border-blue-400 hover:border-b-blue-500 hover:shadow-sm',
          'activeIconClass' => 'bg-blue-50 text-blue-600',
          'inactiveIconClass' => 'bg-slate-50 text-slate-500 group-hover:text-blue-600 group-hover:bg-blue-50',
          'activePillClass' => 'text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200',
          'inactivePillClass' => 'text-slate-400 bg-slate-50 px-2 py-0.5 rounded-full border border-slate-100 group-hover:text-slate-600',
          'titleClass' => 'text-sm font-extrabold text-slate-900 group-hover:text-blue-600 transition-colors',
          'activeDateClass' => 'text-blue-600',
          'inactiveDateClass' => 'text-slate-400 group-hover:text-blue-600',
          'rightHrClass' => 'hidden lg:flex absolute -right-5 top-1/2 -translate-y-1/2 w-5 px-1 items-center justify-center pointer-events-none z-10',
          'hasBottomHr' => true,
        ],
        [
          'value' => 'Gelombang 2',
          'title' => 'Gelombang 2',
          'dateText' => '12 Mei 2027 – 19 Jul 2027',
          'showCalendar' => true,
          'iconPath' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
          'activeCardClass' => 'bg-white border-2 border-blue-600 border-b-4 border-b-blue-700 shadow-sm ring-4 ring-blue-500/10',
          'inactiveCardClass' => 'bg-white border-2 border-slate-200/90 border-b-4 border-b-slate-300 hover:border-blue-400 hover:border-b-blue-500 hover:shadow-sm',
          'activeIconClass' => 'bg-blue-50 text-blue-600',
          'inactiveIconClass' => 'bg-slate-50 text-slate-500 group-hover:text-blue-600 group-hover:bg-blue-50',
          'activePillClass' => 'text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200',
          'inactivePillClass' => 'text-slate-400 bg-slate-50 px-2 py-0.5 rounded-full border border-slate-100 group-hover:text-slate-600',
          'titleClass' => 'text-sm font-extrabold text-slate-900 group-hover:text-blue-600 transition-colors',
          'activeDateClass' => 'text-blue-600',
          'inactiveDateClass' => 'text-slate-400 group-hover:text-blue-600',
          'rightHrClass' => 'hidden sm:flex absolute -right-4 lg:-right-5 top-1/2 -translate-y-1/2 w-4 lg:w-5 px-1 items-center justify-center pointer-events-none z-10',
          'hasBottomHr' => true,
        ],
        [
          'value' => 'Tutup Gelombang',
          'title' => 'Pendaftaran Ditutup',
          'dateText' => 'Akses pendaftaran ditutup',
          'showCalendar' => false,
          'iconPath' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
          'activeCardClass' => 'bg-white border-2 border-rose-500 border-b-4 border-b-rose-600 shadow-sm ring-4 ring-rose-500/10',
          'inactiveCardClass' => 'bg-white border-2 border-slate-200/90 border-b-4 border-b-slate-300 hover:border-rose-400 hover:border-b-rose-500 hover:shadow-sm',
          'activeIconClass' => 'bg-rose-50 text-rose-600',
          'inactiveIconClass' => 'bg-slate-50 text-slate-500 group-hover:text-rose-600 group-hover:bg-rose-50',
          'activePillClass' => 'text-rose-700 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-200',
          'inactivePillClass' => 'text-slate-400 bg-slate-50 px-2 py-0.5 rounded-full border border-slate-100 group-hover:text-rose-600 group-hover:border-rose-200',
          'titleClass' => 'text-sm font-extrabold text-slate-900 group-hover:text-rose-600 transition-colors',
          'activeDateClass' => 'text-rose-600',
          'inactiveDateClass' => 'text-slate-400 group-hover:text-rose-600',
          'rightHrClass' => null,
          'hasBottomHr' => false,
        ],
      ];
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4 lg:gap-5">
      @foreach ($waveCards as $card)
        @php $isActiveCard = $currentWave === $card['value']; @endphp
        <button
          type="button"
          onclick="selectAdminWave('{{ $card['value'] }}')"
          data-wave-card="{{ $card['value'] }}"
          class="group relative flex flex-col justify-between p-3.5 sm:p-4 rounded-2xl text-left transition-all duration-150 cursor-pointer outline-none active:translate-y-[2px] active:border-b-2 {{ $isActiveCard ? $card['activeCardClass'] : $card['inactiveCardClass'] }}"
        >
          <div class="flex items-center justify-between gap-2 mb-2.5">
            <div class="w-8 h-8 rounded-xl flex items-center justify-center {{ $isActiveCard ? $card['activeIconClass'] : $card['inactiveIconClass'] }} transition-colors">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['iconPath'] }}" />
              </svg>
            </div>
            <span class="wave-card-pill inline-flex items-center gap-1 text-[10px] font-bold {{ $isActiveCard ? $card['activePillClass'] : $card['inactivePillClass'] }}">
              {{ $isActiveCard ? 'Aktif' : 'Pilih' }}
            </span>
          </div>
          <div>
            <h4 class="{{ $card['titleClass'] }}">{{ $card['title'] }}</h4>
            <div class="wave-card-date mt-1 flex items-center gap-1.5 text-[11px] font-medium {{ $isActiveCard ? $card['activeDateClass'] : $card['inactiveDateClass'] }} transition-colors">
              @if ($card['showCalendar'])
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              @endif
              <span>{{ $card['dateText'] }}</span>
            </div>
          </div>
          @if ($card['rightHrClass'])
            <div class="{{ $card['rightHrClass'] }}" aria-hidden="true">
              <hr class="w-full border-0 border-t border-slate-300" />
            </div>
          @endif
          @if ($card['hasBottomHr'])
            <div class="flex sm:hidden absolute -bottom-[7px] left-8 right-8 items-center justify-center pointer-events-none z-10" aria-hidden="true">
              <hr class="w-full border-0 border-t border-slate-200" />
            </div>
          @endif
        </button>
      @endforeach
    </div>
  </div>
</section>
