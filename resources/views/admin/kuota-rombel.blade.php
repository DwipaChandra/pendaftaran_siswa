<!-- ============================================================== -->
<!-- 3. HALAMAN KUOTA ROMBEL (Langsung Kartu Tanpa Background Box)   -->
<!-- ============================================================== -->
<section
  id="section-kuota-rombel"
  class="space-y-4 sm:space-y-6 no-print transition-all {{ ($activePage ?? 'status-berkas') !== 'kuota-rombel' ? 'hidden' : '' }}"
>
  <!-- Grid Jurusan Cards Lebih Besar (2 - 3 Kolom Responsif dengan Baris Bawah Ditengahin) -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 sm:gap-6 items-stretch">
    @foreach ($jurusanStats as $j)
      @php
        $isTjkt = explode(' ', $j['nama_jurusan'])[0] === 'TJKT' || $loop->iteration === 4;
      @endphp
      <div
        data-jurusan-card="{{ $j['id_jurusan'] }}"
        data-registered="{{ $j['terisi'] }}"
        data-accepted="{{ $j['diterima'] }}"
        data-quota="{{ $j['kuota'] }}"
        class="bg-white rounded-3xl border border-slate-200/90 p-5 sm:p-6 shadow-sm hover:shadow-md hover:border-blue-200 transition-all flex flex-col justify-between h-full col-span-1 lg:col-span-2 {{ $isTjkt ? 'lg:col-start-2' : '' }}"
      >
        <div class="flex-1 flex flex-col">
          <!-- Header Kartu: Persentase Terisi (Kiri Atas) & Tombol Ubah Kuota (Kanan Atas) -->
          <div class="flex items-center justify-between gap-3 mb-2 shrink-0">
            <span data-jurusan-percent class="inline-flex items-center text-xs font-bold px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200/70 tabular-nums">
              {{ $j['persen_diterima'] }}% Terisi
            </span>
            <button
              type="button"
              data-quota-edit
              title="Ubah kuota maksimal"
              aria-label="Ubah kuota maksimal {{ $j['nama_jurusan'] }}"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:text-blue-700 bg-slate-50 hover:bg-blue-50 border border-slate-200/70 hover:border-blue-200 rounded-xl transition cursor-pointer"
            >
              <img
                src="{{ asset('assets/img/icon/pencil.svg') }}"
                alt=""
                class="w-3.5 h-3.5 object-contain"
              />
              <span>Ubah Kuota</span>
            </button>
          </div>

          <!-- Judul Lengkap Jurusan (Tinggi seragam agar posisi elemen di bawahnya sejajar) -->
          <h3 class="text-base sm:text-lg font-bold text-slate-900 leading-snug mt-1 min-h-[3rem] sm:min-h-[3.5rem] flex items-start shrink-0" title="{{ $j['nama_jurusan'] }}">
            <span class="line-clamp-2">{{ $j['nama_jurusan'] }}</span>
          </h3>

          <!-- Metrik Angka Kuota Utama -->
          <div class="flex items-baseline gap-1.5 mt-3 sm:mt-4">
            <span
              class="text-2xl sm:text-3xl font-black text-slate-900 tabular-nums"
              data-jurusan-count
            >{{ $j['diterima'] }} / {{ $j['kuota'] }}</span>
            <span class="text-xs font-medium text-slate-500">Kursi</span>
          </div>

          <!-- Progress Bar Besar & Rapi -->
          <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden mt-3">
            <div
              data-jurusan-progress
              class="bg-blue-600 h-2.5 rounded-full transition-all duration-500"
              style="width: {{ $j['persen_diterima'] }}%"
            ></div>
          </div>

          <!-- Rincian Pendaftar & Diterima -->
          <div class="mt-3.5 grid grid-cols-2 gap-2 text-xs">
            <div class="flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-slate-400"></span>
              <span class="text-slate-500">Pendaftar:</span>
              <strong
                class="text-slate-800 font-bold"
                data-jurusan-occupancy
              >{{ $j['terisi'] }}</strong>
            </div>
            <div class="flex items-center gap-2 justify-end">
              <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
              <span class="text-slate-500">Diterima:</span>
              <strong class="text-emerald-700 font-bold">{{ $j['diterima'] }}</strong>
            </div>
          </div>
        </div>

        <!-- Form Edit Kuota yang Lebih Luas & Nyaman -->
        <div
          data-quota-editor
          class="hidden mt-4 border-t border-slate-200/80 bg-slate-50/80 -mx-5 -mb-5 p-4 sm:-mx-6 sm:-mb-6 sm:p-5 rounded-b-3xl"
        >
          <form
            action="{{ route('admin.jurusan.kuota.update', ['jurusan' => $j['id_jurusan']]) }}"
            method="POST"
            data-quota-form
            class="space-y-3"
          >
            @csrf
            <div class="flex items-center justify-between">
              <label
                for="jurusan-kuota-{{ $j['id_jurusan'] }}"
                class="text-xs font-bold text-slate-700"
              >
                Atur Kuota Maksimal:
              </label>
              <span
                data-quota-status
                class="text-xs font-semibold text-slate-500"
                aria-live="polite"
              ></span>
            </div>
            <div class="flex items-center gap-2">
              <div class="relative flex-1 min-w-0">
                <input
                  id="jurusan-kuota-{{ $j['id_jurusan'] }}"
                  type="number"
                  name="kuota"
                  min="1"
                  max="2147483647"
                  value="{{ $j['kuota'] }}"
                  required
                  aria-label="Kuota maksimal {{ $j['nama_jurusan'] }}"
                  class="w-full h-10 pl-3.5 pr-12 text-sm font-bold text-slate-900 bg-white border border-slate-300 rounded-xl focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-100 transition [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                />
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400 pointer-events-none">Kursi</span>
              </div>
              <button
                type="submit"
                title="Simpan kuota"
                aria-label="Simpan kuota"
                class="w-10 h-10 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-95 text-white transition flex items-center justify-center shrink-0 disabled:opacity-50 cursor-pointer"
              >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
              </button>
              <button
                type="button"
                data-quota-cancel
                title="Batal"
                aria-label="Batal mengubah kuota"
                class="w-10 h-10 rounded-xl bg-white hover:bg-slate-100 active:scale-95 border border-slate-200 text-slate-500 hover:text-slate-800 transition flex items-center justify-center shrink-0 cursor-pointer"
              >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            <div
              data-quota-warning
              class="p-2.5 rounded-xl border border-amber-200 bg-amber-50 text-xs font-semibold text-amber-800 items-center gap-2 {{ $j['diterima'] < $j['kuota'] ? 'hidden' : 'flex' }}"
              role="alert"
            >
              <img
                src="{{ asset('assets/img/icon/alert-triangle.svg') }}"
                alt=""
                class="w-4 h-4 shrink-0 object-contain"
              />
              <span data-quota-warning-message>Kuota penerimaan penuh ({{ $j['diterima'] }}/{{ $j['kuota'] }})</span>
            </div>
          </form>
        </div>
      </div>
    @endforeach
  </div>
</section>

