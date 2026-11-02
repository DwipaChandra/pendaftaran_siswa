@extends('layouts.app')

@section('title', 'Tahap 2/4 - SMK TI BALI GLOBAL BADUNG')
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
          'currentStep' => 2,
          'description' => 'Lengkapi informasi orang tua/wali serta tentukan pilihan jalur dan kompetensi keahlian.',
        ])

        <!-- ============================================================ -->
        <!-- STEP 2: DATA ORANG TUA & PILIHAN JURUSAN                     -->
        <!-- ============================================================ -->
        <form
          id="form-step-2"
          action="{{ route('pendaftaran.step2.post') }}"
          method="POST"
          class="mt-2.5 sm:mt-3 pt-2.5 sm:pt-3 border-t border-slate-100 space-y-5"
        >
          <!-- Alert Notifikasi -->
          @if(isset($errors) && $errors->any())
            <div class="p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium">
              {{ $errors->first() }}
            </div>
          @endif

          @php
            $daftarPekerjaan = [
              'PNS / ASN',
              'TNI / Polri',
              'Karyawan Swasta',
              'Wiraswasta',
              'Petani / Nelayan',
              'Tidak Bekerja',
              'Lainnya',
            ];
            $parentFields = [
                [
                    'name_id' => 'nama-ayah',
                    'name_field' => 'nama_bapak',
                    'name_label' => 'Nama Ayah Kandung / Wali',
                    'name_placeholder' => 'Nama lengkap ayah',
                    'name_value' => old('nama_bapak', $calonSiswa->nama_bapak ?? ''),
                    'error_id' => 'error-nama-ayah',
                    'job_id' => 'pekerjaan-ayah',
                    'job_field' => 'pekerjaan_bapak',
                    'job_label' => 'Pekerjaan Ayah',
                    'job_placeholder' => 'Pilih Pekerjaan Ayah',
                    'job_selected' => old('pekerjaan_bapak', $calonSiswa->pekerjaan_bapak ?? ''),
                ],
                [
                    'name_id' => 'nama-ibu',
                    'name_field' => 'nama_ibu',
                    'name_label' => 'Nama Ibu Kandung',
                    'name_placeholder' => 'Nama lengkap ibu',
                    'name_value' => old('nama_ibu', $calonSiswa->nama_ibu ?? ''),
                    'error_id' => 'error-nama-ibu',
                    'job_id' => 'pekerjaan-ibu',
                    'job_field' => 'pekerjaan_ibu',
                    'job_label' => 'Pekerjaan Ibu',
                    'job_placeholder' => 'Pilih Pekerjaan Ibu',
                    'job_selected' => old('pekerjaan_ibu', $calonSiswa->pekerjaan_ibu ?? ''),
                ],
            ];
            $parentWaFields = [
                [
                    'id' => 'input-parent-wa-father',
                    'name' => 'no_wa_ayah',
                    'label' => 'No. WhatsApp Ayah',
                    'value' => old('no_wa_ayah', $calonSiswa->no_wa_ayah ?? ''),
                ],
                [
                    'id' => 'input-parent-wa-mother',
                    'name' => 'no_wa_ibu',
                    'label' => 'No. WhatsApp Ibu',
                    'value' => old('no_wa_ibu', $calonSiswa->no_wa_ibu ?? ''),
                ],
            ];
          @endphp

          <!-- Nama & Pekerjaan Orang Tua -->
          @foreach ($parentFields as $parent)
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="{{ $parent['name_id'] }}" class="block text-sm font-semibold mb-1.5">
                  {{ $parent['name_label'] }} <span class="text-red-500">*</span>
                </label>
                <input
                  type="text"
                  name="{{ $parent['name_field'] }}"
                  id="{{ $parent['name_id'] }}"
                  required
                  value="{{ $parent['name_value'] }}"
                  placeholder="{{ $parent['name_placeholder'] }}"
                  class="w-full text-sm sm:text-base rounded-xl border border-slate-200 px-3.5 py-2.5 sm:py-3 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition bg-white"
                />
                <p id="{{ $parent['error_id'] }}" class="hidden text-xs text-red-500 mt-1 font-medium">
                  Nama tidak boleh mengandung angka atau simbol khusus.
                </p>
              </div>
              <div>
                <label for="{{ $parent['job_id'] }}" class="block text-sm font-semibold mb-1.5">
                  {{ $parent['job_label'] }} <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <select
                    name="{{ $parent['job_field'] }}"
                    id="{{ $parent['job_id'] }}"
                    required
                    class="w-full text-sm sm:text-base rounded-xl border border-slate-200 pl-3.5 pr-10 py-2.5 sm:py-3 bg-white hover:border-slate-300 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition appearance-none cursor-pointer"
                  >
                    <option value="" disabled @selected(empty($parent['job_selected']))>{{ $parent['job_placeholder'] }}</option>
                    @foreach($daftarPekerjaan as $pekerjaan)
                      <option value="{{ $pekerjaan }}" @selected($parent['job_selected'] === $pekerjaan)>
                        {{ $pekerjaan }}
                      </option>
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
          @endforeach

          <!-- Kontak Orang Tua -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach ($parentWaFields as $waField)
              <div>
                <label for="{{ $waField['id'] }}" class="block text-sm font-semibold mb-1.5">
                  {{ $waField['label'] }} <span class="text-red-500">*</span>
                </label>
                <input
                  type="tel"
                  name="{{ $waField['name'] }}"
                  id="{{ $waField['id'] }}"
                  data-phone-input
                  required
                  inputmode="tel"
                  pattern="[+]?[0-9]+"
                  maxlength="20"
                  value="{{ $waField['value'] }}"
                  placeholder="Contoh: 08XXXXXXXXXX"
                  title="Masukkan hanya angka, dengan tanda + opsional di awal."
                  class="w-full text-sm sm:text-base rounded-xl border border-slate-200 px-3.5 py-2.5 sm:py-3 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition tabular-nums bg-white"
                />
              </div>
            @endforeach
          </div>

          <!-- Alamat Orang Tua -->
          <div>
            <label class="block text-sm font-semibold mb-1.5">
              Alamat Orang Tua / Wali <span class="text-red-500">*</span>
            </label>
            <textarea
              name="alamat_ortu"
              id="input-parent-address"
              required
              rows="3"
              placeholder="Alamat domisili lengkap orang tua/wali"
              class="w-full text-sm sm:text-base rounded-xl border border-slate-200 p-3.5 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition bg-white leading-relaxed"
            >{{ old('alamat_ortu', $calonSiswa->alamat_ortu ?? '') }}</textarea>
          </div>

          <!-- Pilihan Jalur Pendaftaran -->
          <div>
            <label class="block text-sm font-semibold mb-1.5">
              Pilihan Jalur Pendaftaran <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <select
                name="id_jalur"
                id="input-jalur"
                required
                class="w-full text-sm sm:text-base rounded-xl border border-slate-200 pl-3.5 pr-10 py-2.5 sm:py-3 bg-white hover:border-slate-300 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition appearance-none cursor-pointer"
              >
                <option value="" disabled selected>Pilih jalur pendaftaran</option>
                @foreach($jalurList as $jl)
                  @php
                    $isPreselected = isset($preselectedJalur) && (
                      str_contains(strtolower($jl->nama_jalur), strtolower($preselectedJalur)) ||
                      str_contains(strtolower(str_replace([' ', '_', '-'], '', $jl->nama_jalur)), strtolower(str_replace([' ', '_', '-'], '', $preselectedJalur)))
                    );
                    $isSelected = old('id_jalur', $draft['id_jalur'] ?? '') == $jl->id_jalur || $isPreselected;
                  @endphp
                  <option value="{{ $jl->id_jalur }}" @selected($isSelected) data-nama="{{ $jl->nama_jalur }}">
                    {{ $jl->nama_jalur }}
                  </option>
                @endforeach
              </select>
              <img
                src="{{ asset('assets/img/icon/chevron-down.svg') }}"
                alt=""
                class="w-4 h-4 object-contain absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
              />
            </div>
            <!-- Info Kuota / Ketentuan Khusus Jalur -->
            <div id="jalur-info-badge" class="hidden mt-2 p-2.5 rounded-xl bg-blue-50 border border-blue-200 text-xs text-blue-800"></div>
          </div>

          <!-- Sub-Kategori Jalur (Prestasi & Khusus) -->
          <div id="container-sub-jalur" class="hidden">
            <label id="label-sub-jalur" class="block text-sm font-semibold mb-1.5">
              Kategori Spesifik Jalur <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <select
                name="sub_jalur"
                id="input-sub-jalur"
                class="w-full text-sm sm:text-base rounded-xl border border-slate-200 pl-3.5 pr-10 py-2.5 sm:py-3 bg-white hover:border-slate-300 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition appearance-none cursor-pointer"
              >
                <option value="" disabled selected>Pilih kategori spesifik</option>
              </select>
              <img
                src="{{ asset('assets/img/icon/chevron-down.svg') }}"
                alt=""
                class="w-4 h-4 object-contain absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
              />
            </div>
            <input type="hidden" name="nama_sub_jalur" id="input-nama-sub-jalur" value="{{ old('nama_sub_jalur', $draft['nama_sub_jalur'] ?? '') }}" />

            <!-- Opsi Waktu MOU jika memilih MOU -->
            <div id="container-mou-timing" class="hidden mt-2 p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs">
              <span class="font-bold block mb-1">Ketentuan Waktu Rekomendasi MOU:</span>
              <div class="flex items-center gap-4">
                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                  <input type="radio" name="mou_tepat_waktu" value="1" @checked(old('mou_tepat_waktu', $draft['mou_tepat_waktu'] ?? true)) class="text-blue-600 focus:ring-blue-500" />
                  <span class="text-slate-700">Tepat Waktu (Diskon SPP 30%)</span>
                </label>
                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                  <input type="radio" name="mou_tepat_waktu" value="0" @checked(!old('mou_tepat_waktu', $draft['mou_tepat_waktu'] ?? true)) class="text-blue-600 focus:ring-blue-500" />
                  <span class="text-slate-700">Lewat Periode (Diskon SPP 10%)</span>
                </label>
              </div>
            </div>
          </div>

          <!-- Voucher Keringanan (Opsional) -->
          <div>
            <label class="block text-sm font-semibold mb-1.5">
              Voucher Keringanan Biaya Pendaftaran <span class="text-slate-400 font-normal text-xs">(Opsional)</span>
            </label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div class="relative">
                <select
                  name="voucher_jenis"
                  id="input-voucher-jenis"
                  class="w-full text-sm sm:text-base rounded-xl border border-slate-200 pl-3.5 pr-10 py-2.5 sm:py-3 bg-white hover:border-slate-300 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition appearance-none cursor-pointer"
                >
                  <option value="">Tidak Menggunakan Voucher</option>
                  <option value="rekomendasi" @selected(old('voucher_jenis', $draft['voucher_jenis'] ?? '') === 'rekomendasi')>
                    Voucher Rekomendasi Siswa Aktif / Alumni (Gratis Pendaftaran 100%)
                  </option>
                  <option value="hut" @selected(old('voucher_jenis', $draft['voucher_jenis'] ?? '') === 'hut')>
                    Voucher HUT SMK TI Bali Global Badung (Gratis Pendaftaran 100%)
                  </option>
                </select>
                <img
                  src="{{ asset('assets/img/icon/chevron-down.svg') }}"
                  alt=""
                  class="w-4 h-4 object-contain absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
                />
              </div>
              <div>
                <input
                  type="text"
                  name="voucher_kode"
                  id="input-voucher-kode"
                  placeholder="Kode Voucher / Nama Siswa Perekomendasi"
                  value="{{ old('voucher_kode', $draft['voucher_kode'] ?? '') }}"
                  class="w-full text-sm sm:text-base rounded-xl border border-slate-200 px-3.5 py-2.5 sm:py-3 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition bg-white"
                />
              </div>
            </div>
          </div>

          <!-- Pilihan Jurusan Utama -->
          <div>
            <label class="block text-sm font-semibold mb-1.5">
              Pilih Kompetensi Keahlian (Jurusan) <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <select
                name="id_jurusan"
                id="input-major"
                required
                class="w-full text-sm sm:text-base rounded-xl border border-slate-200 pl-3.5 pr-10 py-2.5 sm:py-3 bg-white hover:border-slate-300 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition appearance-none cursor-pointer"
              >
                <option value="" disabled selected>Pilih jurusan yang diminati</option>
                @foreach($jurusanList as $jur)
                  <option value="{{ $jur->id_jurusan }}" @selected(old('id_jurusan', $draft['id_jurusan'] ?? '') == $jur->id_jurusan)>
                    {{ $jur->nama_jurusan }}
                  </option>
                @endforeach
              </select>
              <img
                src="{{ asset('assets/img/icon/chevron-down.svg') }}"
                alt=""
                class="w-4 h-4 object-contain absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
              />
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 leading-tight">
              Seleksi penerimaan dilakukan berdasarkan hasil tes wawancara minat dan bakat.
            </p>
          </div>

          <!-- Navigation Buttons -->
          <div class="pt-3 sm:pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
            <a
              href="{{ route('pendaftaran.step1') }}"
              class="px-4 py-2.5 sm:px-5 sm:py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm sm:text-base rounded-xl transition flex items-center gap-1.5 cursor-pointer"
            >
              <img
                src="{{ asset('assets/img/icon/arrow-left.svg') }}"
                alt=""
                class="w-4 h-4 object-contain shrink-0"
              />
              <span>Kembali</span>
            </a>
            <button
              type="submit"
              class="px-5 py-2.5 sm:px-6 sm:py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm sm:text-base rounded-xl shadow-md shadow-blue-600/20 hover:shadow-lg hover:shadow-blue-600/25 transition flex items-center gap-2 cursor-pointer"
            >
              <span>Simpan &amp; Lanjut Langkah 3</span>
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
        const regexNama = /^[a-zA-Z\s.,'-]+$/;

        function setupNameValidation(inputId, errorId) {
          const inputEl = document.getElementById(inputId);
          const errorEl = document.getElementById(errorId);
          if (!inputEl || !errorEl) {
            return function () {
              return true;
            };
          }

          const validate = function () {
            const val = inputEl.value;

            // Jika input mengandung angka atau karakter di luar Regex
            if (val.length > 0 && !regexNama.test(val)) {
              inputEl.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-100');
              inputEl.classList.remove('border-slate-200', 'focus:border-blue-600', 'focus:ring-blue-100');
              errorEl.classList.remove('hidden');
              return false;
            } else {
              inputEl.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-100');
              inputEl.classList.add('border-slate-200', 'focus:border-blue-600', 'focus:ring-blue-100');
              errorEl.classList.add('hidden');
              return true;
            }
          };

          inputEl.addEventListener('input', validate);
          return validate;
        }

        const validateAyah = setupNameValidation('nama-ayah', 'error-nama-ayah');
        const validateIbu = setupNameValidation('nama-ibu', 'error-nama-ibu');

        // Sub-Jalur & Quota Interactive Logic
        const jalurSelect = document.getElementById('input-jalur');
        const containerSubJalur = document.getElementById('container-sub-jalur');
        const inputSubJalur = document.getElementById('input-sub-jalur');
        const inputNamaSubJalur = document.getElementById('input-nama-sub-jalur');
        const jalurInfoBadge = document.getElementById('jalur-info-badge');
        const containerMouTiming = document.getElementById('container-mou-timing');

        const SUB_JALUR_OPTIONS = {
          prestasi: [
            { id: 'prestasi_sekolah_kab', text: 'Juara 1, 2, 3 Kelas / Tingkat Desa / Kabupaten (SPP 15% 1 Thn)' },
            { id: 'prestasi_prov_nas', text: 'Tingkat Provinsi atau Nasional (SPP 20% 3 Thn)' },
            { id: 'prestasi_internasional', text: 'Tingkat Internasional (SPP 25% 3 Thn)' }
          ],
          khusus: [
            { id: 'mou', text: 'Warga Desa / SMP / MTs MOU Mitra Sekolah' },
            { id: 'saudara', text: 'Saudara Kembar / Saudara Kandung Bersekolah di SMK TI' },
            { id: 'anak_guru', text: 'Anak Kandung Guru/Staf SMK TI Bali Global & STIKOM Grup (SPP 70%)' },
            { id: 'kerabat_guru', text: 'Kerabat Guru/Staf SMK TI Bali Global & STIKOM Grup (SPP 15%)' },
            { id: 'panti', text: 'Panti Asuhan MOU Mitra Sekolah (SPP 25%)' },
            { id: 'ojol', text: 'Putra / Putri Pengemudi Ojek Online (SPP 20% 3 Thn, DPP 50% 1 Thn)' },
            { id: 'minat_bakat', text: 'Jalur Minat & Bakat Seni / Olahraga (SPP 15% 3 Thn, DPP 50% 1 Thn)' },
            { id: 'inklusi', text: 'Jalur Inklusi (Berkebutuhan Khusus / Kebijakan Kepala Sekolah)' }
          ]
        };

        const currentSavedSub = "{{ old('sub_jalur', $draft['sub_jalur'] ?? '') }}";

        function syncJalurOptions() {
          if (!jalurSelect) return;
          const selectedOption = jalurSelect.options[jalurSelect.selectedIndex];
          if (!selectedOption || !selectedOption.value) {
            if (containerSubJalur) containerSubJalur.classList.add('hidden');
            if (jalurInfoBadge) jalurInfoBadge.classList.add('hidden');
            return;
          }

          const namaJalur = (selectedOption.getAttribute('data-nama') || selectedOption.textContent).toLowerCase();

          // Quota & Notes
          if (namaJalur.includes('afirmasi')) {
            if (jalurInfoBadge) {
              jalurInfoBadge.innerHTML = '<strong>Kuota Terbatas: 25 Calon Siswa</strong>. Potongan SPP 15% (3 Tahun) & DPP 50% (3 Tahun) berdasarkan verifikasi SKTM & persetujuan Kepala Sekolah.';
              jalurInfoBadge.classList.remove('hidden');
            }
          } else if (namaJalur.includes('kip')) {
            if (jalurInfoBadge) {
              jalurInfoBadge.innerHTML = '<strong>Kuota Terbatas: 50 Calon Siswa</strong>. Potongan SPP 15% (3 Tahun) & DPP 50% (3 Tahun) dengan melampirkan kartu KIP/KPS/KKS/PKH aktif.';
              jalurInfoBadge.classList.remove('hidden');
            }
          } else {
            if (jalurInfoBadge) {
              jalurInfoBadge.classList.add('hidden');
              jalurInfoBadge.innerHTML = '';
            }
          }

          // Sub Jalur
          let options = [];
          if (namaJalur.includes('prestasi')) {
            options = SUB_JALUR_OPTIONS.prestasi;
          } else if (namaJalur.includes('khusus')) {
            options = SUB_JALUR_OPTIONS.khusus;
          }

          if (options.length > 0 && containerSubJalur && inputSubJalur) {
            inputSubJalur.innerHTML = '<option value="" disabled selected>Pilih kategori spesifik</option>';
            options.forEach(opt => {
              const el = document.createElement('option');
              el.value = opt.id;
              el.textContent = opt.text;
              if (currentSavedSub === opt.id) {
                el.selected = true;
              }
              inputSubJalur.appendChild(el);
            });
            containerSubJalur.classList.remove('hidden');
            syncSubJalurChoice();
          } else if (containerSubJalur && inputSubJalur) {
            containerSubJalur.classList.add('hidden');
            inputSubJalur.innerHTML = '';
            if (inputNamaSubJalur) inputNamaSubJalur.value = '';
            if (containerMouTiming) containerMouTiming.classList.add('hidden');
          }
        }

        function syncSubJalurChoice() {
          if (!inputSubJalur) return;
          const val = inputSubJalur.value;
          const selectedText = inputSubJalur.options[inputSubJalur.selectedIndex]?.textContent || '';
          if (inputNamaSubJalur) inputNamaSubJalur.value = selectedText;

          if (val === 'mou' && containerMouTiming) {
            containerMouTiming.classList.remove('hidden');
          } else if (containerMouTiming) {
            containerMouTiming.classList.add('hidden');
          }
        }

        if (jalurSelect) jalurSelect.addEventListener('change', syncJalurOptions);
        if (inputSubJalur) inputSubJalur.addEventListener('change', syncSubJalurChoice);
        syncJalurOptions();

        const form = document.getElementById('form-step-2');
        if (form) {
          form.addEventListener('submit', function (e) {
            const isAyahValid = validateAyah();
            const isIbuValid = validateIbu();

            if (!isAyahValid) {
              e.preventDefault();
              document.getElementById('nama-ayah')?.focus();
            } else if (!isIbuValid) {
              e.preventDefault();
              document.getElementById('nama-ibu')?.focus();
            }
          });
        }
      });
    </script>
@endpush
