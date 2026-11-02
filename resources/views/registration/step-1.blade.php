@extends('layouts.app')

@section('title', 'Tahap 1/4 - SMK TI BALI GLOBAL BADUNG')
@section('html_class', 'h-full bg-slate-50')
@section('body_class', 'min-h-full flex flex-col text-slate-800 antialiased selection:bg-blue-600 selection:text-white')

@section('content')
    <!-- Main Content Container -->
    <main
      class="flex-1 py-4 sm:py-5 px-3.5 sm:px-6 max-w-3xl mx-auto w-full no-print"
    >
      <!-- CARD PENDAFTARAN (HEADER, STEPPER & FORMULIR) -->
      <div
        class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 p-4 sm:py-6 sm:px-7 shadow-xl shadow-slate-200/50"
      >
        @include('registration.partials.stepper', [
          'currentStep' => 1,
          'description' => 'Lengkapi identitas pribadi calon siswa secara lengkap dan valid sesuai dokumen resmi.',
        ])

        <!-- ============================================================ -->
        <!-- STEP 1: IDENTITAS SISWA & KONTAK                             -->
        <!-- ============================================================ -->
        <form
          id="form-step-1"
          action="{{ route('pendaftaran.step1.post') }}"
          method="POST"
          class="mt-2.5 sm:mt-3 pt-2.5 sm:pt-3 border-t border-slate-100 space-y-5"
        >
          <!-- Alert Pesan -->
          @if(isset($errors) && $errors->any())
            <div class="p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium">
              {{ $errors->first() }}
            </div>
          @endif

          <!-- Nama Lengkap -->
          <div>
            <label class="block text-sm font-semibold mb-1.5">
              Nama Lengkap Calon Siswa <span class="text-red-500">*</span>
            </label>
            <input
              type="text"
              name="nama_lengkap"
              id="input-name"
              required
              value="{{ old('nama_lengkap', $calonSiswa->nama_lengkap ?? '') }}"
              placeholder="Contoh: I Putu Rizky Pratama"
              class="w-full text-sm sm:text-base rounded-xl border border-slate-200 px-3.5 py-2.5 sm:py-3 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition bg-white"
            />
          </div>

          <div>
            <label for="input-nisn" class="block text-sm font-semibold mb-1.5">
              NISN <span class="text-red-500">*</span>
            </label>
            <input
              type="text"
              name="nisn"
              id="input-nisn"
              required
              inputmode="numeric"
              maxlength="10"
              value="{{ old('nisn', $nisn) }}"
              placeholder="Masukkan 10 digit NISN"
              @class([
                'w-full text-sm sm:text-base rounded-xl border px-3.5 py-2.5 sm:py-3 placeholder-slate-400 focus:outline-none transition bg-white',
                'border-red-500 focus:border-red-500 focus:ring-red-100' => $errors->has('nisn'),
                'border-slate-200 focus:border-blue-600 focus:ring-2 focus:ring-blue-100' => ! $errors->has('nisn'),
              ])
            />
            <!-- Teks kecil di bawah formulir NISN -->
            <div id="error-nisn" class="mt-1.5 text-xs sm:text-sm font-medium">
              <span id="nisn-message" class="text-red-500">{{ $errors->first('nisn') ?: 'NISN harus terdiri dari 10 digit angka.' }}</span>
              <span id="nisn-text-complete" class="hidden text-emerald-600 font-semibold items-center gap-1">
                <img
                  src="{{ asset('assets/img/icon/check-green.svg') }}"
                  alt="Centang"
                  class="w-4 h-4 object-contain shrink-0"
                />
                <span>NISN sudah 10 digit angka</span>
              </span>
            </div>
          </div>

          <!-- Tempat & Tanggal Lahir -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold mb-1.5">
                Tempat Lahir <span class="text-red-500">*</span>
              </label>
              <input
                type="text"
                name="tempat_lahir"
                id="input-pob"
                required
                value="{{ old('tempat_lahir', $calonSiswa->tempat_lahir ?? '') }}"
                placeholder="Contoh: Badung"
                class="w-full text-sm sm:text-base rounded-xl border border-slate-200 px-3.5 py-2.5 sm:py-3 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition bg-white"
              />
            </div>
            <div>
              <label
                for="input-dob"
                class="block text-sm font-semibold mb-1.5 cursor-pointer select-none"
              >
                Tanggal Lahir <span class="text-red-500">*</span>
              </label>
              <input
                type="date"
                name="tanggal_lahir"
                id="input-dob"
                required
                min="1950-01-01"
                max="{{ date('Y-m-d') }}"
                value="{{ old('tanggal_lahir', $calonSiswa->tanggal_lahir ? date('Y-m-d', strtotime($calonSiswa->tanggal_lahir)) : '') }}"
                class="w-full text-sm sm:text-base rounded-xl border border-slate-200 px-3.5 py-2.5 sm:py-3 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition bg-white cursor-pointer"
              />
            </div>
          </div>

          <!-- Jenis Kelamin & Agama -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold mb-1.5">
                Jenis Kelamin <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <select
                  name="jenis_kelamin"
                  id="input-jk"
                  required
                  class="w-full text-sm sm:text-base rounded-xl border border-slate-200 pl-3.5 pr-10 py-2.5 sm:py-3 bg-white hover:border-slate-300 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition appearance-none cursor-pointer"
                >
                  <option value="" disabled @selected(old('jenis_kelamin', $calonSiswa->jenis_kelamin ?? '') == '')>
                    Pilih jenis kelamin
                  </option>
                  <option value="Laki-laki" @selected(old('jenis_kelamin', $calonSiswa->jenis_kelamin ?? '') == 'Laki-laki')>Laki-laki</option>
                  <option value="Perempuan" @selected(old('jenis_kelamin', $calonSiswa->jenis_kelamin ?? '') == 'Perempuan')>Perempuan</option>
                </select>
                <img
                  src="{{ asset('assets/img/icon/chevron-down.svg') }}"
                  alt=""
                  class="w-4 h-4 object-contain absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
                />
              </div>
            </div>

            <div>
              <label class="block text-sm font-semibold mb-1.5">
                Agama <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <select
                  name="agama"
                  id="input-agama"
                  required
                  class="w-full text-sm sm:text-base rounded-xl border border-slate-200 pl-3.5 pr-10 py-2.5 sm:py-3 bg-white hover:border-slate-300 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition appearance-none cursor-pointer"
                >
                  <option value="" disabled @selected(old('agama', $calonSiswa->agama ?? '') == '')>Pilih agama</option>
                  @foreach(['Hindu', 'Islam', 'Kristen', 'Katolik', 'Buddha', 'Konghucu'] as $agm)
                    <option value="{{ $agm }}" @selected(old('agama', $calonSiswa->agama ?? '') == $agm)>{{ $agm }}</option>
                  @endforeach
                </select>
                <img
                  src="{{ asset('assets/img/icon/chevron-down.svg') }}"
                  alt=""
                  class="w-4 h-4 object-contain absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
                />
              </div>
            </div>
          </div>

          <!-- Asal Sekolah SMP & No. WhatsApp Siswa -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold mb-1.5">
                Asal Sekolah (SMP / MTs) <span class="text-red-500">*</span>
              </label>
              <input
                type="text"
                name="asal_sekolah"
                id="input-school"
                required
                value="{{ old('asal_sekolah', $calonSiswa->asal_sekolah ?? '') }}"
                placeholder="Contoh: SMP Negeri 1 Kuta Utara"
                class="w-full text-sm sm:text-base rounded-xl border border-slate-200 px-3.5 py-2.5 sm:py-3 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition bg-white"
              />
            </div>
            <div>
              <label class="block text-sm font-semibold mb-1.5">
                Nomor WhatsApp Aktif Siswa <span class="text-red-500">*</span>
              </label>
              <input
                type="tel"
                name="no_whatsapp"
                id="input-whatsapp"
                data-phone-input
                required
                inputmode="tel"
                pattern="[+]?[0-9]+"
                maxlength="20"
                value="{{ old('no_whatsapp', $calonSiswa->no_whatsapp ?? '') }}"
                placeholder="Contoh: 081234567890"
                title="Masukkan hanya angka, dengan tanda + opsional di awal."
                class="w-full text-sm sm:text-base rounded-xl border border-slate-200 px-3.5 py-2.5 sm:py-3 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition tabular-nums bg-white"
              />
            </div>
          </div>

          <!-- Alamat Lengkap Siswa -->
          <div>
            <label class="block text-sm font-semibold mb-1.5">
              Alamat Lengkap Domisili Siswa <span class="text-red-500">*</span>
            </label>
            <textarea
              name="alamat_domisili"
              id="input-address"
              required
              rows="3"
              placeholder="Jalan, Gang, No. Rumah, Banjar/Dusun, Desa/Kelurahan, Kecamatan, Kabupaten"
              class="w-full text-sm sm:text-base rounded-xl border border-slate-200 p-3.5 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition bg-white leading-relaxed"
            >{{ old('alamat_domisili', $calonSiswa->alamat_domisili ?? '') }}</textarea>
          </div>

          <!-- Action Button -->
          <div class="pt-3 sm:pt-4 border-t border-slate-100 flex justify-end">
            <button
              type="submit"
              class="px-5 sm:px-6 py-2.5 sm:py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm sm:text-base rounded-xl shadow-md shadow-blue-600/20 hover:shadow-lg hover:shadow-blue-600/25 transition-all flex items-center gap-2 cursor-pointer"
            >
              <span>Simpan &amp; Lanjut Langkah 2</span>
              <img
                src="{{ asset('assets/img/icon/arrow-right-white.svg') }}"
                alt=""
                class="w-4 h-4 object-contain shrink-0"
              />
            </button>
          </div>

          @csrf
        </form>
      </div>
    </main>

    @include('partials.footer-panitia')
