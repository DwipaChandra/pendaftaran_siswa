@props([
    'currentStep' => 1,
    'description' => '',
])

@php
  $stepDefs = [
      1 => ['label' => 'Identitas Siswa', 'activeIcon' => 'user-white.svg', 'inactiveIcon' => 'user.svg', 'alt' => 'Identitas Siswa'],
      2 => ['label' => 'Orang Tua & Pilihan', 'activeIcon' => 'users-white.svg', 'inactiveIcon' => 'users.svg', 'alt' => 'Orang Tua'],
      3 => ['label' => 'Unggah Berkas', 'activeIcon' => 'upload-white.svg', 'inactiveIcon' => 'upload.svg', 'alt' => 'Unggah Berkas'],
      4 => ['label' => 'Verifikasi Data', 'activeIcon' => 'check-circle-white.svg', 'inactiveIcon' => 'check-circle.svg', 'alt' => 'Verifikasi Data'],
  ];

  $progressWidth = match((int) $currentStep) {
      2 => '33.33%',
      3 => '66.66%',
      4 => '100%',
      default => '0%',
  };
@endphp

<!-- Top Action & Badge -->
<div class="flex items-center justify-between gap-3 mb-2.5 pb-2.5 border-b border-slate-100">
  <a
    href="{{ route('home') }}"
    class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-600 hover:text-blue-600 transition"
  >
    <img
      src="{{ asset('assets/img/icon/arrow-left.svg') }}"
      alt="Kembali"
      class="w-4 h-4 object-contain shrink-0"
    />
    <span>Kembali ke Beranda</span>
  </a>

  <span
    class="text-xs sm:text-sm text-blue-700 font-bold px-3 py-1 bg-blue-50 border border-blue-200 rounded-lg sm:rounded-xl uppercase tracking-wider"
  >
    TAHAP {{ $currentStep }} DARI 4
  </span>
</div>

<!-- Institutional Text -->
<div class="mb-2.5">
  <span
    class="text-xs sm:text-[13px] font-bold text-blue-600 tracking-wider uppercase block"
  >
    SMK TI BALI GLOBAL BADUNG
  </span>
  <h1
    class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1 leading-tight"
  >
    Sistem Penerimaan Murid Baru
  </h1>
  <p
    class="text-xs sm:text-sm text-slate-500 mt-1 leading-relaxed"
  >
    {{ $description }}
  </p>
</div>

<!-- 4-STEP HORIZONTAL STEPPER -->
<div
  id="stepper-horizontal-container"
  class="relative pt-2.5 pb-0.5 border-t border-slate-100"
>
  <!-- Connecting Line & Active Progress -->
  <div
    class="absolute top-[26px] sm:top-[27px] left-[10%] right-[10%] h-[2px] bg-slate-200 z-0"
  >
    <div
      id="stepper-progress-bar"
      class="h-full bg-blue-600 transition-all duration-300"
      style="width: {{ $progressWidth }}"
    ></div>
  </div>

  <!-- 4 Step Items -->
  <div class="grid grid-cols-4 relative z-10 text-center">
    @foreach ($stepDefs as $number => $step)
      @php
        $isCompleted = $number < $currentStep;
        $isActive = $number === (int) $currentStep;
        $tag = $isCompleted ? 'a' : 'div';
        $icon = $isCompleted ? 'check-white.svg' : ($isActive ? $step['activeIcon'] : $step['inactiveIcon']);
        $alt = $isCompleted ? 'Selesai' : $step['alt'];
        $circleClass = $isCompleted
            ? 'bg-blue-600 text-white flex items-center justify-center transition-all group-hover:scale-105'
            : ($isActive
                ? 'bg-slate-900 text-white flex items-center justify-center transition-all shadow-md shadow-slate-900/25 scale-105'
                : 'bg-slate-100 border border-slate-200 text-slate-400 flex items-center justify-center transition-all');
        $labelClass = $isCompleted
            ? 'font-semibold text-blue-700 mt-1.5 leading-tight group-hover:underline'
            : ($isActive
                ? 'font-bold text-slate-900 mt-1.5 leading-tight'
                : 'font-semibold text-slate-400 mt-1.5 leading-tight');
      @endphp
      <{{ $tag }}
        id="stepper-tab-{{ $number }}"
        @if ($isCompleted) href="{{ route('pendaftaran.step' . $number) }}" @endif
        class="flex flex-col items-center{{ $isCompleted ? ' group cursor-pointer' : ($isActive ? '' : ' cursor-not-allowed opacity-60') }}"
      >
        <div
          id="stepper-circle-{{ $number }}"
          class="w-[34px] h-[34px] sm:w-9 sm:h-9 rounded-full {{ $circleClass }}"
        >
          <img
            src="{{ asset('assets/img/icon/' . $icon) }}"
            alt="{{ $alt }}"
            class="w-3.5 h-3.5 object-contain shrink-0"
          />
        </div>
        <span
          id="stepper-label-{{ $number }}"
          class="text-xs sm:text-sm {{ $labelClass }}"
        >
          {{ $step['label'] }}
        </span>
      </{{ $tag }}>
    @endforeach
  </div>
</div>

