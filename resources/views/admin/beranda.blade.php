<!-- ============================================================== -->
<!-- 0. HALAMAN BERANDA ADMIN SPMB                                  -->
<!-- ============================================================== -->
@php
  $totalKuota = (int) $jurusanStats->sum('kuota');
  $totalDiterima = (int) $jurusanStats->sum('diterima');
  $totalMenunggu = (int) ($menungguCount ?? 0);
  $totalPendaftarAll = (int) $jurusanStats->sum('terisi');
  $sisaKuota = max(0, $totalKuota - $totalDiterima);
  $persenTotal = $totalKuota > 0 ? min(100, round(($totalDiterima / $totalKuota) * 100)) : 0;

  // Pemetaan Warna & Urutan Resmi 5 Jurusan SPMB
  $jurusanColorMap = [
    'PPLG' => '#2563EB', // Biru
    'DKV'  => '#7C3AED', // Ungu
    'TJKT' => '#0D9488', // Teal / Tosca
    'BD'   => '#D97706', // Amber / Oranye
    'TKC'  => '#E11D48', // Pink / Rose
  ];

  $jurusanOrderMap = [
    'PPLG' => 1,
    'DKV'  => 2,
    'TJKT' => 3,
    'BD'   => 4,
    'TKC'  => 5,
  ];

  // Susun data 5 jurusan berurutan dengan metadata warna dan kode
  $orderedJurusanStats = $jurusanStats->map(function ($j) use ($jurusanColorMap, $jurusanOrderMap) {
    $namaParts = explode(' ', $j['nama_jurusan']);
    $kode = trim($namaParts[0], ' ()');
    if (str_contains(strtoupper($j['nama_jurusan']), 'KECANTIKAN') || $kode === 'TKC' || $kode === 'Tata') {
      $kode = 'TKC';
      $subName = 'Tata Kecantikan';
    } else {
      $subName = trim(preg_replace('/^\s*'.preg_quote($kode, '/').'\s*\((.*?)\)$/', '$1', $j['nama_jurusan']), ' ()');
    }
    $color = $jurusanColorMap[$kode] ?? '#2563EB';
    $order = $jurusanOrderMap[$kode] ?? 99;

    return array_merge($j, [
      'kode' => $kode,
      'nama_sub' => $subName,
      'color' => $color,
      'order' => $order,
    ]);
  })->unique('kode')->sortBy('order')->values();

  // Geometri Donut Chart: Radius 75, Keliling ~471.24
  $circumference = 471.24;
  $accumulatedOffset = 0;

  // Analisis awal insight jurusan paling diminati berdasarkan BANYAK PENDAFTAR
  $maxPendaftar = $orderedJurusanStats->max('terisi') ?? 0;
  $topJurusans = $orderedJurusanStats->where('terisi', $maxPendaftar);
  $topCount = $topJurusans->count();

  if ($totalPendaftarAll === 0 || $maxPendaftar === 0) {
    $initialInsight = "Saat ini belum ada data pendaftar pada kelima jurusan.";
  } elseif ($topCount === 1) {
    $top = $topJurusans->first();
    $pctTop = $totalPendaftarAll > 0 ? (float) round(($top['terisi'] / $totalPendaftarAll) * 100, 1) : 0;
    $initialInsight = "Jurusan paling diminati saat ini adalah <strong>{$top['kode']}</strong> dengan <strong>{$top['terisi']} pendaftar</strong> ({$pctTop}% dari total pendaftar).";
  } elseif ($topCount < $orderedJurusanStats->count()) {
    $codes = $topJurusans->pluck('kode')->implode(' dan ');
    $pctTop = $totalPendaftarAll > 0 ? (float) round(($maxPendaftar / $totalPendaftarAll) * 100, 1) : 0;
    $initialInsight = "Jurusan paling diminati saat ini adalah <strong>{$codes}</strong> dengan masing-masing <strong>{$maxPendaftar} pendaftar</strong> ({$pctTop}% dari total pendaftar).";
  } else {
    $initialInsight = "Seluruh 5 jurusan memiliki jumlah peminat yang sama, yaitu masing-masing <strong>{$maxPendaftar} pendaftar</strong>.";
  }
@endphp

<section
  id="section-beranda"
  class="space-y-5 sm:space-y-6 no-print transition-all {{ ($activePage ?? 'beranda') !== 'beranda' ? 'hidden' : '' }}"