@endsection

@push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const nisnInput = document.getElementById('input-nisn');
        const nisnComplete = document.getElementById('nisn-text-complete');
        const nisnMessage = document.getElementById('nisn-message');
        const form = document.getElementById('form-step-1');

        if (nisnInput) {
          const regexHanyaAngka = /^[0-9]+$/;
          const regexTepat10Angka = /^[0-9]{10}$/;

          function setInputStyle(state) {
            nisnInput.classList.remove(
              'border-red-500', 'focus:border-red-500', 'focus:ring-red-100',
              'border-emerald-500', 'focus:border-emerald-500', 'focus:ring-emerald-100',
              'border-slate-200', 'focus:border-blue-600', 'focus:ring-blue-100'
            );

            if (state === 'valid') {
              nisnInput.classList.add('border-emerald-500', 'focus:border-emerald-500', 'focus:ring-emerald-100');
            } else if (state === 'invalid') {
              nisnInput.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-100');
            } else {
              nisnInput.classList.add('border-slate-200', 'focus:border-blue-600', 'focus:ring-blue-100');
            }
          }

          function updateValidation(isSubmitting = false) {
            const rawVal = nisnInput.value;
            const val = rawVal.trim();
            const len = val.length;

            // 1. Jika mengandung selain angka (huruf / simbol khusus)
            if (len > 0 && !regexHanyaAngka.test(val)) {
              if (nisnComplete) nisnComplete.classList.replace('flex', 'hidden');
              if (nisnMessage) {
                nisnMessage.classList.remove('hidden');
                nisnMessage.textContent = 'NISN hanya boleh berupa angka (tidak boleh huruf atau simbol).';
              }
              setInputStyle('invalid');
              return false;
            }

            // 2. Pas 10 digit angka -> Tampilkan centang hijau & teks hijau (Boleh lanjut)
            if (regexTepat10Angka.test(val)) {
              if (nisnMessage) nisnMessage.classList.add('hidden');
              if (nisnComplete) nisnComplete.classList.replace('hidden', 'flex');
              setInputStyle('valid');
              return true;
            }

            // 3. Belum 10 angka -> Teks merah penanda di bawah formulir
            if (nisnComplete) nisnComplete.classList.replace('flex', 'hidden');
            if (nisnMessage) {
              nisnMessage.classList.remove('hidden');
              nisnMessage.textContent = len > 0
                ? 'NISN harus terdiri dari 10 digit angka (' + len + '/10).'
                : 'NISN harus terdiri dari 10 digit angka.';
            }

            if (isSubmitting) {
              setInputStyle('invalid');
            } else {
              setInputStyle('normal');
            }

            return false;
          }

          nisnInput.addEventListener('input', function () {
            updateValidation(false);
          });

          nisnInput.addEventListener('blur', function () {
            const val = nisnInput.value.trim();
            if (val.length > 0 && val.length < 10) {
              setInputStyle('invalid');
            }
          });

          if (form) {
            form.addEventListener('submit', function (e) {
              const isValid = updateValidation(true);
              if (!isValid) {
                e.preventDefault();
                nisnInput.focus();
              }
            });
          }

          // Cek otomatis saat pertama kali halaman dimuat (jika ada nilai pre-filled)
          updateValidation(false);
        }
      });
    </script>
@endpush
