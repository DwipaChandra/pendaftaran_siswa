<?php

namespace App\Http\Controllers;

use App\Models\BerkasPersyaratan;
use App\Models\CalonSiswa;
use App\Models\GelombangPendaftaran;
use App\Models\JalurPendaftaran;
use App\Models\Jurusan;
use App\Models\Pendaftaran;
use App\Services\KeuanganService;
use App\Services\SyaratBerkasService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PendaftaranController extends Controller
{
    protected function getCalonSiswa(): CalonSiswa
    {
        $user = Auth::user();

        return CalonSiswa::firstOrCreate(
            ['id_user' => $user->id_user],
            [
                'nama_lengkap' => $user->email,
                'no_whatsapp' => '',
            ]
        );
    }

    protected function ensureRegistrationIsOpen(): ?RedirectResponse
    {
        if (Cache::get('ppdb_active_wave') === 'Tutup Gelombang') {
            return redirect()->route('home')->with('error', 'Pendaftaran PPDB saat ini sedang ditutup.');
        }

        return null;
    }

    // Step 1: Identitas Calon Peserta Didik
    public function step1()
    {
        $calonSiswa = $this->getCalonSiswa();

        // Jika sudah mendaftar dan verifikasi final, arahkan ke kartu
        if ($calonSiswa->latestPendaftaran) {
            return redirect()->route('pendaftaran.card');
        }

        if ($closed = $this->ensureRegistrationIsOpen()) {
            return $closed;
        }

        $emailPrefix = explode('@', $calonSiswa->user?->email ?? '', 2)[0];
        $nisn = $calonSiswa->nisn ?: (preg_match('/^\d{10}$/', $emailPrefix) ? $emailPrefix : '');

        return view('registration.step-1', compact('calonSiswa', 'nisn'));
    }

    public function postStep1(Request $request)
    {
        $calonSiswa = $this->getCalonSiswa();

        if ($calonSiswa->latestPendaftaran) {
            return redirect()->route('pendaftaran.card');
        }

        if ($closed = $this->ensureRegistrationIsOpen()) {
            return $closed;
        }

        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'nisn' => ['required', 'digits:10', Rule::unique('calon_siswa', 'nisn')->ignore($calonSiswa->id_siswa, 'id_siswa')],
            'tempat_lahir' => ['required', 'string', 'max:50'],
            'tanggal_lahir' => ['required', 'date_format:Y-m-d', 'before_or_equal:today', 'after:1900-01-01'],
            'jenis_kelamin' => ['required', 'string', 'max:20'],
            'agama' => ['required', 'string', 'max:30'],
            'asal_sekolah' => ['required', 'string', 'max:100'],
            'alamat_domisili' => ['required', 'string'],
            'no_whatsapp' => ['required', 'string', 'regex:/^\+?[0-9]+$/', 'max:20'],
        ], [
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.digits' => 'NISN harus terdiri dari 10 digit angka.',
            'nisn.unique' => 'NISN ini sudah terdaftar di sistem.',
            'tanggal_lahir.date_format' => 'Format tanggal lahir harus YYYY-MM-DD.',
            'tanggal_lahir.before_or_equal' => 'Tanggal lahir tidak boleh melebihi tanggal hari ini.',
            'tanggal_lahir.after' => 'Tanggal lahir tidak valid.',
        ]);

        $calonSiswa->update($validated);

        return redirect()->route('pendaftaran.step2')->with('success', 'Data identitas berhasil disimpan.');
    }

    // Step 2: Data Orang Tua & Pilihan Jurusan
    public function step2(Request $request)
    {
        $calonSiswa = $this->getCalonSiswa();

        if ($calonSiswa->latestPendaftaran) {
            return redirect()->route('pendaftaran.card');
        }

        if ($closed = $this->ensureRegistrationIsOpen()) {
            return $closed;
        }

        $jurusanList = Jurusan::all();
        $jalurList = JalurPendaftaran::all();
        $draft = session('pendaftaran_draft', []);

        // Support preselected query params from landing page (e.g. ?jalur=prestasi)
        $preselectedJalur = $request->query('jalur');

        return view('registration.step-2', compact('calonSiswa', 'jurusanList', 'jalurList', 'draft', 'preselectedJalur'));
    }

    public function postStep2(Request $request)
    {
        $calonSiswa = $this->getCalonSiswa();

        if ($calonSiswa->latestPendaftaran) {
            return redirect()->route('pendaftaran.card');
        }

        if ($closed = $this->ensureRegistrationIsOpen()) {
            return $closed;
        }

        if ($request->filled('nama_ortu') && ! $request->filled('nama_bapak')) {
            $request->merge(['nama_bapak' => $request->input('nama_ortu')]);
        }
        if ($request->filled('pekerjaan_ortu') && ! $request->filled('pekerjaan_bapak')) {
            $request->merge(['pekerjaan_bapak' => $request->input('pekerjaan_ortu')]);
        }
        if (! $request->filled('nama_ibu')) {
            $request->merge(['nama_ibu' => $request->input('nama_bapak') ?: '-']);
        }
        if (! $request->filled('pekerjaan_ibu')) {
            $request->merge(['pekerjaan_ibu' => $request->input('pekerjaan_bapak') ?: '-']);
        }

        $validated = $request->validate([
            'nama_ortu' => ['nullable', 'string', 'max:100', 'regex:/^[a-zA-Z\s.,\'-]+$/'],
            'pekerjaan_ortu' => ['nullable', 'string', 'max:50'],
            'nama_bapak' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s.,\'-]+$/'],
            'pekerjaan_bapak' => ['required', 'string', 'max:50'],
            'nama_ibu' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s.,\'-]+$/'],
            'pekerjaan_ibu' => ['required', 'string', 'max:50'],
            'no_wa_ayah' => ['required', 'string', 'regex:/^\+?[0-9]+$/', 'max:20'],
            'no_wa_ibu' => ['required', 'string', 'regex:/^\+?[0-9]+$/', 'max:20'],
            'alamat_ortu' => ['required', 'string'],
            'id_jurusan' => ['required', 'exists:jurusan,id_jurusan'],
            'id_jalur' => ['required', 'exists:jalur_pendaftaran,id_jalur'],
            'sub_jalur' => ['nullable', 'string', 'max:100'],
            'nama_sub_jalur' => ['nullable', 'string', 'max:255'],
            'voucher_jenis' => ['nullable', 'string', 'max:50'],
            'voucher_kode' => ['nullable', 'string', 'max:100'],
            'mou_tepat_waktu' => ['nullable'],
        ], [
            'nama_ortu.regex' => 'Nama tidak boleh mengandung angka atau simbol khusus.',
            'nama_bapak.regex' => 'Nama tidak boleh mengandung angka atau simbol khusus.',
            'nama_ibu.regex' => 'Nama tidak boleh mengandung angka atau simbol khusus.',
        ]);

        // Kuota Check (Poin PDF: Jalur Afirmasi maks 25 pendaftar, Jalur KIP maks 50 pendaftar)
        $selectedJalur = JalurPendaftaran::find($validated['id_jalur']);
        if ($selectedJalur) {
            $jName = strtolower($selectedJalur->nama_jalur);
            if (str_contains($jName, 'afirmasi')) {
                $countAfirmasi = Pendaftaran::where('id_jalur', $selectedJalur->id_jalur)
                    ->whereIn('status_berkas', ['Diterima', 'Menunggu Verifikasi'])
                    ->count();
                if ($countAfirmasi >= 25) {
                    return back()->withInput()->withErrors([
                        'id_jalur' => 'Mohon maaf, kuota penerimaan Jalur Afirmasi telah penuh (maksimal 25 pendaftar sesuai ketentuan SPMB). Silakan memilih jalur pendaftaran lain.',
                    ]);
                }
            } elseif (str_contains($jName, 'kip')) {
                $countKip = Pendaftaran::where('id_jalur', $selectedJalur->id_jalur)
                    ->whereIn('status_berkas', ['Diterima', 'Menunggu Verifikasi'])
                    ->count();
                if ($countKip >= 50) {
                    return back()->withInput()->withErrors([
                        'id_jalur' => 'Mohon maaf, kuota penerimaan Jalur KIP telah penuh (maksimal 50 pendaftar sesuai ketentuan SPMB). Silakan memilih jalur pendaftaran lain.',
                    ]);
                }
            }
        }

        // Simpan data ortu ke calon_siswa
        $calonSiswa->update([
            'nama_bapak' => $validated['nama_bapak'],
            'pekerjaan_bapak' => $validated['pekerjaan_bapak'],
            'nama_ibu' => $validated['nama_ibu'],
            'pekerjaan_ibu' => $validated['pekerjaan_ibu'],
            'no_wa_ayah' => $validated['no_wa_ayah'],
            'no_wa_ibu' => $validated['no_wa_ibu'],
            'alamat_ortu' => $validated['alamat_ortu'],
        ]);

        // Gelombang pendaftaran ditentukan berdasarkan kontrol admin atau tanggal
        $activeWaveName = Cache::get('ppdb_active_wave', 'Gelombang Khusus');
        $gelombangAktif = null;

        if ($activeWaveName !== 'Tutup Gelombang') {
            $gelombangAktif = GelombangPendaftaran::where('nama_gelombang', $activeWaveName)->first();
        }

        if (! $gelombangAktif) {
            $today = now()->toDateString();
            $gelombangAktif = GelombangPendaftaran::where('tgl_buka', '<=', $today)
                ->where('tgl_tutup', '>=', $today)
                ->first() ?? GelombangPendaftaran::orderBy('tgl_buka')->first();
        }

        $id_gelombang = $gelombangAktif ? $gelombangAktif->id_gelombang : 1;

        // Simpan pilihan pendaftaran ke session
        session([
            'pendaftaran_draft' => [
                'id_jurusan' => $validated['id_jurusan'],
                'id_jalur' => $validated['id_jalur'],
                'id_gelombang' => $id_gelombang,
                'sub_jalur' => $validated['sub_jalur'] ?? null,
                'nama_sub_jalur' => $validated['nama_sub_jalur'] ?? null,
                'voucher_jenis' => $validated['voucher_jenis'] ?? null,
                'voucher_kode' => $validated['voucher_kode'] ?? null,
                'mou_tepat_waktu' => $request->has('mou_tepat_waktu') ? (bool) $request->input('mou_tepat_waktu') : true,
            ],
        ]);

        return redirect()->route('pendaftaran.step3')->with('success', 'Data orang tua dan pilihan jurusan berhasil disimpan.');
    }

    // Step 3: Unggah Berkas Persyaratan
    public function step3()
    {
        $calonSiswa = $this->getCalonSiswa();

        if ($calonSiswa->latestPendaftaran) {
            return redirect()->route('pendaftaran.card');
        }

        if ($closed = $this->ensureRegistrationIsOpen()) {
            return $closed;
        }

        $draft = session('pendaftaran_draft', []);
        $jalur = isset($draft['id_jalur']) ? JalurPendaftaran::find($draft['id_jalur']) : null;
        $gelombang = isset($draft['id_gelombang']) ? GelombangPendaftaran::find($draft['id_gelombang']) : null;

        $requiredDocs = SyaratBerkasService::getRequiredDocuments(
            jalurNama: $jalur?->nama_jalur,
            subJalur: $draft['sub_jalur'] ?? null,
            gelombangNama: $gelombang?->nama_gelombang,
            voucherJenis: $draft['voucher_jenis'] ?? null
        );

        $berkasDraft = session('berkas_draft', []);

        return view('registration.step-3', compact('calonSiswa', 'berkasDraft', 'requiredDocs', 'draft', 'jalur', 'gelombang'));
    }

    public function postStep3(Request $request)
    {
        $calonSiswa = $this->getCalonSiswa();

        if ($calonSiswa->latestPendaftaran) {
            return redirect()->route('pendaftaran.card');
        }

        if ($closed = $this->ensureRegistrationIsOpen()) {
            return $closed;
        }

        $berkasDraft = session('berkas_draft', []);

        $rules = [
            'berkas_foto' => [isset($berkasDraft['berkas_foto']) ? 'nullable' : 'required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'berkas_kk' => [isset($berkasDraft['berkas_kk']) ? 'nullable' : 'required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:10240'],
            'berkas_akta' => [isset($berkasDraft['berkas_akta']) ? 'nullable' : 'required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:10240'],
        ];

        foreach (SyaratBerkasService::BERKAS_DEFINITIONS as $k => $def) {
            if (! isset($rules[$k])) {
                $rules[$k] = ['nullable', 'file', 'mimes:'.$def['mimes'], 'max:10240'];
            }
        }

        $request->validate($rules, [
            'berkas_foto.required' => 'Pasfoto Formal Siswa wajib diunggah.',
            'berkas_foto.mimes' => 'Format pasfoto harus JPG, JPEG, PNG, atau WEBP.',
            'berkas_foto.max' => 'Ukuran pasfoto maksimal 10 MB.',
            'berkas_kk.required' => 'Kartu Keluarga (KK) wajib diunggah.',
            'berkas_kk.mimes' => 'Format berkas KK harus JPG, JPEG, PNG, WEBP, atau PDF.',
            'berkas_kk.max' => 'Ukuran berkas KK maksimal 10 MB.',
            'berkas_akta.required' => 'Akta Kelahiran Asli wajib diunggah.',
            'berkas_akta.mimes' => 'Format berkas Akta harus JPG, JPEG, PNG, WEBP, atau PDF.',
            'berkas_akta.max' => 'Ukuran berkas Akta maksimal 10 MB.',
        ]);

        foreach (SyaratBerkasService::BERKAS_DEFINITIONS as $inputName => $def) {
            if ($request->hasFile($inputName)) {
                $file = $request->file($inputName);
                $origName = $file->getClientOriginalName();
                $size = $file->getSize();
                $baseName = Str::slug(pathinfo($origName, PATHINFO_FILENAME)) ?: 'berkas';
                $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin');
                $filename = time().'_'.Str::random(6).'_'.$baseName.'.'.$extension;
                $path = $file->storeAs('berkas', $filename, 'public');

                $berkasDraft[$inputName] = [
                    'jenis_berkas' => $def['label'],
                    'nama_file' => $origName,
                    'ukuran_file' => $size,
                    'path_file' => 'storage/'.$path,
                ];
            }
        }

        session(['berkas_draft' => $berkasDraft]);

        return redirect()->route('pendaftaran.step4')->with('success', 'Berkas berhasil diunggah.');
    }

    // Step 4: Verifikasi & Konfirmasi Data
    public function step4()
    {
        $calonSiswa = $this->getCalonSiswa();

        if ($calonSiswa->latestPendaftaran) {
            return redirect()->route('pendaftaran.card');
        }

        if ($closed = $this->ensureRegistrationIsOpen()) {
            return $closed;
        }

        $draft = session('pendaftaran_draft', []);
        $berkasDraft = session('berkas_draft', []);

        $jurusan = isset($draft['id_jurusan']) ? Jurusan::find($draft['id_jurusan']) : null;
        $jalur = isset($draft['id_jalur']) ? JalurPendaftaran::find($draft['id_jalur']) : null;
        $gelombang = isset($draft['id_gelombang']) ? GelombangPendaftaran::find($draft['id_gelombang']) : null;

        return view('registration.step-4', compact('calonSiswa', 'draft', 'jurusan', 'jalur', 'gelombang', 'berkasDraft'));
    }

    // Submit Akhir Pendaftaran
    public function submitRegistration(Request $request)
    {
        $calonSiswa = $this->getCalonSiswa();

        if ($calonSiswa->latestPendaftaran) {
            return redirect()->route('pendaftaran.card')->with('info', 'Anda sudah menyelesaikan pendaftaran.');
        }

        if ($closed = $this->ensureRegistrationIsOpen()) {
            return $closed;
        }

        $draft = session('pendaftaran_draft');
        $berkasDraft = session('berkas_draft', []);

        if (! $draft || ! isset($draft['id_jurusan'])) {
            return redirect()->route('pendaftaran.step2')->withErrors(['error' => 'Silakan pilih jurusan dan jalur pendaftaran terlebih dahulu.']);
        }

        foreach (['berkas_foto', 'berkas_kk', 'berkas_akta'] as $inputName) {
            if (! isset($berkasDraft[$inputName])) {
                return redirect()->route('pendaftaran.step3')->withErrors([
                    $inputName => 'Semua berkas wajib diunggah sebelum pendaftaran dikirim.',
                ]);
            }
        }

        DB::beginTransaction();
        try {
            $lastSimpleNumber = (int) (Pendaftaran::lockForUpdate()
                ->whereRaw("no_registrasi REGEXP '^[0-9]+$'")
                ->selectRaw('MAX(CAST(no_registrasi AS UNSIGNED)) as max_num')
                ->value('max_num') ?? 0);
            $nextNumber = $lastSimpleNumber + 1;
            $noRegistrasi = str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);

            while (Pendaftaran::where('no_registrasi', $noRegistrasi)->exists()) {
                $nextNumber++;
                $noRegistrasi = str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
            }

            $pendaftaran = Pendaftaran::create([
                'no_registrasi' => $noRegistrasi,
                'id_siswa' => $calonSiswa->id_siswa,
                'id_jurusan' => $draft['id_jurusan'],
                'id_jalur' => $draft['id_jalur'],
                'id_gelombang' => $draft['id_gelombang'],
                'sub_jalur' => $draft['sub_jalur'] ?? null,
                'nama_sub_jalur' => $draft['nama_sub_jalur'] ?? null,
                'voucher_jenis' => $draft['voucher_jenis'] ?? null,
                'voucher_kode' => $draft['voucher_kode'] ?? null,
                'mou_tepat_waktu' => $draft['mou_tepat_waktu'] ?? true,
                'tgl_daftar' => now('Asia/Makassar')->toDateString(),
                'status_berkas' => 'Menunggu Verifikasi',
            ]);

            // Simpan berkas yang diunggah
            foreach ($berkasDraft as $b) {
                BerkasPersyaratan::create([
                    'no_registrasi' => $pendaftaran->no_registrasi,
                    'jenis_berkas' => $b['jenis_berkas'],
                    'nama_file' => $b['nama_file'],
                    'ukuran_file' => $b['ukuran_file'],
                    'path_file' => $b['path_file'],
                ]);
            }

            // Inisialisasi dan sinkronisasi rincian keuangan siswa
            app(KeuanganService::class)->syncKeuanganStatus($pendaftaran);

            DB::commit();

            // Bersihkan session draft
            session()->forget(['pendaftaran_draft', 'berkas_draft']);

            return redirect()->route('pendaftaran.card')->with('success', 'Pendaftaran Anda berhasil dikirim! Silakan unduh atau cetak kartu pendaftaran Anda.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Gagal mengirim pendaftaran: '.$e->getMessage()]);
        }
    }

    // Revisi / Unggah Ulang Berkas (Saat Status: Perbaikan Berkas)
    public function reuploadDocuments(Request $request)
    {
        $calonSiswa = $this->getCalonSiswa();
        $pendaftaran = $calonSiswa->latestPendaftaran;

        if (! $pendaftaran) {
            return redirect()->route('pendaftaran.step1')->with('info', 'Anda belum memiliki pendaftaran.');
        }

        if ($pendaftaran->status_berkas !== 'Perbaikan Berkas') {
            return redirect()->route('pendaftaran.card')->with('info', 'Status pendaftaran Anda saat ini tidak memerlukan perbaikan berkas.');
        }

        $rules = [
            'berkas_foto' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'berkas_kk' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:10240'],
            'berkas_akta' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:10240'],
        ];

        foreach (SyaratBerkasService::BERKAS_DEFINITIONS as $k => $def) {
            if (! isset($rules[$k])) {
                $rules[$k] = ['nullable', 'file', 'mimes:'.$def['mimes'], 'max:10240'];
            }
        }

        $request->validate($rules, [
            'berkas_foto.mimes' => 'Format pasfoto harus JPG, JPEG, PNG, atau WEBP.',
            'berkas_foto.max' => 'Ukuran pasfoto maksimal 10 MB.',
            'berkas_kk.mimes' => 'Format berkas KK harus JPG, JPEG, PNG, WEBP, atau PDF.',
            'berkas_kk.max' => 'Ukuran berkas KK maksimal 10 MB.',
            'berkas_akta.mimes' => 'Format berkas Akta harus JPG, JPEG, PNG, WEBP, atau PDF.',
            'berkas_akta.max' => 'Ukuran berkas Akta maksimal 10 MB.',
        ]);

        $uploadMap = [];
        foreach (SyaratBerkasService::BERKAS_DEFINITIONS as $k => $def) {
            $uploadMap[$k] = $def['label'];
        }

        $hasUpload = false;
        foreach ($uploadMap as $inputName => $label) {
            if ($request->hasFile($inputName)) {
                $hasUpload = true;
                break;
            }
        }

        if (! $hasUpload) {
            return back()->withErrors(['error' => 'Pilih minimal satu berkas perbaikan untuk diunggah.']);
        }

        DB::beginTransaction();
        try {
            foreach ($uploadMap as $inputName => $label) {
                if (! $request->hasFile($inputName)) {
                    continue;
                }

                $file = $request->file($inputName);
                $origName = $file->getClientOriginalName();
                $size = $file->getSize();
                $baseName = Str::slug(pathinfo($origName, PATHINFO_FILENAME)) ?: 'berkas';
                $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin');
                $filename = time().'_'.Str::random(6).'_'.$baseName.'.'.$extension;
                $path = $file->storeAs('berkas', $filename, 'public');

                $berkas = $pendaftaran->berkas()->firstOrNew(['jenis_berkas' => $label]);
                if ($berkas->path_file && str_starts_with($berkas->path_file, 'storage/berkas/')) {
                    $oldStoragePath = substr($berkas->path_file, strlen('storage/'));
                    if (Storage::disk('public')->exists($oldStoragePath)) {
                        Storage::disk('public')->delete($oldStoragePath);
                    }
                }

                $berkas->fill([
                    'nama_file' => $origName,
                    'ukuran_file' => $size,
                    'path_file' => 'storage/'.$path,
                ])->save();
            }

            // Kembalikan status pendaftaran ke Menunggu Verifikasi agar ditinjau ulang oleh panitia
            $pendaftaran->update([
                'status_berkas' => 'Menunggu Verifikasi',
            ]);

            DB::commit();

            return redirect()->route('pendaftaran.card')->with('success', 'Berkas perbaikan berhasil diunggah! Status berkas telah diperbarui menjadi Menunggu Verifikasi.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Gagal mengunggah berkas perbaikan: '.$e->getMessage()]);
        }
    }

    // Kartu Tanda Peserta
    public function card()
    {
        $calonSiswa = $this->getCalonSiswa();
        $pendaftaran = Pendaftaran::where('id_siswa', $calonSiswa->id_siswa)
            ->with(['jurusan', 'jalur', 'gelombang', 'berkas', 'verifikasi.panitia'])
            ->latest('tgl_daftar')
            ->first();

        if (! $pendaftaran) {
            return redirect()->route('pendaftaran.step1')->with('info', 'Anda belum menyelesaikan pendaftaran.');
        }

        return view('registration.participant-card', compact('pendaftaran', 'calonSiswa'));
    }
}