>
  <span class="sr-only">Halaman Beranda SPMB</span>

  <!-- ============================================================== -->
  <!-- 1. KARTU UCAPAN SELAMAT DATANG (WELCOME / GREETING HERO)       -->
  <!-- ============================================================== -->
  <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 p-5 sm:p-6 relative overflow-hidden transition-all">
    <!-- Subtle Background Ambient Aura (Biru Sangat Halus & Minimalis) -->
    <div class="absolute -top-10 -right-10 w-44 h-44 sm:w-52 sm:h-52 bg-gradient-to-br from-blue-500/10 via-sky-400/5 to-transparent rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-5">
      <!-- Avatar Inisial 'A' untuk Admin (Warna Biru Khas SMK TI) -->
      <div class="sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-br from-[#00A8E8] to-blue-600 text-white flex items-center justify-center font-black text-2xl sm:text-3xl shadow-sm shadow-blue-500/20 shrink-0 select-none">
        <span>A</span>
      </div>

      <!-- Informasi Greeting & Identitas Pengguna -->
      <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-500 mb-1">
          <span class="inline-block text-base">👋</span>
          <span>Halo, Selamat Datang!</span>
        </div>

        <h2 class="text-lg sm:text-2xl font-black text-slate-900 tracking-tight leading-snug truncate" title="ADMIN SMK TI BALI GLOBAL BADUNG">
          ADMIN SMK TI BALI GLOBAL BADUNG
        </h2>

        <!-- Baris Metadata: Email & Peran -->
        <div class="flex flex-wrap items-center gap-y-2 gap-x-4 sm:gap-x-6 mt-2 text-xs text-slate-600 font-medium">
          <!-- Email Sekolah / Admin -->
          <div class="inline-flex items-center gap-2">
            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <rect width="20" height="16" x="2" y="4" rx="2" />
              <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
            </svg>
            <span class="truncate text-slate-700">admin@smktibadung.sch.id</span>
          </div>

          <div class="hidden sm:block w-px h-3.5 bg-slate-200"></div>

          <!-- Peran Panitia -->
          <div class="inline-flex items-center gap-2">
            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
              <circle cx="9" cy="7" r="4" />
              <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
              <path d="M16 3.13a4 4 0 0 1 0 7.75" />
            </svg>
            <div class="flex items-center gap-1.5">
              <span class="text-slate-400">Peran</span>
              <span class="font-bold text-slate-800">Panitia Penerima SPMB</span>
            </div>
          </div>

          <div class="hidden sm:block w-px h-3.5 bg-slate-200"></div>

          <!-- Status Gelombang Aktif -->
          <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200/70 text-[11px] font-bold">
            <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
            <span>{{ $activeWave ?? 'Gelombang Khusus' }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ============================================================== -->
  <!-- 2. BARIS 4 KARTU RINGKASAN INFORMASI SEKOLAH                    -->
  <!-- ============================================================== -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
    <!-- Kartu 1: NPSN Sekolah -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 hover:shadow-md hover:border-blue-200 transition-all flex items-center gap-3.5 sm:gap-4">
      <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-blue-50 text-blue-600 border border-blue-200/60 flex items-center justify-center shrink-0 font-black text-lg select-none">
        <span>H</span>
      </div>
      <div class="min-w-0 flex-1">
        <span class="block text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">NPSN Sekolah</span>
        <span class="block text-base sm:text-lg font-black text-slate-900 tracking-tight tabular-nums mt-0.5">69959273</span>
      </div>
    </div>

    <!-- Kartu 2: Akreditasi Sekolah -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 hover:shadow-md hover:border-blue-200 transition-all flex items-center gap-3.5 sm:gap-4">
      <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-blue-50 text-blue-600 border border-blue-200/60 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 9H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h2m12 6h2a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2h-2m-3 18H9m6-4H9m7-9a4 4 0 0 1-8 0V3h8v5z" />
        </svg>
      </div>
      <div class="min-w-0 flex-1">
        <span class="block text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Akreditasi Sekolah</span>
        <div class="flex items-center gap-1.5 mt-0.5">
          <span class="text-base sm:text-lg font-black text-slate-900 tracking-tight">A</span>
          <span class="text-[10px] font-bold text-blue-700 bg-blue-100/70 px-1.5 py-0.5 rounded">Unggul</span>
        </div>
      </div>
    </div>

    <!-- Kartu 3: Jenjang Sekolah -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 hover:shadow-md hover:border-blue-200 transition-all flex items-center gap-3.5 sm:gap-4">
      <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-blue-50 text-blue-600 border border-blue-200/60 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M22 10v6M2 10l10-5 10 5-10 5z" />
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 12v5c3 3 9 3 12 0v-5" />
        </svg>
      </div>
      <div class="min-w-0 flex-1">
        <span class="block text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Jenjang Sekolah</span>
        <span class="block text-base sm:text-lg font-black text-slate-900 tracking-tight mt-0.5">SMK</span>
      </div>
    </div>

    <!-- Kartu 4: Status Sekolah -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 hover:shadow-md hover:border-blue-200 transition-all flex items-center gap-3.5 sm:gap-4">
      <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-blue-50 text-blue-600 border border-blue-200/60 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5m-4 0h4" />
        </svg>
      </div>
      <div class="min-w-0 flex-1">
        <span class="block text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Status Sekolah</span>
        <span class="block text-base sm:text-lg font-black text-slate-900 tracking-tight mt-0.5">Swasta</span>
      </div>
    </div>
  </div>

  <!-- ============================================================== -->
  <!-- 3. DUA KARTU UTAMA BAWAH: DONUT CHART & KUOTA SPMB             -->
  <!-- ============================================================== -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6 items-stretch">
    <!-- ============================================================ -->
    <!-- KARTU KIRI: DISTRIBUSI PEMINAT JURUSAN (DONUT CHART)         -->
    <!-- ============================================================ -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 p-5 sm:p-6 flex flex-col justify-between h-full relative">
      <div class="flex flex-col h-full justify-between gap-3.5">
        <div class="space-y-3.5">
          <!-- Header Kartu: Judul dengan Ikon Chart & Status Real-time -->
          <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
              <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                </svg>
              </div>
              <h3 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                Distribusi Peminat Jurusan
              </h3>
            </div>

            <!-- Badge Status Real-time -->
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/70 text-[11px] font-bold select-none">
              <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
              <span>Real-time</span>
            </div>
          </div>

          <!-- Donut Chart Responsif dengan Lubang Tengah Dinamis (Berdasarkan Banyak Pendaftar) -->
          <div class="relative w-44 h-44 sm:w-48 sm:h-48 mx-auto flex items-center justify-center my-0.5">
            <svg
              id="donut-chart-svg"
              class="w-full h-full transform -rotate-90 select-none overflow-visible"
              viewBox="0 0 220 220"
              role="img"
              aria-label="Grafik Distribusi Peminat 5 Jurusan"
            >
              <!-- Background Ring Circle (Fallback Abu-abu Netral jika Belum Ada Pendaftar) -->
              <circle
                id="donut-bg-ring"
                cx="110"
                cy="110"
                r="75"
                fill="none"
                stroke="{{ $totalPendaftarAll > 0 ? '#F1F5F9' : '#E2E8F0' }}"
                stroke-width="24"
              />

              <!-- 5 Segmen Donut Jurusan Real-time Berdasarkan Banyak Pendaftar -->
              @foreach ($orderedJurusanStats as $j)
                @php
                  $sliceRatio = $totalPendaftarAll > 0 ? ($j['terisi'] / $totalPendaftarAll) : 0;
                  $sliceLength = round($sliceRatio * $circumference, 2);
                  $dashLength = $sliceRatio > 0 ? max(0, $sliceLength - 2) : 0;
                  $gapLength = max(0, $circumference - $dashLength);
                  $dashOffset = round(-$accumulatedOffset, 2);
                  $accumulatedOffset += $sliceLength;
                @endphp
                <circle
                  id="donut-segment-{{ $j['id_jurusan'] }}"
                  data-jurusan-id="{{ $j['id_jurusan'] }}"
                  data-kode="{{ $j['kode'] }}"
                  data-color="{{ $j['color'] }}"
                  cx="110"
                  cy="110"
                  r="75"
                  fill="none"
                  stroke="{{ $j['color'] }}"
                  stroke-width="24"
                  stroke-linecap="butt"
                  stroke-dasharray="{{ $dashLength }} {{ $gapLength }}"
                  stroke-dashoffset="{{ $dashOffset }}"
                  class="donut-segment transition-all duration-700 ease-out cursor-pointer hover:stroke-[28px] hover:opacity-90"
                />
              @endforeach
            </svg>

            <!-- Tampilan Teks di Tengah Lubang Donut (Total Seluruh Pendaftar) -->
            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none select-none text-center px-4">
              <span
                id="donut-center-total"
                class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight tabular-nums transition-all duration-300"
              >{{ $totalPendaftarAll }}</span>
              <span
                id="donut-center-label"
                class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mt-0.5 transition-all duration-300"
              >TOTAL PENDAFTAR</span>
              <span
                id="donut-center-sub"
                class="text-[10px] text-slate-400 font-medium transition-all duration-300"
              >5 Jurusan</span>
            </div>
          </div>

          <!-- Custom Legend Grid (2 Kolom untuk Jurusan 1-4, 2 Baris Selaras untuk TKC) -->
          <div id="donut-legend-grid" class="grid grid-cols-2 gap-2 sm:gap-2.5 pt-0.5">
            @foreach ($orderedJurusanStats as $index => $j)
              @php
                $persenPeminat = $totalPendaftarAll > 0 ? (float) round(($j['terisi'] / $totalPendaftarAll) * 100, 1) : 0;
                $isLastItem = ($loop->iteration === 5);
              @endphp
              <div
                data-legend-item="{{ $j['id_jurusan'] }}"
                data-legend-code="{{ $j['kode'] }}"
                class="{{ $isLastItem ? 'col-span-2' : 'col-span-1' }} bg-[#F8FAFC] border border-slate-200/80 hover:border-slate-300 rounded-xl p-2.5 transition flex flex-col justify-between gap-1.5 cursor-pointer group hover:bg-slate-50"
              >
                <!-- Baris Atas: Titik Warna + Nama Jurusan di Kiri, Persentase Peminat di Kanan -->
                <div class="flex items-center justify-between gap-1.5">
                  <div class="flex items-center gap-2 min-w-0">
                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $j['color'] }}"></span>
                    <span class="text-xs font-bold text-slate-800 tracking-tight truncate">{{ $j['kode'] }}</span>
                    @if ($isLastItem)
                      <span class="text-[11px] text-slate-500 font-medium truncate">({{ $j['nama_sub'] }})</span>
                    @endif
                  </div>
                  <span
                    class="text-[11px] font-extrabold tabular-nums shrink-0"
                    style="color: {{ $j['color'] }}"
                    data-legend-pct="{{ $j['id_jurusan'] }}"
                  >
                    {{ $persenPeminat }}%
                  </span>
                </div>

                <!-- Baris Bawah: Pendaftar di Kiri (Cetak Tebal), Kuota di Kanan -->
                <div class="flex items-center justify-between pt-1 border-t border-slate-100 text-[10.5px] text-slate-500 font-medium">
                  <span>Pendaftar: <strong class="text-slate-900 font-bold tabular-nums" data-legend-pendaftar="{{ $j['id_jurusan'] }}">{{ $j['terisi'] }}</strong></span>
                  <span>Kuota: <strong class="text-slate-700 font-semibold tabular-nums" data-legend-kuota="{{ $j['id_jurusan'] }}">{{ $j['kuota'] }}</strong></span>
                </div>
              </div>
            @endforeach
          </div>
        </div>

        <!-- Kotak Insight Otomatis di Paling Bawah Kartu -->
        <div id="donut-insight-box" class="bg-blue-50/70 border border-blue-100/90 rounded-xl p-3 text-xs text-slate-700 flex items-start gap-2.5 shrink-0 transition-all duration-300">
          <span class="text-base shrink-0 leading-none select-none">💡</span>
          <p id="donut-insight-text" class="leading-relaxed">
            {!! $initialInsight !!}
          </p>
        </div>
      </div>
    </div>

    <!-- ============================================================ -->
    <!-- KARTU KANAN: KUOTA SPMB (STATISTIK LENGKAP & PROGRESS BAR)   -->
    <!-- ============================================================ -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 p-5 sm:p-6 flex flex-col justify-between h-full">
      <div class="space-y-4">
        <!-- Header Kartu: Judul 'Kuota SPMB' & Tombol Aksi Cepat -->
        <div class="flex items-center justify-between gap-3">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
              <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
              </svg>
            </div>
            <h3 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
              Kuota SPMB
            </h3>
          </div>

          <!-- Tombol Menuju Manajemen Kuota Rombel -->
          <a
            href="{{ route('admin.kuota-rombel') }}"
            onclick="handleAdminNavClick(event, 'kuota-rombel')"
            title="Kelola rincian kuota rombel"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100/70 border border-blue-200/60 rounded-xl transition cursor-pointer"
          >
            <span>Detail Rombel</span>
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </a>
        </div>

        <!-- 4 Mini Metric Badges: Total Kuota, Diterima, Menunggu, Sisa -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3">
          <div class="bg-slate-50 rounded-xl p-3 border border-slate-200/80 text-center">
            <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Total Kuota</span>
            <span id="stat-total-kuota" class="block text-base sm:text-lg font-black text-slate-900 tabular-nums mt-0.5">{{ $totalKuota }}</span>
            <span class="text-[10px] text-slate-400">Kursi Rombel</span>
          </div>
          <div class="bg-emerald-50/70 rounded-xl p-3 border border-emerald-200/60 text-center">
            <span class="block text-[10px] font-bold text-emerald-700 uppercase tracking-wider">Diterima</span>
            <span id="stat-total-diterima" class="block text-base sm:text-lg font-black text-emerald-800 tabular-nums mt-0.5">{{ $totalDiterima }}</span>
            <span class="text-[10px] text-emerald-600">Siswa Lolos</span>
          </div>
          <div class="bg-amber-50/70 rounded-xl p-3 border border-amber-200/60 text-center">
            <span class="block text-[10px] font-bold text-amber-700 uppercase tracking-wider">Menunggu</span>
            <span id="stat-total-menunggu" class="block text-base sm:text-lg font-black text-amber-800 tabular-nums mt-0.5">{{ $totalMenunggu }}</span>
            <span class="text-[10px] text-amber-600">Verifikasi</span>
          </div>
          <div class="bg-sky-50/70 rounded-xl p-3 border border-sky-200/60 text-center">
            <span class="block text-[10px] font-bold text-sky-700 uppercase tracking-wider">Sisa Kuota</span>
            <span id="stat-sisa-kuota" class="block text-base sm:text-lg font-black text-sky-800 tabular-nums mt-0.5">{{ $sisaKuota }}</span>
            <span class="text-[10px] text-sky-600">Tersedia</span>
          </div>
        </div>

        <!-- Progress Bar Akumulatif Seluruh Sekolah -->
        <div class="bg-slate-50/80 rounded-xl p-3.5 border border-slate-200/80 space-y-2">
          <div class="flex items-center justify-between text-xs">
            <span class="font-bold text-slate-700">Keterisian Total Kursi</span>
            <span id="stat-persen-total" class="font-extrabold text-blue-600 tabular-nums">{{ $persenTotal }}% Terpenuhi</span>
          </div>
          <div class="bg-slate-200 rounded-full h-3 overflow-hidden">
            <div
              id="stat-bar-total"
              class="bg-gradient-to-r from-blue-600 to-indigo-600 h-full rounded-full transition-all duration-500"
              style="width: {{ $persenTotal }}%"
            ></div>
          </div>
          <div class="flex items-center justify-between text-[11px] text-slate-500">
            <span id="stat-diterima-sub">{{ $totalDiterima }} Siswa Diterima</span>
            <span id="stat-kapasitas-sub">Kapasitas: {{ $totalKuota }} Kursi</span>
          </div>
        </div>

        <!-- Rincian Keterisian per Jurusan (5 Jurusan Utuh Tanpa Scrollbar) -->
        <div class="space-y-2 pt-0.5">
          <span class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Keterisian per Jurusan</span>
          <div id="keterisian-jurusan-list" class="space-y-2">
            @foreach ($orderedJurusanStats as $j)
              @php
                $percent = $j['persen_diterima'];
                $isFull = $j['diterima'] >= $j['kuota'];
              @endphp
              <div
                data-keterisian-item="{{ $j['id_jurusan'] }}"
                class="flex items-center justify-between gap-3 p-2.5 rounded-xl bg-white border border-slate-200/80 hover:border-slate-300 transition"
              >
                <div class="min-w-0 flex-1">
                  <div class="flex items-center justify-between gap-2 mb-1">
                    <div class="flex items-center gap-2 min-w-0">
                      <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $j['color'] }}"></span>
                      <span class="text-xs font-bold text-slate-800 truncate" title="{{ $j['nama_jurusan'] }}">
                        {{ $j['nama_jurusan'] }}
                      </span>
                    </div>
                    <span
                      data-keterisian-count="{{ $j['id_jurusan'] }}"
                      class="text-[11px] font-bold tabular-nums {{ $isFull ? 'text-rose-600' : 'text-slate-600' }}"
                    >
                      {{ $j['diterima'] }} / {{ $j['kuota'] }}
                    </span>
                  </div>
                  <div class="bg-slate-100 rounded-full h-1.5 overflow-hidden">
                    <div
                      data-keterisian-bar="{{ $j['id_jurusan'] }}"
                      class="h-full rounded-full transition-all duration-500"
                      style="width: {{ $percent }}%; background-color: {{ $j['color'] }}"
                    ></div>
                  </div>
                </div>

                <span
                  data-keterisian-badge="{{ $j['id_jurusan'] }}"
                  class="text-[10px] font-bold px-2 py-0.5 rounded-md shrink-0 tabular-nums border {{ $isFull ? 'bg-rose-50 text-rose-700 border-rose-200' : '' }}"
                  @if (!$isFull)
                    style="color: {{ $j['color'] }}; background-color: {{ $j['color'] }}15; border-color: {{ $j['color'] }}30"
                  @endif
                >
                  {{ $percent }}%
                </span>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@php
  $berandaInitialState = [
      'totalMenunggu' => (int) ($totalMenunggu ?? 0),
      'jurusans' => $orderedJurusanStats->map(function ($j) {
          return [
              'id' => (int) ($j['id_jurusan'] ?? 0),
              'kode' => (string) ($j['kode'] ?? ''),
              'nama' => (string) ($j['nama_jurusan'] ?? ''),
              'namaSub' => (string) ($j['nama_sub'] ?? ''),
              'color' => (string) ($j['color'] ?? ''),
              'order' => (int) ($j['order'] ?? 0),
              'kuota' => (int) ($j['kuota'] ?? 0),
              'terisi' => (int) ($j['terisi'] ?? 0),
              'diterima' => (int) ($j['diterima'] ?? 0),
              'menunggu' => (int) ($j['menunggu'] ?? 0),
          ];
      })->values()->all(),
  ];
