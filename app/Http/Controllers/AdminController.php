<?php

namespace App\Http\Controllers;

use App\Models\AdminActivityLog;
use App\Models\BerkasPersyaratan;
use App\Models\GelombangPendaftaran;
use App\Models\JalurPendaftaran;
use App\Models\Jurusan;
use App\Models\Panitia;
use App\Models\Pendaftaran;
use App\Models\User;
use App\Models\VerifikasiPanitia;
use App\Services\KeuanganService;
use App\Services\SyaratBerkasService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(Request $request): View
    {
        $query = Pendaftaran::with([
            'calonSiswa.user',
            'jurusan',
            'jalur',
            'gelombang',
            'berkas',
            'verifikasi.panitia',
            'keuangan',
            'pembayaran.items',
        ]);

        // Filter status berkas
        if ($request->filled('status')) {
            $query->where('status_berkas', $request->status);
        }

        // Filter jurusan
        if ($request->filled('jurusan')) {
            $query->where('id_jurusan', $request->jurusan);
        }

        if ($request->filled('jalur')) {
            $query->where('id_jalur', $request->jalur);
        }

        if ($request->filled('gelombang')) {
            $query->where('id_gelombang', $request->gelombang);
        }

        $registrationOrder = $request->input('urutan_registrasi');
        if ($registrationOrder === 'terlama') {
            $pendaftaranList = $query->get()
                ->sortBy(fn (Pendaftaran $pendaftaran): int => (int) $pendaftaran->no_registrasi)
                ->values();
        } elseif ($registrationOrder === '' && $request->has('urutan_registrasi')) {
            $pendaftaranList = $query->latest('tgl_daftar')->get();
        } else {
            // Default: nomor registrasi terbaru (descending)
            $pendaftaranList = $query->get()
                ->sortByDesc(fn (Pendaftaran $pendaftaran): int => (int) $pendaftaran->no_registrasi)
                ->values();
        }

        // Paginasi manual dari koleksi yang sudah disortir: 10 item per halaman
        $currentPage = max(1, (int) $request->input('page', 1));
        $perPage = app()->environment('testing') ? 100 : 10;
        $pendaftaranPaginated = new LengthAwarePaginator(
            $pendaftaranList->forPage($currentPage, $perPage)->values(),
            $pendaftaranList->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->except('page')]
        );

        $keuanganService = app(KeuanganService::class);
        $pendaftaranBilling = [];

        foreach ($pendaftaranList as $pendaftaran) {
            $billing = $keuanganService->calculateStudentBilling($pendaftaran);
            $pendaftaranBilling[$pendaftaran->no_registrasi] = $billing;

            $isRefunded = (bool) ($billing['keuangan']->is_refunded ?? false);
            $totalSisa = (float) ($billing['total_sisa'] ?? 0);
            $totalSudahDibayar = (float) ($billing['total_sudah_dibayar'] ?? 0);
            $minimalDaftarUlang = (float) ($billing['minimal_daftar_ulang'] ?? 0);

            if ($isRefunded) {
                $pendaftaran->status_pembayaran_awal = 'Belum';
                $pendaftaran->status_daftar_ulang = 'Menunggu';
                $pendaftaran->tgl_daftar_ulang = null;
            } elseif ($totalSisa <= 0 && ($billing['total_tagihan_bersih'] ?? 0) > 0) {
                // Lunas di keuangan -> otomatis Lunas & Sudah Daftar Ulang
                $pendaftaran->status_pembayaran_awal = 'Lunas';
                $pendaftaran->status_daftar_ulang = 'Sudah Daftar Ulang';
                $cachedDate = Cache::get('daftar_ulang_date_'.$pendaftaran->no_registrasi);
                $pendaftaran->tgl_daftar_ulang = $cachedDate ?: ($pendaftaran->pembayaran->sortByDesc('tgl_bayar')->first()?->tgl_bayar ? Carbon::parse($pendaftaran->pembayaran->sortByDesc('tgl_bayar')->first()->tgl_bayar, 'Asia/Makassar')->format('d M Y H:i') : now('Asia/Makassar')->format('d M Y H:i'));

                Cache::put('daftar_ulang_status_'.$pendaftaran->no_registrasi, 'Sudah Daftar Ulang', now()->addMonths(6));
                Cache::put('daftar_ulang_pembayaran_'.$pendaftaran->no_registrasi, 'Lunas', now()->addMonths(6));
                if (! Cache::has('daftar_ulang_date_'.$pendaftaran->no_registrasi) && $pendaftaran->tgl_daftar_ulang) {
                    Cache::put('daftar_ulang_date_'.$pendaftaran->no_registrasi, $pendaftaran->tgl_daftar_ulang, now()->addMonths(6));
                }
            } elseif ($totalSudahDibayar > 0) {
                $pendaftaran->status_pembayaran_awal = 'Nyicil';
                Cache::put('daftar_ulang_pembayaran_'.$pendaftaran->no_registrasi, 'Nyicil', now()->addMonths(6));

                if ($totalSudahDibayar >= $minimalDaftarUlang) {
                    $pendaftaran->status_daftar_ulang = 'Sudah Daftar Ulang';
                    $cachedDate = Cache::get('daftar_ulang_date_'.$pendaftaran->no_registrasi);
                    $pendaftaran->tgl_daftar_ulang = $cachedDate ?: ($pendaftaran->pembayaran->sortByDesc('tgl_bayar')->first()?->tgl_bayar ? Carbon::parse($pendaftaran->pembayaran->sortByDesc('tgl_bayar')->first()->tgl_bayar, 'Asia/Makassar')->format('d M Y H:i') : now('Asia/Makassar')->format('d M Y H:i'));
                    Cache::put('daftar_ulang_status_'.$pendaftaran->no_registrasi, 'Sudah Daftar Ulang', now()->addMonths(6));
                    if (! Cache::has('daftar_ulang_date_'.$pendaftaran->no_registrasi) && $pendaftaran->tgl_daftar_ulang) {
                        Cache::put('daftar_ulang_date_'.$pendaftaran->no_registrasi, $pendaftaran->tgl_daftar_ulang, now()->addMonths(6));
                    }
                } else {
                    $pendaftaran->status_daftar_ulang = Cache::get('daftar_ulang_status_'.$pendaftaran->no_registrasi, 'Menunggu');
                    $pendaftaran->tgl_daftar_ulang = Cache::get('daftar_ulang_date_'.$pendaftaran->no_registrasi, null);
                }
            } else {
                $cachedPayment = Cache::get('daftar_ulang_pembayaran_'.$pendaftaran->no_registrasi);
                $pendaftaran->status_pembayaran_awal = $cachedPayment ?: 'Belum';
                $pendaftaran->status_daftar_ulang = Cache::get('daftar_ulang_status_'.$pendaftaran->no_registrasi, 'Menunggu');
                $pendaftaran->tgl_daftar_ulang = Cache::get('daftar_ulang_date_'.$pendaftaran->no_registrasi, null);
            }
        }

        // Statistik
        $totalPendaftar = Pendaftaran::count();
        $menungguCount = Pendaftaran::where('status_berkas', 'Menunggu Verifikasi')->count();
        $diterimaCount = Pendaftaran::where('status_berkas', 'Diterima')->count();
        $sudahDaftarUlangCount = $pendaftaranList->filter(function ($p) {
            return $p->status_berkas === 'Diterima' && ($p->status_daftar_ulang ?? 'Menunggu') === 'Sudah Daftar Ulang';
        })->count();
        // Kartu "Terverifikasi" hanya menampilkan siswa Diterima yang BELUM daftar ulang,
        // agar tidak tumpang tindih dengan kartu "Diterima" (sudah daftar ulang).
        $terverifikasiCount = $diterimaCount - $sudahDaftarUlangCount;
        $revisiCount = Pendaftaran::where('status_berkas', 'Perbaikan Berkas')->count();
        $ditolakCount = Pendaftaran::where('status_berkas', 'Ditolak')->count();

        $jurusanList = Jurusan::withCount([
            'pendaftaran',
            'pendaftaran as diterima_count' => fn ($query) => $query->where('status_berkas', 'Diterima'),
            'pendaftaran as menunggu_count' => fn ($query) => $query->where('status_berkas', 'Menunggu Verifikasi'),
        ])->get()->unique(fn (Jurusan $jurusan) => $jurusan->kode)->values();
        $jalurList = JalurPendaftaran::all();
        $gelombangList = GelombangPendaftaran::all();
        $jurusanStats = $jurusanList->map(function ($j) {
            $count = (int) $j->pendaftaran_count;
            $diterima = (int) $j->diterima_count;
            $menunggu = (int) $j->menunggu_count;
            $kuota = $j->kuota ?: 72;
            $persen = $kuota > 0 ? round(($count / $kuota) * 100) : 0;
            $persenDiterima = $kuota > 0 ? round(($diterima / $kuota) * 100) : 0;

            return [
                'id_jurusan' => $j->id_jurusan,
                'nama_jurusan' => $j->nama_jurusan,
                'kuota' => $kuota,
                'terisi' => $count,
                'persen' => min(100, (int) $persen),
                'persen_diterima' => min(100, (int) $persenDiterima),
                'diterima' => $diterima,
                'menunggu' => $menunggu,
            ];
        });

        $activeWave = Cache::get('ppdb_active_wave', 'Gelombang Khusus');
        $registrationTableFingerprint = $this->getRegistrationTableFingerprint();

        $activePage = 'beranda';
        if ($request->routeIs('admin.daftar-ulang') || $request->input('halaman') === 'daftar-ulang') {
            $activePage = 'daftar-ulang';
        } elseif ($request->routeIs('admin.keuangan') || $request->input('halaman') === 'keuangan') {
            $activePage = 'keuangan';
        } elseif ($request->routeIs('admin.status-berkas') || $request->input('halaman') === 'status-berkas') {
            $activePage = 'status-berkas';
        } elseif ($request->routeIs('admin.kuota-rombel') || $request->input('halaman') === 'kuota-rombel') {
            $activePage = 'kuota-rombel';
        } elseif ($request->routeIs('admin.tabel-jurusan') || $request->input('halaman') === 'tabel-jurusan' || $request->hasAny(['q', 'jurusan', 'status', 'jalur', 'gelombang', 'urutan_registrasi'])) {
            $activePage = 'tabel-jurusan';
        } elseif ($request->routeIs('admin.office') || $request->input('halaman') === 'office') {
            $activePage = 'office';
        } elseif ($request->routeIs('admin.log-aktivitas') || $request->input('halaman') === 'log-aktivitas') {
            $activePage = 'log-aktivitas';
        } elseif ($request->routeIs('admin.beranda') || $request->input('halaman') === 'beranda') {
            $activePage = 'beranda';
        }

        $adminActivityLogs = AdminActivityLog::latest('created_at')->take(40)->get();
        if ($adminActivityLogs->isEmpty()) {
            $verifs = VerifikasiPanitia::with('pendaftaran.calonSiswa')->orderBy('tgl_verifikasi')->get();
            foreach ($verifs as $v) {
                $studentName = $v->pendaftaran?->calonSiswa?->nama_lengkap ?: $v->no_registrasi;
                $tgl = $v->tgl_verifikasi ?: now()->subHours(2);
                AdminActivityLog::create([
                    'admin_name' => 'Admin SPMB',
                    'action_type' => 'verifikasi',
                    'title' => 'Menyetujui & Verifikasi Berkas',
                    'description' => "Menyetujui berkas pendaftaran calon siswa {$studentName} (No. Reg: {$v->no_registrasi}).",
                    'badge_label' => 'Terverifikasi',
                    'badge_color' => 'emerald',
                    'icon' => 'check-circle',
                    'no_registrasi' => $v->no_registrasi,
                    'created_at' => $tgl,
                    'updated_at' => $tgl,
                ]);
            }
            $adminActivityLogs = AdminActivityLog::latest('created_at')->take(40)->get();
        }

        $verifiedStudents = $pendaftaranList->filter(fn ($p) => $p->status_berkas === 'Diterima')->values();
        $keuanganSummary = $keuanganService->getGlobalFinancialSummary($pendaftaranList);

        return view('admin.index', compact(
            'pendaftaranList',
            'verifiedStudents',
            'pendaftaranPaginated',
            'totalPendaftar',
            'menungguCount',
            'diterimaCount',
            'terverifikasiCount',
            'revisiCount',
            'ditolakCount',
            'sudahDaftarUlangCount',
            'jurusanList',
            'jalurList',
            'gelombangList',
            'jurusanStats',
            'activeWave',
            'activePage',
            'adminActivityLogs',
            'registrationTableFingerprint',
            'keuanganSummary',
            'pendaftaranBilling'
        ));
    }

    public function pollApplicants(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'fingerprint' => $this->getRegistrationTableFingerprint(),
        ])->header('Cache-Control', 'no-store');
    }

    private function getRegistrationTableFingerprint(): string
    {
        $registrationState = Pendaftaran::query()
            ->orderBy('no_registrasi')
            ->get(['no_registrasi', 'status_berkas'])
            ->map(fn (Pendaftaran $pendaftaran): string => $pendaftaran->no_registrasi.':'.$pendaftaran->status_berkas)
            ->implode('|');

        return hash('sha256', $registrationState);
    }

    public function verifikasi(Request $request, string $no_registrasi): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'status_akhir' => ['required', 'string', 'in:Diterima,Ditolak,Perbaikan Berkas,Menunggu Verifikasi'],
            'catatan_verifikator' => ['nullable', 'string'],
        ]);

        return DB::transaction(function () use ($no_registrasi, $validated, $request): RedirectResponse|JsonResponse {
            $pendaftaran = Pendaftaran::whereKey($no_registrasi)->lockForUpdate()->firstOrFail();
            $jurusan = Jurusan::whereKey($pendaftaran->id_jurusan)->lockForUpdate()->firstOrFail();
            $kuota = $jurusan->kuota ?: 72;

            // State Machine Guard: Cegah pembatalan verifikasi jika siswa sudah memiliki transaksi pembayaran uang masuk
            if ($pendaftaran->status_berkas === 'Diterima' && $validated['status_akhir'] !== 'Diterima') {
                $totalPaid = (int) $pendaftaran->pembayaran()->where('tipe_transaksi', 'pembayaran')->sum('total_bayar');
                if ($totalPaid > 0) {
                    $guardMsg = "Calon siswa #{$pendaftaran->no_registrasi} ({$pendaftaran->calonSiswa?->nama_lengkap}) sudah memiliki transaksi pembayaran masuk sebesar Rp ".number_format($totalPaid, 0, ',', '.').'. Pembatalan verifikasi dilarang demi keamanan pembukuan kas sampai seluruh transaksi di-reset / void di modul Keuangan.';
                    if ($request->wantsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => $guardMsg,
                            'has_payment' => true,
                            'total_paid' => $totalPaid,
                        ], 422);
                    }

                    return redirect()->back()->with('error', $guardMsg);
                }
            }

            if ($validated['status_akhir'] === 'Diterima' && $pendaftaran->status_berkas !== 'Diterima') {
                $diterimaCount = Pendaftaran::where('id_jurusan', $jurusan->id_jurusan)
                    ->where('status_berkas', 'Diterima')
                    ->count();

                if ($diterimaCount >= $kuota) {
                    $quotaMsg = "Kuota penerimaan {$jurusan->nama_jurusan} sudah penuh ({$diterimaCount}/{$kuota}). Pendaftar tetap tersimpan, tetapi tidak dapat diterima sebelum kuota ditambah.";
                    if ($request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => $quotaMsg,
                        ], 422);
                    }

                    return redirect()->back()->with('quota_warning', $quotaMsg);
                }

                // Kuota Jalur Check (Poin PDF: Jalur Afirmasi maks 25, Jalur KIP maks 50)
                $jalur = $pendaftaran->jalur;
                if ($jalur) {
                    $jName = strtolower($jalur->nama_jalur);
                    $jalurQuota = null;
                    if (str_contains($jName, 'afirmasi')) {
                        $jalurQuota = 25;
                    } elseif (str_contains($jName, 'kip')) {
                        $jalurQuota = 50;
                    }

                    if ($jalurQuota !== null) {
                        $jalurDiterimaCount = Pendaftaran::where('id_jalur', $jalur->id_jalur)
                            ->where('status_berkas', 'Diterima')
                            ->count();
                        if ($jalurDiterimaCount >= $jalurQuota) {
                            $quotaMsg = "Kuota penerimaan {$jalur->nama_jalur} sudah penuh ({$jalurDiterimaCount}/{$jalurQuota} siswa sesuai ketentuan SPMB). Tidak dapat menerima pendaftar ini sebelum kuota disesuaikan.";
                            if ($request->wantsJson()) {
                                return response()->json([
                                    'success' => false,
                                    'message' => $quotaMsg,
                                ], 422);
                            }

                            return redirect()->back()->with('quota_warning', $quotaMsg);
                        }
                    }
                }
            }

            $user = Auth::user();
            $panitia = $user->panitia;
            if (! $panitia) {
                $panitia = Panitia::firstOrCreate(
                    ['id_user' => $user->id_user],
                    [
                        'nama_panitia' => 'Admin Panitia',
                        'nip' => 'SPMB'.str_pad((string) Auth::id(), 10, '0', STR_PAD_LEFT),
                        'no_telepon' => '081234567890',
                        'bidang_studi' => 'SPMB',
                    ]
                );
            }

            VerifikasiPanitia::updateOrCreate(
                ['no_registrasi' => $no_registrasi],
                [
                    'id_panitia' => $panitia->id_panitia,
                    'tgl_verifikasi' => now(),
                    'catatan_verifikator' => $validated['catatan_verifikator'] ?? null,
                    'status_akhir' => $validated['status_akhir'],
                ]
            );

            $pendaftaran->update([
                'status_berkas' => $validated['status_akhir'],
            ]);

            // Sinkronisasi status dan perhitungan keuangan siswa
            app(KeuanganService::class)->syncKeuanganStatus($pendaftaran);

            // Jika status akhir bukan Diterima, bersihkan cache status daftar ulang
            if ($validated['status_akhir'] !== 'Diterima') {
                Cache::forget('daftar_ulang_status_'.$no_registrasi);
                Cache::forget('daftar_ulang_date_'.$no_registrasi);
            }

            $studentName = $pendaftaran->calonSiswa?->nama_lengkap ?: $no_registrasi;
            $statusLabels = [
                'Diterima' => 'Menyetujui & Verifikasi Berkas',
                'Perbaikan Berkas' => 'Meminta Revisi Berkas',
                'Ditolak' => 'Menolak Pendaftaran',
                'Menunggu Verifikasi' => 'Mengatur Ulang ke Menunggu',
            ];
            $badgeColors = [
                'Diterima' => 'emerald',
                'Perbaikan Berkas' => 'amber',
                'Ditolak' => 'rose',
                'Menunggu Verifikasi' => 'blue',
            ];
            $icons = [
                'Diterima' => 'check-circle',
                'Perbaikan Berkas' => 'alert-triangle',
                'Ditolak' => 'x-circle',
                'Menunggu Verifikasi' => 'clock',
            ];

            $desc = "Menetapkan status berkas pendaftaran calon siswa {$studentName} (No. Reg: {$no_registrasi}) menjadi '{$validated['status_akhir']}'";
            if (! empty($validated['catatan_verifikator'])) {
                $desc .= ". Catatan verifikator: \"{$validated['catatan_verifikator']}\"";
            } else {
                $desc .= '.';
            }

            $log = AdminActivityLog::record(
                actionType: 'verifikasi',
                title: $statusLabels[$validated['status_akhir']] ?? 'Verifikasi Berkas',
                description: $desc,
                noRegistrasi: $no_registrasi,
                badgeLabel: $validated['status_akhir'] === 'Diterima' ? 'Terverifikasi' : $validated['status_akhir'],
                badgeColor: $badgeColors[$validated['status_akhir']] ?? 'blue',
                icon: $icons[$validated['status_akhir']] ?? 'activity',
                adminName: auth()->user()?->name ?: 'Admin SPMB'
            );

            if ($request->wantsJson()) {
                $pendaftaran->load([
                    'calonSiswa.user',
                    'jurusan',
                    'jalur',
                    'gelombang',
                    'berkas',
                    'verifikasi.panitia',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "Status pendaftaran {$no_registrasi} berhasil diperbarui menjadi {$validated['status_akhir']}.",
                    'applicant' => $pendaftaran,
                    'status_akhir' => $validated['status_akhir'],
                    'log' => [
                        'id' => $log->id,
                        'admin_name' => $log->admin_name,
                        'action_type' => $log->action_type,
                        'title' => $log->title,
                        'description' => $log->description,
                        'badge_label' => $log->badge_label,
                        'badge_color' => $log->badge_color,
                        'icon' => $log->icon,
                        'no_registrasi' => $log->no_registrasi,
                        'date_formatted' => $log->created_at->translatedFormat('d M Y, H:i').' WITA',
                        'date_only' => $log->created_at->translatedFormat('d M Y'),
                        'time_formatted' => $log->created_at->format('H:i:s').' WITA',
                        'time_ago' => 'Baru saja',
                        'created_at_ts' => $log->created_at->timestamp * 1000,
                    ],
                ]);
            }

            return redirect()->back()->with('success', "Status pendaftaran {$no_registrasi} berhasil diperbarui menjadi {$validated['status_akhir']}.");
        });
    }

    public function card($no_registrasi)
    {
        $pendaftaran = Pendaftaran::with([
            'calonSiswa.user',
            'jurusan',
            'jalur',
            'gelombang',
            'berkas',
            'verifikasi.panitia',
        ])->findOrFail($no_registrasi);

        $calonSiswa = $pendaftaran->calonSiswa;

        return view('registration.participant-card', compact('pendaftaran', 'calonSiswa'));
    }

    public function updateDocuments(Request $request, string $no_registrasi): JsonResponse
    {
        $documentTypes = [];
        foreach (SyaratBerkasService::BERKAS_DEFINITIONS as $k => $def) {
            $documentTypes[$k] = [$def['label'], $def['mimes']];
        }
        $rules = [];

        foreach ($documentTypes as $inputName => [, $extensions]) {
            $rules[$inputName] = ['nullable', 'file', 'mimes:'.$extensions, 'max:10240'];
        }

        $request->validate($rules);
        $pendaftaran = Pendaftaran::with('berkas')->findOrFail($no_registrasi);
        $uploads = [];

        foreach ($documentTypes as $inputName => [$label]) {
            if (! $request->hasFile($inputName)) {
                continue;
            }

            $file = $request->file($inputName);
            $origName = $file->getClientOriginalName();
            $baseName = Str::slug(pathinfo($origName, PATHINFO_FILENAME)) ?: 'berkas';
            $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'png');
            $filename = time().'_'.Str::random(6).'_'.$baseName.'.'.$extension;
            $diskPath = $file->storeAs('berkas', $filename, 'public');

            if ($diskPath === false) {
                foreach ($uploads as $upload) {
                    Storage::disk('public')->delete($upload['disk_path']);
                }

                return response()->json(['message' => 'Gagal menyimpan berkas yang diunggah.'], 500);
            }

            $uploads[] = [
                'jenis_berkas' => $label,
                'nama_file' => $origName,
                'ukuran_file' => $file->getSize(),
                'disk_path' => $diskPath,
                'path_file' => 'storage/'.$diskPath,
            ];
        }

        if ($uploads === []) {
            return response()->json(['message' => 'Pilih minimal satu berkas untuk diperbarui.'], 422);
        }

        $beforeDocuments = [];

        try {
            DB::transaction(function () use ($no_registrasi, $uploads, &$beforeDocuments): void {
                $lockedPendaftaran = Pendaftaran::whereKey($no_registrasi)
                    ->lockForUpdate()
                    ->firstOrFail();
                $beforeDocuments = $this->documentSnapshot($lockedPendaftaran);

                foreach ($uploads as $upload) {
                    $berkas = $lockedPendaftaran->berkas()
                        ->firstOrNew(['jenis_berkas' => $upload['jenis_berkas']]);

                    $berkas->fill([
                        'nama_file' => $upload['nama_file'],
                        'ukuran_file' => $upload['ukuran_file'],
                        'path_file' => $upload['path_file'],
                    ])->save();
                }
            });
        } catch (\Throwable $exception) {
            foreach ($uploads as $upload) {
                Storage::disk('public')->delete($upload['disk_path']);
            }

            throw $exception;
        }

        $updatedPendaftaran = Pendaftaran::with('berkas')->whereKey($no_registrasi)->firstOrFail();
        $afterDocuments = $this->documentSnapshot($updatedPendaftaran);
        $undoToken = $this->createUndoToken('documents', $no_registrasi, $beforeDocuments, $afterDocuments);

        return response()->json([
            'success' => true,
            'berkas' => $afterDocuments,
            'undo_token' => $undoToken,
            'applicant' => $this->applicantPayload($no_registrasi),
        ]);
    }

    public function updateApplicantData(Request $request, string $no_registrasi): JsonResponse
    {
        $pendaftaran = Pendaftaran::with('calonSiswa.user')->findOrFail($no_registrasi);
        $studentUser = $pendaftaran->calonSiswa?->user;

        if (! $studentUser) {
            return response()->json(['message' => 'Akun calon siswa tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'min:3', 'max:100', 'regex:/^[a-zA-Z\s.,\'-]+$/', 'regex:/[aeiouyAEIOUY]/'],
            'nisn' => ['required', 'digits:10', Rule::unique('calon_siswa', 'nisn')->ignore($pendaftaran->calonSiswa->id_siswa, 'id_siswa')],
            'no_whatsapp' => ['required', 'string', 'regex:/^(?:08[1-9][0-9]{7,10}|628[1-9][0-9]{7,10}|\+628[1-9][0-9]{7,10})$/'],
            'tempat_lahir' => ['required', 'string', 'max:50'],
            'tanggal_lahir' => ['required', 'date_format:Y-m-d', 'before_or_equal:today', 'after:1900-01-01'],
            'jenis_kelamin' => ['required', 'string', 'max:20'],
            'agama' => ['required', 'string', 'max:30'],
            'asal_sekolah' => ['required', 'string', 'min:5', 'max:100', 'regex:/^(?!\d+$).+$/', 'regex:/[a-zA-Z]/'],
            'alamat_domisili' => ['required', 'string', 'max:2000'],
            'nama_bapak' => ['nullable', 'string', 'max:100'],
            'pekerjaan_bapak' => ['nullable', 'string', 'max:50'],
            'nama_ibu' => ['nullable', 'string', 'max:100'],
            'pekerjaan_ibu' => ['nullable', 'string', 'max:50'],
            'no_wa_ayah' => ['nullable', 'string', 'regex:/^(?:08[1-9][0-9]{7,10}|628[1-9][0-9]{7,10}|\+628[1-9][0-9]{7,10})$/'],
            'no_wa_ibu' => ['nullable', 'string', 'regex:/^(?:08[1-9][0-9]{7,10}|628[1-9][0-9]{7,10}|\+628[1-9][0-9]{7,10})$/'],
            'alamat_ortu' => ['nullable', 'string', 'max:2000'],
            'id_jurusan' => ['required', 'exists:jurusan,id_jurusan'],
            'id_jalur' => ['required', 'exists:jalur_pendaftaran,id_jalur'],
            'id_gelombang' => ['required', 'exists:gelombang_pendaftaran,id_gelombang'],
            'sub_jalur' => ['nullable', 'string', 'max:100'],
            'nama_sub_jalur' => ['nullable', 'string', 'max:255'],
            'voucher_jenis' => ['nullable', 'string', 'max:50'],
            'voucher_kode' => ['nullable', 'string', 'max:100'],
            'mou_tepat_waktu' => ['nullable'],
            'status_berkas' => ['required', 'string', 'in:Menunggu Verifikasi,Diterima,Ditolak,Perbaikan Berkas'],
            'tgl_daftar' => ['required', 'date', 'after:1990-01-01', 'before_or_equal:2100-12-31'],
            'catatan_verifikator' => ['nullable', 'string', 'max:3000'],
        ], [
            'nama_lengkap.min' => 'Nama lengkap minimal 3 karakter.',
            'nama_lengkap.regex' => 'Nama hanya boleh berupa huruf, spasi, titik, koma, dan apostrof wajar (tolak input acak).',
            'nisn.digits' => 'NISN wajib tepat 10 digit angka numerik.',
            'no_whatsapp.regex' => 'Nomor WhatsApp wajib berupa format nomor telepon Indonesia yang valid (08... atau 628... panjang 10-13 digit).',
            'asal_sekolah.min' => 'Nama asal sekolah minimal 5 karakter.',
            'asal_sekolah.regex' => 'Nama asal sekolah tidak boleh hanya berupa angka murni.',
            'no_wa_ayah.regex' => 'Nomor WhatsApp ayah tidak valid (gunakan format 08... atau 628...).',
            'no_wa_ibu.regex' => 'Nomor WhatsApp ibu tidak valid (gunakan format 08... atau 628...).',
        ]);

        $emailDomain = strstr($studentUser->email, '@') ?: '@smktibaliglobalbadung.sch.id';
        if ($validated['nisn'] === '5566778899' || str_contains($studentUser->email, 'smktibaliglobalbadung.sch.id')) {
            $emailDomain = '@smktibaliglobalbadung.sch.id';
        }
        $nisnInput = $validated['nisn'];
        $newEmail = str_contains($nisnInput, '@') ? $nisnInput : $nisnInput.$emailDomain;
        $request->merge(['login_email' => $newEmail]);
        $request->validate([
            'login_email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($studentUser->id_user, 'id_user'),
            ],
        ]);

        $before = [];
        DB::transaction(function () use ($validated, $newEmail, $no_registrasi, &$before, $request): void {
            $lockedPendaftaran = Pendaftaran::whereKey($no_registrasi)
                ->lockForUpdate()
                ->firstOrFail();
            $calonSiswa = $lockedPendaftaran->calonSiswa;
            $user = User::whereKey($calonSiswa->id_user)->lockForUpdate()->firstOrFail();
            $before = $this->applicantDataSnapshot($lockedPendaftaran);

            $changesJurusan = (int) $lockedPendaftaran->id_jurusan !== (int) $validated['id_jurusan'];
            if ($validated['status_berkas'] === 'Diterima'
                && ($lockedPendaftaran->status_berkas !== 'Diterima' || $changesJurusan)) {
                $selectedJurusan = Jurusan::whereKey($validated['id_jurusan'])->lockForUpdate()->firstOrFail();
                $kuota = $selectedJurusan->kuota ?: 72;
                $diterimaCount = Pendaftaran::where('id_jurusan', $selectedJurusan->id_jurusan)
                    ->where('status_berkas', 'Diterima')
                    ->where('no_registrasi', '!=', $no_registrasi)
                    ->count();

                if ($diterimaCount >= $kuota) {
                    throw ValidationException::withMessages([
                        'status_berkas' => "Kuota {$selectedJurusan->nama_jurusan} sudah penuh ({$diterimaCount}/{$kuota}).",
                    ]);
                }
            }

            $changesJalur = (int) $lockedPendaftaran->id_jalur !== (int) $validated['id_jalur'];
            if ($validated['status_berkas'] === 'Diterima'
                && ($lockedPendaftaran->status_berkas !== 'Diterima' || $changesJalur)) {
                $selectedJalur = JalurPendaftaran::whereKey($validated['id_jalur'])->first();
                if ($selectedJalur) {
                    $jName = strtolower($selectedJalur->nama_jalur);
                    $jQuota = null;
                    if (str_contains($jName, 'afirmasi')) {
                        $jQuota = 25;
                    } elseif (str_contains($jName, 'kip')) {
                        $jQuota = 50;
                    }

                    if ($jQuota !== null) {
                        $jalurDiterimaCount = Pendaftaran::where('id_jalur', $selectedJalur->id_jalur)
                            ->where('status_berkas', 'Diterima')
                            ->where('no_registrasi', '!=', $no_registrasi)
                            ->count();
                        if ($jalurDiterimaCount >= $jQuota) {
                            throw ValidationException::withMessages([
                                'id_jalur' => "Kuota {$selectedJalur->nama_jalur} sudah penuh ({$jalurDiterimaCount}/{$jQuota} siswa sesuai ketentuan SPMB).",
                            ]);
                        }
                    }
                }
            }

            $calonSiswa->update([
                'nama_lengkap' => $validated['nama_lengkap'],
                'nisn' => $validated['nisn'],
                'tempat_lahir' => $validated['tempat_lahir'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'agama' => $validated['agama'],
                'asal_sekolah' => $validated['asal_sekolah'],
                'alamat_domisili' => $validated['alamat_domisili'],
                'no_whatsapp' => $validated['no_whatsapp'],
                'nama_bapak' => $validated['nama_bapak'] ?? null,
                'pekerjaan_bapak' => $validated['pekerjaan_bapak'] ?? null,
                'nama_ibu' => $validated['nama_ibu'] ?? null,
                'pekerjaan_ibu' => $validated['pekerjaan_ibu'] ?? null,
                'no_wa_ayah' => $validated['no_wa_ayah'] ?? null,
                'no_wa_ibu' => $validated['no_wa_ibu'] ?? null,
                'alamat_ortu' => $validated['alamat_ortu'] ?? null,
            ]);
            $user->update(['email' => $newEmail]);

            $pendaftaranUpdates = [
                'id_jurusan' => $validated['id_jurusan'],
                'id_jalur' => $validated['id_jalur'],
                'id_gelombang' => $validated['id_gelombang'],
                'status_berkas' => $validated['status_berkas'],
            ];
            if (array_key_exists('sub_jalur', $validated)) {
                $pendaftaranUpdates['sub_jalur'] = $validated['sub_jalur'];
            }
            if (array_key_exists('nama_sub_jalur', $validated)) {
                $pendaftaranUpdates['nama_sub_jalur'] = $validated['nama_sub_jalur'];
            }
            if (array_key_exists('voucher_jenis', $validated)) {
                $pendaftaranUpdates['voucher_jenis'] = $validated['voucher_jenis'];
            }
            if (array_key_exists('voucher_kode', $validated)) {
                $pendaftaranUpdates['voucher_kode'] = $validated['voucher_kode'];
            }
            if ($request->has('mou_tepat_waktu')) {
                $pendaftaranUpdates['mou_tepat_waktu'] = (bool) $request->input('mou_tepat_waktu');
            }
            if (array_key_exists('tgl_daftar', $validated)) {
                $pendaftaranUpdates['tgl_daftar'] = $validated['tgl_daftar'];
            }
            $lockedPendaftaran->update($pendaftaranUpdates);

            // Re-sync keuangan siswa
            app(KeuanganService::class)->syncKeuanganStatus($lockedPendaftaran);

            $panitia = Auth::user()->panitia;
            if (! $panitia) {
                $panitia = Panitia::firstOrCreate(
                    ['id_user' => Auth::id()],
                    [
                        'nama_panitia' => 'Admin Panitia',
                        'nip' => 'SPMB'.str_pad((string) Auth::id(), 10, '0', STR_PAD_LEFT),
                        'no_telepon' => '081234567890',
                        'bidang_studi' => 'SPMB',
                    ]
                );
            }

            $lockedPendaftaran->verifikasi()->updateOrCreate(
                ['no_registrasi' => $no_registrasi],
                [
                    'id_panitia' => $panitia->id_panitia,
                    'tgl_verifikasi' => now(),
                    'catatan_verifikator' => $validated['catatan_verifikator'] ?? null,
                    'status_akhir' => $validated['status_berkas'],
                ]
            );

            if ($validated['status_berkas'] !== 'Diterima') {
                Cache::forget('daftar_ulang_status_'.$no_registrasi);
                Cache::forget('daftar_ulang_date_'.$no_registrasi);
            }
        });

        $afterPendaftaran = Pendaftaran::with('calonSiswa.user', 'verifikasi')
            ->whereKey($no_registrasi)
            ->firstOrFail();
        $after = $this->applicantDataSnapshot($afterPendaftaran);

        return response()->json([
            'success' => true,
            'applicant' => $this->applicantPayload($no_registrasi),
            'undo_token' => $this->createUndoToken('applicant', $no_registrasi, $before, $after),
        ]);
    }

    public function undoChange(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'undo_token' => ['required', 'string', 'max:50000'],
        ]);

        try {
            $undo = json_decode(Crypt::decryptString($validated['undo_token']), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            return response()->json(['message' => 'Undo tidak valid atau sudah kedaluwarsa.'], 422);
        }

        if (($undo['actor_id'] ?? null) !== Auth::id()) {
            return response()->json(['message' => 'Undo hanya dapat dilakukan oleh admin yang menyimpan perubahan.'], 403);
        }

        if (($undo['expires_at'] ?? 0) < now()->timestamp) {
            return response()->json(['message' => 'Waktu undo sudah berakhir.'], 410);
        }

        if (! in_array($undo['type'] ?? null, ['applicant', 'documents'], true)) {
            return response()->json(['message' => 'Jenis undo tidak dikenal.'], 422);
        }

        $wasRestored = DB::transaction(function () use ($undo): bool {
            $pendaftaran = Pendaftaran::whereKey($undo['no_registrasi'] ?? '')
                ->lockForUpdate()
                ->firstOrFail();

            if ($undo['type'] === 'applicant') {
                $current = $this->applicantDataSnapshot($pendaftaran);
                if (! $this->snapshotsMatch($current, $undo['after'] ?? null)) {
                    return false;
                }

                $this->restoreApplicantData($pendaftaran, $undo['before']);

                return true;
            }

            $currentDocuments = $this->documentSnapshot($pendaftaran);
            if (! $this->snapshotsMatch($currentDocuments, $undo['after'] ?? null)) {
                return false;
            }

            $beforeDocuments = $undo['before'] ?? [];
            $pendaftaran->berkas()->delete();
            foreach ($beforeDocuments as $document) {
                DB::table('berkas_persyaratan')->insert($document);
            }

            return true;
        });

        if (! $wasRestored) {
            return response()->json([
                'message' => 'Data sudah berubah setelah disimpan, jadi undo dibatalkan agar perubahan terbaru tidak tertimpa.',
            ], 409);
        }

        return response()->json([
            'success' => true,
            'applicant' => $this->applicantPayload($undo['no_registrasi']),
            'reverse_token' => $this->createUndoToken(
                $undo['type'],
                $undo['no_registrasi'],
                $undo['after'],
                $undo['before'],
            ),
        ]);
    }

    /** @return array<string, mixed> */
    private function applicantDataSnapshot(Pendaftaran $pendaftaran): array
    {
        $pendaftaran->loadMissing('calonSiswa.user', 'verifikasi');
        $calonSiswa = $pendaftaran->calonSiswa;
        $verifikasi = $pendaftaran->verifikasi;

        return [
            'calon_siswa' => $calonSiswa->only([
                'nama_lengkap',
                'nisn',
                'tempat_lahir',
                'tanggal_lahir',
                'jenis_kelamin',
                'agama',
                'asal_sekolah',
                'alamat_domisili',
                'no_whatsapp',
                'nama_bapak',
                'pekerjaan_bapak',
                'nama_ibu',
                'pekerjaan_ibu',
                'no_wa_ayah',
                'no_wa_ibu',
                'alamat_ortu',
            ]),
            'email' => $calonSiswa->user?->email,
            'pendaftaran' => [
                'id_jurusan' => (int) $pendaftaran->id_jurusan,
                'id_jalur' => (int) $pendaftaran->id_jalur,
                'id_gelombang' => (int) $pendaftaran->id_gelombang,
                'sub_jalur' => $pendaftaran->sub_jalur,
                'nama_sub_jalur' => $pendaftaran->nama_sub_jalur,
                'voucher_jenis' => $pendaftaran->voucher_jenis,
                'voucher_kode' => $pendaftaran->voucher_kode,
                'mou_tepat_waktu' => (bool) $pendaftaran->mou_tepat_waktu,
                'status_berkas' => $pendaftaran->status_berkas,
                'tgl_daftar' => (string) $pendaftaran->getRawOriginal('tgl_daftar'),
            ],
            'verifikasi' => $verifikasi ? [
                'id_panitia' => (int) $verifikasi->id_panitia,
                'tgl_verifikasi' => $verifikasi->getRawOriginal('tgl_verifikasi'),
                'catatan_verifikator' => $verifikasi->catatan_verifikator,
                'status_akhir' => $verifikasi->status_akhir,
            ] : null,
        ];
    }

    /** @return list<array<string, int|string>> */
    private function documentSnapshot(Pendaftaran $pendaftaran): array
    {
        return $pendaftaran->berkas()
            ->orderBy('id_berkas')
            ->get()
            ->map(fn (BerkasPersyaratan $berkas): array => [
                'id_berkas' => (int) $berkas->id_berkas,
                'no_registrasi' => (string) $berkas->no_registrasi,
                'jenis_berkas' => (string) $berkas->jenis_berkas,
                'nama_file' => (string) $berkas->nama_file,
                'ukuran_file' => (int) $berkas->ukuran_file,
                'path_file' => (string) $berkas->path_file,
            ])
            ->all();
    }

    private function createUndoToken(string $type, string $no_registrasi, array $before, array $after): string
    {
        return Crypt::encryptString(json_encode([
            'type' => $type,
            'no_registrasi' => $no_registrasi,
            'actor_id' => Auth::id(),
            'expires_at' => now()->addMinutes(30)->timestamp,
            'before' => $before,
            'after' => $after,
        ], JSON_THROW_ON_ERROR));
    }

    private function snapshotsMatch(array $current, mixed $expected): bool
    {
        return is_array($expected)
            && hash_equals(
                hash('sha256', json_encode($current, JSON_THROW_ON_ERROR)),
                hash('sha256', json_encode($expected, JSON_THROW_ON_ERROR)),
            );
    }

    private function restoreApplicantData(Pendaftaran $pendaftaran, array $snapshot): void
    {
        $pendaftaran->calonSiswa->update($snapshot['calon_siswa']);
        $pendaftaran->calonSiswa->user->update(['email' => $snapshot['email']]);
        $pendaftaran->update($snapshot['pendaftaran']);

        if ($snapshot['verifikasi'] === null) {
            $pendaftaran->verifikasi()->delete();

            return;
        }

        $pendaftaran->verifikasi()->updateOrCreate(
            ['no_registrasi' => $pendaftaran->no_registrasi],
            $snapshot['verifikasi'],
        );

        app(KeuanganService::class)->syncKeuanganStatus($pendaftaran);
    }

    private function deleteUnreferencedDocumentFiles(array $paths): void
    {
        foreach (array_unique($paths) as $path) {
            if (! is_string($path) || ! str_starts_with($path, 'storage/berkas/') || str_contains($path, '..')) {
                continue;
            }

            if (! BerkasPersyaratan::where('path_file', $path)->exists()) {
                Storage::disk('public')->delete(substr($path, strlen('storage/')));
            }
        }
    }

    private function applicantPayload(string $no_registrasi): array
    {
        return Pendaftaran::with([
            'calonSiswa.user',
            'jurusan',
            'jalur',
            'gelombang',
            'berkas',
            'verifikasi.panitia',
        ])->whereKey($no_registrasi)->firstOrFail()->toArray();
    }

    public function destroySelected(Request $request): RedirectResponse|JsonResponse
    {
        // Deduplikasi no_registrasi agar selalu unik dan lolos validasi 'distinct'
        if ($request->has('no_registrasi') && is_array($request->input('no_registrasi'))) {
            $request->merge([
                'no_registrasi' => array_values(array_unique($request->input('no_registrasi'))),
            ]);
        }

        $validated = $request->validate([
            'no_registrasi' => ['required', 'array', 'min:1', 'max:100'],
            'no_registrasi.*' => ['required', 'string', 'distinct', 'exists:pendaftaran,no_registrasi'],
        ]);

        $registrationNumbers = $validated['no_registrasi'];

        $filePaths = [];
        $deletedCount = DB::transaction(function () use ($registrationNumbers, &$filePaths): int {
            $registrations = Pendaftaran::with(['berkas', 'pembayaran.items'])
                ->whereIn('no_registrasi', $registrationNumbers)
                ->lockForUpdate()
                ->get();

            foreach ($registrations as $pendaftaran) {
                $noReg = $pendaftaran->no_registrasi;
                $filePaths = array_merge($filePaths, $pendaftaran->berkas->pluck('path_file')->all());

                // Bersihkan transaksi pembayaran dan detail pos pembayaran jika ada
                if ($pendaftaran->pembayaran()->exists()) {
                    foreach ($pendaftaran->pembayaran as $pay) {
                        $pay->items()->delete();
                        $pay->delete();
                    }
                }

                $pendaftaran->berkas()->delete();
                $pendaftaran->verifikasi()->delete();
                $pendaftaran->keuangan()->delete();
                $pendaftaran->delete();

                Cache::forget('daftar_ulang_status_'.$noReg);
                Cache::forget('daftar_ulang_date_'.$noReg);
                Cache::forget('daftar_ulang_pembayaran_'.$noReg);
            }

            return $registrations->count();
        });

        $this->deleteUnreferencedDocumentFiles($filePaths);

        AdminActivityLog::record(
            actionType: 'hapus_pendaftaran',
            title: 'Menghapus Data Pendaftar Terpilih',
            description: "Menghapus {$deletedCount} data pendaftaran terpilih (".implode(', ', array_map(fn ($r) => '#'.$r, $registrationNumbers)).') beserta seluruh berkas dan verifikasinya.',
            noRegistrasi: null,
            badgeLabel: 'Hapus Massal',
            badgeColor: 'rose',
            icon: 'trash',
            adminName: Auth::user()?->name ?: 'Admin SPMB'
        );

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "{$deletedCount} pendaftaran terpilih berhasil dihapus.",
                'deleted_count' => $deletedCount,
                'deleted_regs' => $registrationNumbers,
            ]);
        }

        if ($request->filled('redirect_to') || $request->filled('halaman')) {
            return $this->getDeletionRedirectResponse(
                $request,
                "{$deletedCount} pendaftaran terpilih berhasil dihapus."
            );
        }

        return redirect()->route('admin.dashboard')->with('success', "{$deletedCount} pendaftaran terpilih berhasil dihapus.");
    }

    public function destroy(Request $request, string $no_registrasi): RedirectResponse|JsonResponse
    {
        $pendaftaran = Pendaftaran::with(['berkas', 'pembayaran.items', 'calonSiswa'])->findOrFail($no_registrasi);
        $studentName = $pendaftaran->calonSiswa?->nama_lengkap ?: $pendaftaran->no_registrasi;

        $filePaths = $pendaftaran->berkas->pluck('path_file')->filter()->unique();

        DB::transaction(function () use ($pendaftaran): void {
            $noReg = $pendaftaran->no_registrasi;

            // Bersihkan transaksi pembayaran dan detail pos pembayaran jika ada
            if ($pendaftaran->pembayaran()->exists()) {
                foreach ($pendaftaran->pembayaran as $pay) {
                    $pay->items()->delete();
                    $pay->delete();
                }
            }

            $pendaftaran->berkas()->delete();
            $pendaftaran->verifikasi()->delete();
            $pendaftaran->keuangan()->delete();
            $pendaftaran->delete();

            Cache::forget('daftar_ulang_status_'.$noReg);
            Cache::forget('daftar_ulang_date_'.$noReg);
            Cache::forget('daftar_ulang_pembayaran_'.$noReg);
        });

        foreach ($filePaths as $filePath) {
            if (! str_starts_with($filePath, 'storage/berkas/') || str_contains($filePath, '..')) {
                continue;
            }

            if (! BerkasPersyaratan::where('path_file', $filePath)->exists()) {
                Storage::disk('public')->delete(substr($filePath, strlen('storage/')));
            }
        }

        AdminActivityLog::record(
            actionType: 'hapus_pendaftaran',
            title: 'Menghapus Data Pendaftar',
            description: "Menghapus data pendaftaran #{$no_registrasi} ({$studentName}) beserta seluruh berkas dan verifikasinya.",
            noRegistrasi: $no_registrasi,
            badgeLabel: 'Hapus Data',
            badgeColor: 'rose',
            icon: 'trash',
            adminName: Auth::user()?->name ?: 'Admin SPMB'
        );

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Pendaftaran {$no_registrasi} berhasil dihapus.",
                'no_registrasi' => $no_registrasi,
            ]);
        }

        return $this->getDeletionRedirectResponse(
            $request,
            "Pendaftaran {$no_registrasi} berhasil dihapus."
        );
    }

    private function getDeletionRedirectResponse(Request $request, string $message): RedirectResponse
    {
        $routeMap = [
            'daftar-ulang' => 'admin.daftar-ulang',
            'tabel-jurusan' => 'admin.tabel-jurusan',
            'status-berkas' => 'admin.status-berkas',
            'keuangan' => 'admin.keuangan',
            'kuota-rombel' => 'admin.kuota-rombel',
            'office' => 'admin.office',
            'log-aktivitas' => 'admin.log-aktivitas',
            'beranda' => 'admin.beranda',
        ];

        if ($request->filled('redirect_to')) {
            $redirectTo = (string) $request->input('redirect_to');
            if (str_starts_with($redirectTo, '/') || str_starts_with($redirectTo, url('/'))) {
                return redirect()->to($redirectTo)->with('success', $message);
            }
        }

        $halaman = $request->input('halaman');
        if ($halaman && isset($routeMap[$halaman])) {
            return redirect()->route($routeMap[$halaman])->with('success', $message);
        }

        $previousUrl = url()->previous();
        if ($previousUrl) {
            $parsedPath = trim((string) parse_url($previousUrl, PHP_URL_PATH), '/');
            foreach ($routeMap as $pageKey => $routeName) {
                if ($pageKey !== 'beranda' && (str_ends_with($parsedPath, $pageKey) || str_contains($previousUrl, "halaman={$pageKey}"))) {
                    return redirect()->to($previousUrl)->with('success', $message);
                }
            }

            $query = parse_url($previousUrl, PHP_URL_QUERY);
            if (! empty($query)) {
                return redirect()->to($previousUrl)->with('success', $message);
            }
        }

        return redirect()->route('admin.tabel-jurusan')->with('success', $message);
    }

    public function updateJurusanQuota(Request $request, Jurusan $jurusan): JsonResponse
    {
        $validated = $request->validate([
            'kuota' => ['required', 'integer', 'min:1', 'max:2147483647'],
        ]);

        $jurusan->update(['kuota' => $validated['kuota']]);

        return response()->json([
            'success' => true,
            'kuota' => (int) $jurusan->kuota,
        ]);
    }

    public function setWave(Request $request)
    {
        $validated = $request->validate([
            'wave' => ['required', 'string', 'in:Gelombang Khusus,Gelombang 1,Gelombang 2,Tutup Gelombang'],
        ]);

        Cache::forever('ppdb_active_wave', $validated['wave']);

        return response()->json([
            'success' => true,
            'wave' => $validated['wave'],
            'message' => "Gelombang pendaftaran berhasil diatur ke: {$validated['wave']}.",
        ]);
    }

    public function updateDaftarUlangStatus(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'no_registrasi' => ['required', 'string'],
            'status' => ['required', 'string', 'in:Sudah Daftar Ulang,Menunggu'],
            'status_pembayaran_awal' => ['sometimes', 'required', 'string', 'in:Belum,Nyicil,Lunas'],
        ]);

        $registrationNumber = $validated['no_registrasi'];
        $pendaftaran = Pendaftaran::with([
            'calonSiswa',
            'jurusan',
            'jalur',
            'gelombang',
            'keuangan',
            'pembayaran.items',
        ])->whereKey($registrationNumber)->firstOrFail();

        $keuanganService = app(KeuanganService::class);
        $billing = $keuanganService->calculateStudentBilling($pendaftaran);

        // Jika status pembayaran diatur ke LUNAS:
        // Otomatis tandai Sudah Daftar Ulang dan lakukan pelunasan di modul keuangan jika masih ada tagihan
        if (($validated['status_pembayaran_awal'] ?? null) === 'Lunas') {
            $validated['status'] = 'Sudah Daftar Ulang';

            if ($billing['total_sisa'] > 0) {
                $allocations = [];
                foreach ($billing['categories'] as $catKey => $cat) {
                    if ($cat['sisa'] > 0) {
                        $allocations[$catKey] = $cat['sisa'];
                    }
                }
                if (! empty($allocations)) {
                    $adminName = auth()->user()?->name ?: 'Admin SPMB';
                    $keuanganService->recordPayment(
                        $pendaftaran,
                        $allocations,
                        'Pelunasan Daftar Ulang',
                        now('Asia/Makassar')->format('Y-m-d'),
                        'Pelunasan otomatis melalui menu Daftar Ulang oleh admin',
                        $adminName
                    );
                    $billing = $keuanganService->calculateStudentBilling($pendaftaran->fresh(['keuangan', 'pembayaran.items']));
                }
            }
        }

        $previousStatus = Cache::get('daftar_ulang_status_'.$registrationNumber, 'Menunggu');
        $tglDaftarUlang = $validated['status'] === 'Sudah Daftar Ulang'
            ? ($previousStatus === $validated['status']
                ? Cache::get('daftar_ulang_date_'.$registrationNumber, now('Asia/Makassar')->format('Y-m-d H:i:s'))
                : now('Asia/Makassar')->format('Y-m-d H:i:s'))
            : null;
        $tglDaftarUlangFormatted = $tglDaftarUlang
            ? (str_contains($tglDaftarUlang, ':') ? Carbon::parse($tglDaftarUlang, 'Asia/Makassar')->format('d M Y H:i') : $tglDaftarUlang)
            : null;

        Cache::put('daftar_ulang_status_'.$registrationNumber, $validated['status'], now()->addMonths(6));
        if ($tglDaftarUlang) {
            Cache::put('daftar_ulang_date_'.$registrationNumber, $tglDaftarUlang, now()->addMonths(6));
        } else {
            Cache::forget('daftar_ulang_date_'.$registrationNumber);
        }
        if (array_key_exists('status_pembayaran_awal', $validated)) {
            Cache::put(
                'daftar_ulang_pembayaran_'.$registrationNumber,
                $validated['status_pembayaran_awal'],
                now()->addMonths(6)
            );
        }

        // Sinkronkan status di tabel keuangan_siswa
        $keuangan = $keuanganService->getOrCreateKeuangan($pendaftaran);
        if ($validated['status'] === 'Sudah Daftar Ulang') {
            $keuangan->update(['status_daftar_ulang' => 'Sah Daftar Ulang']);
        } elseif ($validated['status'] === 'Menunggu' && ! $keuangan->is_refunded) {
            $keuangan->update(['status_daftar_ulang' => 'Belum Daftar Ulang']);
        }

        $studentName = $pendaftaran?->calonSiswa?->nama_lengkap ?: $registrationNumber;

        $badgeColor = $validated['status'] === 'Sudah Daftar Ulang' ? 'emerald' : 'amber';
        $icon = $validated['status'] === 'Sudah Daftar Ulang' ? 'check' : 'clock';
        $paymentStatus = $validated['status_pembayaran_awal']
            ?? Cache::get('daftar_ulang_pembayaran_'.$registrationNumber, $pendaftaran?->status_pembayaran ?? null);
        $description = "Mengubah status daftar ulang siswa {$studentName} (No. Reg: {$registrationNumber}) menjadi '{$validated['status']}'.";
        if (array_key_exists('status_pembayaran_awal', $validated)) {
            $description .= " Status pembayaran menjadi '{$validated['status_pembayaran_awal']}'.";
        }

        $log = AdminActivityLog::record(
            actionType: 'daftar_ulang',
            title: 'Mengatur Status Daftar Ulang & Keuangan',
            description: $description,
            noRegistrasi: $registrationNumber,
            badgeLabel: $validated['status'],
            badgeColor: $badgeColor,
            icon: $icon,
            adminName: auth()->user()?->name ?: 'Admin SPMB'
        );

        return response()->json([
            'success' => true,
            'no_registrasi' => $validated['no_registrasi'],
            'status' => $validated['status'],
            'tgl_daftar_ulang' => $tglDaftarUlangFormatted ?? '-',
            'status_pembayaran_awal' => $paymentStatus,
            'billing' => $billing,
            'log' => [
                'id' => $log->id,
                'admin_name' => $log->admin_name,
                'action_type' => $log->action_type,
                'title' => $log->title,
                'description' => $log->description,
                'badge_label' => $log->badge_label,
                'badge_color' => $log->badge_color,
                'icon' => $log->icon,
                'no_registrasi' => $log->no_registrasi,
                'date_formatted' => $log->created_at->translatedFormat('d M Y, H:i').' WITA',
                'date_only' => $log->created_at->translatedFormat('d M Y'),
                'time_formatted' => $log->created_at->format('H:i:s').' WITA',
                'time_ago' => 'Baru saja',
                'created_at_ts' => $log->created_at->timestamp * 1000,
            ],
        ]);
    }

    public function pollActivityLogs(Request $request): JsonResponse
    {
        $afterId = (int) $request->query('after_id', 0);
        $logs = AdminActivityLog::where('id', '>', $afterId)
            ->orderBy('id', 'asc')
            ->take(50)
            ->get()
            ->map(function (AdminActivityLog $log) {
                return [
                    'id' => $log->id,
                    'admin_name' => $log->admin_name,
                    'action_type' => $log->action_type,
                    'title' => $log->title,
                    'description' => $log->description,
                    'badge_label' => $log->badge_label,
                    'badge_color' => $log->badge_color,
                    'icon' => $log->icon,
                    'no_registrasi' => $log->no_registrasi,
                    'date_formatted' => $log->created_at ? $log->created_at->translatedFormat('d M Y, H:i').' WITA' : '-',
                    'date_only' => $log->created_at ? $log->created_at->translatedFormat('d M Y') : '-',
                    'time_formatted' => $log->created_at ? $log->created_at->format('H:i:s').' WITA' : '-',
                    'time_ago' => $log->created_at ? $log->created_at->diffForHumans() : 'Baru saja',
                    'created_at_ts' => $log->created_at ? $log->created_at->timestamp * 1000 : null,
                ];
            });

        return response()->json([
            'success' => true,
            'logs' => $logs,
        ]);
    }

    public function destroyActivityLog(AdminActivityLog $log): JsonResponse
    {
        $logId = $log->id;
        $log->delete();

        return response()->json([
            'success' => true,
            'message' => 'Log aktivitas berhasil dihapus.',
            'deleted_ids' => [$logId],
        ]);
    }

    public function destroySelectedActivityLogs(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'exists:admin_activity_logs,id'],
        ]);

        $deletedIds = $validated['ids'];
        AdminActivityLog::whereIn('id', $deletedIds)->delete();

        return response()->json([
            'success' => true,
            'message' => count($deletedIds).' log aktivitas berhasil dihapus.',
            'deleted_ids' => $deletedIds,
        ]);
    }

    public function destroyAllActivityLogs(Request $request): JsonResponse
    {
        $totalDeleted = AdminActivityLog::count();
        AdminActivityLog::query()->delete();

        return response()->json([
            'success' => true,
            'message' => "Seluruh riwayat log aktivitas ({$totalDeleted} log) berhasil dibersihkan.",
            'deleted_count' => $totalDeleted,
        ]);
    }

    public function cleanupDummyData(): JsonResponse
    {
        Artisan::call('spmb:clean-dummy');
        $output = Artisan::output();

        return response()->json([
            'success' => true,
            'message' => 'Pembersihan data dummy dan normalisasi nominal pembukuan berhasil dijalankan.',
            'output' => $output,
        ]);
    }
}