@endphp

<!-- ============================================================== -->
<!-- SCRIPT REAKTIF REAL-TIME STATE & SINKRONISASI BERANDA          -->
<!-- ============================================================== -->
<script id="admin-beranda-state" type="application/json">
  {!! json_encode($berandaInitialState) !!}
</script>
<script>
  // 1. Single Source of Truth: State Object Terpusat
  window.spmbBerandaState = (() => {
    try {
      const raw = document.getElementById('admin-beranda-state')?.textContent;
      return raw ? JSON.parse(raw) : { totalMenunggu: 0, jurusans: [] };
    } catch (e) {
      console.error('Gagal memuat spmbBerandaState:', e);
      return { totalMenunggu: 0, jurusans: [] };
    }
  })();

  // 2. Fungsi Render Reaktif Komprehensif
  window.renderBerandaStats = function() {
    if (!window.spmbBerandaState || !Array.isArray(window.spmbBerandaState.jurusans)) return;

    const state = window.spmbBerandaState;
    const jurusans = state.jurusans;

    // Hitung Metrik Akumulatif
    const totalKuota = jurusans.reduce((sum, j) => sum + (Number(j.kuota) || 0), 0);
    const totalDiterima = jurusans.reduce((sum, j) => sum + (Number(j.diterima) || 0), 0);
    const totalMenunggu = Number(state.totalMenunggu) || 0;
    const sisaKuota = Math.max(0, totalKuota - totalDiterima);
    const persenTotal = totalKuota > 0 ? Math.min(100, Math.round((totalDiterima / totalKuota) * 100)) : 0;
    const totalPendaftar = jurusans.reduce((sum, j) => sum + (Number(j.terisi) || 0), 0);

    // 2.1 Update Teks Tengah Donut Chart
    const centerTotalEl = document.getElementById('donut-center-total');
    const centerLabelEl = document.getElementById('donut-center-label');
    const centerSubEl = document.getElementById('donut-center-sub');
    if (centerTotalEl && !centerTotalEl.dataset.isHovered) {
      centerTotalEl.textContent = totalPendaftar;
      if (centerLabelEl) centerLabelEl.textContent = 'TOTAL PENDAFTAR';
      if (centerSubEl) centerSubEl.textContent = `${jurusans.length} Jurusan`;
    }

    // 2.2 Update Segmen Donut Chart SVG (Radius 75 -> Keliling 471.24)
    const circumference = 471.24;
    let accumulatedOffset = 0;
    const bgRing = document.getElementById('donut-bg-ring');
    if (bgRing) {
      bgRing.setAttribute('stroke', totalPendaftar > 0 ? '#F1F5F9' : '#E2E8F0');
    }

    jurusans.forEach((j) => {
      const circle = document.getElementById(`donut-segment-${j.id}`);
      const terisi = Number(j.terisi) || 0;
      const sliceRatio = totalPendaftar > 0 ? (terisi / totalPendaftar) : 0;
      const sliceLength = sliceRatio * circumference;
      const dashLength = sliceRatio > 0 ? Math.max(0, sliceLength - 2) : 0;
      const gapLength = Math.max(0, circumference - dashLength);
      const offset = -accumulatedOffset;
      accumulatedOffset += sliceLength;

      if (circle) {
        circle.setAttribute('stroke-dasharray', `${dashLength.toFixed(2)} ${gapLength.toFixed(2)}`);
        circle.setAttribute('stroke-dashoffset', offset.toFixed(2));
      }

      // 2.3 Update Grid Legend Kiri
      const legendPctEl = document.querySelector(`[data-legend-pct="${j.id}"]`);
      const legendKuotaEl = document.querySelector(`[data-legend-kuota="${j.id}"]`);
      const legendPendaftarEl = document.querySelector(`[data-legend-pendaftar="${j.id}"]`);
      const percentAlloc = totalPendaftar > 0 ? Number(((terisi / totalPendaftar) * 100).toFixed(1)) : 0;
      if (legendPctEl) legendPctEl.textContent = `${percentAlloc}%`;
      if (legendKuotaEl) legendKuotaEl.textContent = j.kuota;
      if (legendPendaftarEl) legendPendaftarEl.textContent = terisi;

      // 2.4 Update Daftar Keterisian Per Jurusan di Kartu Kanan
      const keterisianCountEl = document.querySelector(`[data-keterisian-count="${j.id}"]`);
      const keterisianBarEl = document.querySelector(`[data-keterisian-bar="${j.id}"]`);
      const keterisianBadgeEl = document.querySelector(`[data-keterisian-badge="${j.id}"]`);
      const isFull = j.diterima >= j.kuota;
      const percentDiterima = j.kuota > 0 ? Math.min(100, Math.round((j.diterima / j.kuota) * 100)) : 0;

      if (keterisianCountEl) {
        keterisianCountEl.textContent = `${j.diterima} / ${j.kuota}`;
        keterisianCountEl.className = `text-[11px] font-bold tabular-nums ${isFull ? 'text-rose-600' : 'text-slate-600'}`;
      }
      if (keterisianBarEl) {
        keterisianBarEl.style.width = `${percentDiterima}%`;
      }
      if (keterisianBadgeEl) {
        keterisianBadgeEl.textContent = `${percentDiterima}%`;
        if (isFull) {
          keterisianBadgeEl.className = 'text-[10px] font-bold px-2 py-0.5 rounded-md shrink-0 tabular-nums border bg-rose-50 text-rose-700 border-rose-200';
          keterisianBadgeEl.style.backgroundColor = '';
          keterisianBadgeEl.style.borderColor = '';
          keterisianBadgeEl.style.color = '';
        } else {
          keterisianBadgeEl.className = 'text-[10px] font-bold px-2 py-0.5 rounded-md shrink-0 tabular-nums border';
          keterisianBadgeEl.style.color = j.color;
          keterisianBadgeEl.style.backgroundColor = `${j.color}15`;
          keterisianBadgeEl.style.borderColor = `${j.color}30`;
        }
      }
    });

    // 2.5 Update Kotak Insight Otomatis di Paling Bawah Berdasarkan Banyak Pendaftar
    const maxPendaftar = Math.max(...jurusans.map(j => Number(j.terisi) || 0));
    const topJurusans = jurusans.filter(j => (Number(j.terisi) || 0) === maxPendaftar);
    const insightTextEl = document.getElementById('donut-insight-text');
    if (insightTextEl) {
      if (totalPendaftar === 0 || maxPendaftar === 0) {
        insightTextEl.innerHTML = `Saat ini belum ada data pendaftar pada kelima jurusan.`;
      } else if (topJurusans.length === 1) {
        const top = topJurusans[0];
        const pctTop = totalPendaftar > 0 ? Number(((top.terisi / totalPendaftar) * 100).toFixed(1)) : 0;
        insightTextEl.innerHTML = `Jurusan paling diminati saat ini adalah <strong>${top.kode}</strong> dengan <strong>${top.terisi} pendaftar</strong> (${pctTop}% dari total pendaftar).`;
      } else if (topJurusans.length < jurusans.length) {
        const codes = topJurusans.map(j => j.kode).join(' dan ');
        const pctTop = totalPendaftar > 0 ? Number(((maxPendaftar / totalPendaftar) * 100).toFixed(1)) : 0;
        insightTextEl.innerHTML = `Jurusan paling diminati saat ini adalah <strong>${codes}</strong> dengan masing-masing <strong>${maxPendaftar} pendaftar</strong> (${pctTop}% dari total pendaftar).`;
      } else {
        insightTextEl.innerHTML = `Seluruh 5 jurusan memiliki jumlah peminat yang sama, yaitu masing-masing <strong>${maxPendaftar} pendaftar</strong>.`;
      }
    }

    // 2.6 Update 4 Mini Badges di Kartu Kanan
    const statTotalKuotaEl = document.getElementById('stat-total-kuota');
    const statTotalDiterimaEl = document.getElementById('stat-total-diterima');
    const statTotalMenungguEl = document.getElementById('stat-total-menunggu');
    const statSisaKuotaEl = document.getElementById('stat-sisa-kuota');

    if (statTotalKuotaEl) statTotalKuotaEl.textContent = totalKuota;
    if (statTotalDiterimaEl) statTotalDiterimaEl.textContent = totalDiterima;
    if (statTotalMenungguEl) statTotalMenungguEl.textContent = totalMenunggu;
    if (statSisaKuotaEl) statSisaKuotaEl.textContent = sisaKuota;

    // 2.7 Update Bar Keterisian Akumulatif
    const statPersenTotalEl = document.getElementById('stat-persen-total');
    const statBarTotalEl = document.getElementById('stat-bar-total');
    const statDiterimaSubEl = document.getElementById('stat-diterima-sub');
    const statKapasitasSubEl = document.getElementById('stat-kapasitas-sub');

    if (statPersenTotalEl) statPersenTotalEl.textContent = `${persenTotal}% Terpenuhi`;
    if (statBarTotalEl) statBarTotalEl.style.width = `${persenTotal}%`;
    if (statDiterimaSubEl) statDiterimaSubEl.textContent = `${totalDiterima} Siswa Diterima`;
    if (statKapasitasSubEl) statKapasitasSubEl.textContent = `Kapasitas: ${totalKuota} Kursi`;
  };

  // 3. Fungsi Reaktif Utama: updateKuotaJurusan
  window.updateKuotaJurusan = function(jurusanIdentifier, newQuota) {
    if (!window.spmbBerandaState || !Array.isArray(window.spmbBerandaState.jurusans)) return;

    if (typeof jurusanIdentifier === 'object' && jurusanIdentifier !== null) {
      if (Array.isArray(jurusanIdentifier)) {
        jurusanIdentifier.forEach(item => {
          const target = window.spmbBerandaState.jurusans.find(j => j.id == item.id || j.kode === item.kode);
          if (target && item.kuota !== undefined) {
            target.kuota = Math.max(1, parseInt(item.kuota, 10) || target.kuota);
          }
        });
      } else {
        Object.entries(jurusanIdentifier).forEach(([key, val]) => {
          const target = window.spmbBerandaState.jurusans.find(j => j.id == key || j.kode === key);
          if (target) {
            target.kuota = Math.max(1, parseInt(val, 10) || target.kuota);
          }
        });
      }
    } else {
      const target = window.spmbBerandaState.jurusans.find(j => j.id == jurusanIdentifier || j.kode === jurusanIdentifier);
      if (target) {
        target.kuota = Math.max(1, parseInt(newQuota, 10) || target.kuota);
      }
    }

    // Picu pembaruan UI instan dengan animasi transisi yang mulus
    window.renderBerandaStats();
  };

  // 4. Interaksi Hover Donut Chart & Custom Legend Grid
  function setupDonutInteractions() {
    const centerTotalEl = document.getElementById('donut-center-total');
    const centerLabelEl = document.getElementById('donut-center-label');
    const centerSubEl = document.getElementById('donut-center-sub');

    function highlightJurusan(jurusanId) {
      const j = window.spmbBerandaState?.jurusans?.find(item => item.id == jurusanId);
      if (!j) return;

      const totalPendaftar = window.spmbBerandaState.jurusans.reduce((s, x) => s + (Number(x.terisi) || 0), 0);
      const terisi = Number(j.terisi) || 0;
      const pct = totalPendaftar > 0 ? Number(((terisi / totalPendaftar) * 100).toFixed(1)) : 0;

      if (centerTotalEl) {
        centerTotalEl.dataset.isHovered = 'true';
        centerTotalEl.textContent = terisi;
        centerTotalEl.style.color = j.color;
      }
      if (centerLabelEl) {
        centerLabelEl.textContent = `${j.kode} (${pct}%)`;
        centerLabelEl.style.color = j.color;
      }
      if (centerSubEl) {
        centerSubEl.textContent = `Pendaftar: ${terisi} · Kuota: ${j.kuota}`;
      }

      // Animasi sorot segmen SVG
      document.querySelectorAll('.donut-segment').forEach(seg => {
        if (seg.dataset.jurusanId == jurusanId) {
          seg.style.strokeWidth = '28px';
          seg.style.opacity = '1';
          seg.style.filter = 'drop-shadow(0 2px 6px rgba(0,0,0,0.15))';
        } else {
          seg.style.strokeWidth = '24px';
          seg.style.opacity = '0.35';
          seg.style.filter = 'none';
        }
      });

      // Animasi sorot item legend
      document.querySelectorAll('[data-legend-item]').forEach(leg => {
        if (leg.dataset.legendItem == jurusanId) {
          leg.classList.add('ring-2', 'ring-blue-400', 'bg-blue-50/50');
        } else {
          leg.classList.remove('ring-2', 'ring-blue-400', 'bg-blue-50/50');
        }
      });
    }

    function resetHighlight() {
      if (centerTotalEl) {
        delete centerTotalEl.dataset.isHovered;
        centerTotalEl.style.color = '';
      }
      if (centerLabelEl) {
        centerLabelEl.style.color = '';
      }
      window.renderBerandaStats();

      document.querySelectorAll('.donut-segment').forEach(seg => {
        seg.style.strokeWidth = '24px';
        seg.style.opacity = '1';
        seg.style.filter = 'none';
      });

      document.querySelectorAll('[data-legend-item]').forEach(leg => {
        leg.classList.remove('ring-2', 'ring-blue-400', 'bg-blue-50/50');
      });
    }

    document.querySelectorAll('.donut-segment').forEach(seg => {
      seg.addEventListener('mouseenter', () => highlightJurusan(seg.dataset.jurusanId));
      seg.addEventListener('mouseleave', resetHighlight);
    });

    document.querySelectorAll('[data-legend-item]').forEach(leg => {
      leg.addEventListener('mouseenter', () => highlightJurusan(leg.dataset.legendItem));
      leg.addEventListener('mouseleave', resetHighlight);
    });
  }

  // Kompatibilitas jika view switcher memanggil initBerandaMap
  window.initBerandaMap = function() {
    if (typeof window.renderBerandaStats === 'function') {
      window.renderBerandaStats();
    }
  };

  document.addEventListener('DOMContentLoaded', function() {
    setupDonutInteractions();
    window.renderBerandaStats();
  });
</script>
