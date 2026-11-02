<?php

namespace App\Http\Controllers;

use App\Models\PembayaranSiswa;
use App\Models\Pendaftaran;
use App\Services\KeuanganService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeuanganController extends Controller
{
    public function __construct(
        public KeuanganService $keuanganService
    ) {}

    /**
     * Catat pembayaran baru untuk siswa
     */
    public function recordPayment(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'no_registrasi' => ['required', 'string', 'exists:pendaftaran,no_registrasi'],
            'metode_pembayaran' => ['required', 'string', 'max:50'],
            'tgl_bayar' => ['nullable', 'date', 'date_format:Y-m-d', 'after:2020-01-01', 'before_or_equal:2099-12-31'],
            'catatan' => ['nullable', 'string', 'max:500'],
            'alokasi' => ['required', 'array'],
            'alokasi.pendaftaran' => ['nullable', 'numeric', 'min:0'],
            'alokasi.mos' => ['nullable', 'numeric', 'min:0'],
            'alokasi.asuransi' => ['nullable', 'numeric', 'min:0'],
            'alokasi.seragam' => ['nullable', 'numeric', 'min:0'],
            'alokasi.dpp' => ['nullable', 'numeric', 'min:0'],
            'alokasi.spp' => ['nullable', 'numeric', 'min:0'],
            'alokasi.osis' => ['nullable', 'numeric', 'min:0'],
        ]);

        $pendaftaran = Pendaftaran::with([
            'calonSiswa',
            'jurusan',
            'jalur',
            'gelombang',
            'keuangan',
            'pembayaran.items',
        ])->where('no_registrasi', $validated['no_registrasi'])->firstOrFail();

        $adminName = Auth::user()?->name ?: 'Petugas Keuangan SPMB';

        try {
            $pembayaran = $this->keuanganService->recordPayment(
                $pendaftaran,
                $validated['alokasi'],
                $validated['metode_pembayaran'],
                $validated['tgl_bayar'] ?? Carbon::now('Asia/Makassar')->format('Y-m-d'),
                $validated['catatan'] ?? null,
                $adminName
            );

            $billing = $this->keuanganService->calculateStudentBilling($pendaftaran->fresh(['keuangan', 'pembayaran.items']));

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pembayaran berhasil dicatat dengan nomor kuitansi '.$pembayaran->no_kuitansi,
                    'pembayaran' => $pembayaran->load('items'),
                    'billing' => $billing,
                ]);
            }

            return redirect()->route('admin.keuangan')->with('toast', [
                'type' => 'success',
                'title' => 'Pembayaran Berhasil Dicatat',
                'message' => 'Kuitansi '.$pembayaran->no_kuitansi.' sejumlah Rp '.number_format($pembayaran->total_bayar, 0, ',', '.').' berhasil disimpan.',
            ]);
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->with('toast', [
                'type' => 'error',
                'title' => 'Gagal Mencatat Pembayaran',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Proses pengembalian dana siswa diterima di sekolah negeri
     */
    public function processRefund(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'no_registrasi' => ['required', 'string', 'exists:pendaftaran,no_registrasi'],
            'alasan' => ['required', 'string', 'max:500'],
        ]);

        $pendaftaran = Pendaftaran::with([
            'calonSiswa',
            'jurusan',
            'jalur',
            'gelombang',
            'keuangan',
            'pembayaran.items',
        ])->where('no_registrasi', $validated['no_registrasi'])->firstOrFail();

        $adminName = Auth::user()?->name ?: 'Petugas Keuangan SPMB';

        try {
            $refundResult = $this->keuanganService->processRefund(
                $pendaftaran,
                $validated['alasan'],
                $adminName
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Refund dana siswa berhasil diproses sebesar Rp '.number_format($refundResult['refund_amount'], 0, ',', '.'),
                    'refund' => $refundResult,
                ]);
            }

            return redirect()->route('admin.keuangan')->with('toast', [
                'type' => 'success',
                'title' => 'Refund Berhasil Diproses',
                'message' => 'Pengembalian dana Rp '.number_format($refundResult['refund_amount'], 0, ',', '.').' telah dicatat (Non-refundable: Rp '.number_format($refundResult['non_refundable'], 0, ',', '.').').',
            ]);
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->with('toast', [
                'type' => 'error',
                'title' => 'Gagal Memproses Refund',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Penyesuaian diskon SPP manual oleh Admin (Kebijakan Kepala Sekolah)
     */
    public function updateCustomSppDiscount(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'no_registrasi' => ['required', 'string', 'exists:pendaftaran,no_registrasi'],
            'diskon_spp' => ['required', 'numeric', 'min:0', 'max:100'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        $pendaftaran = Pendaftaran::where('no_registrasi', $validated['no_registrasi'])->firstOrFail();
        $adminName = Auth::user()?->name ?: 'Petugas Keuangan SPMB';

        try {
            $this->keuanganService->updateCustomSppDiscount(
                $pendaftaran,
                (float) $validated['diskon_spp'],
                $validated['catatan'] ?? null,
                $adminName
            );

            $billing = $this->keuanganService->calculateStudentBilling($pendaftaran->fresh(['keuangan', 'pembayaran.items']));

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Diskon SPP berhasil diperbarui menjadi '.$validated['diskon_spp'].'%',
                    'billing' => $billing,
                ]);
            }

            return redirect()->route('admin.keuangan')->with('toast', [
                'type' => 'success',
                'title' => 'Diskon SPP Diperbarui',
                'message' => 'Diskon SPP berhasil disesuaikan menjadi '.$validated['diskon_spp'].'%.',
            ]);
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->with('toast', [
                'type' => 'error',
                'title' => 'Gagal Menyesuaikan Diskon',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Ambil rincian keuangan siswa beserta riwayat pembayarannya
     */
    public function getStudentFinanceDetail(string $no_registrasi): JsonResponse
    {
        $pendaftaran = Pendaftaran::with([
            'calonSiswa',
            'jurusan',
            'jalur',
            'gelombang',
            'keuangan',
            'pembayaran.items',
        ])->where('no_registrasi', $no_registrasi)->firstOrFail();

        $billing = $this->keuanganService->calculateStudentBilling($pendaftaran);

        $riwayat = $pendaftaran->pembayaran->sortByDesc('created_at')->values()->map(function ($item) {
            return [
                'id' => $item->id,
                'no_kuitansi' => $item->no_kuitansi,
                'tipe_transaksi' => $item->tipe_transaksi,
                'total_bayar' => $item->total_bayar,
                'metode_pembayaran' => $item->metode_pembayaran,
                'tgl_bayar' => $item->tgl_bayar?->format('d/m/Y') ?: '-',
                'catatan' => $item->catatan ?: '-',
                'nama_petugas' => $item->nama_petugas ?: 'Petugas Keuangan',
                'items' => $item->items->map(function ($row) {
                    return [
                        'kategori' => $row->kategori,
                        'nama_kategori' => KeuanganService::KATEGORI_CONFIG[$row->kategori]['nama'] ?? $row->kategori,
                        'nominal' => $row->nominal,
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'student' => [
                'no_registrasi' => $pendaftaran->no_registrasi,
                'nama' => $pendaftaran->calonSiswa?->nama_lengkap ?: '-',
                'nisn' => $pendaftaran->calonSiswa?->nisn ?: '-',
                'asal_sekolah' => $pendaftaran->calonSiswa?->asal_sekolah ?: '-',
                'jurusan' => $pendaftaran->jurusan?->nama_jurusan ?: '-',
                'jalur' => $pendaftaran->jalur?->nama_jalur ?: '-',
                'gelombang' => $pendaftaran->gelombang?->nama_gelombang ?: '-',
            ],
            'billing' => $billing,
            'riwayat' => $riwayat,
        ]);
    }

    /**
     * Ambil data lengkap 1 kuitansi untuk keperluan cetak kuitansi resmi
     */
    public function getKuitansiDetail(string $no_kuitansi): JsonResponse
    {
        $pembayaran = PembayaranSiswa::with([
            'items',
            'pendaftaran.calonSiswa',
            'pendaftaran.jurusan',
            'pendaftaran.jalur',
            'pendaftaran.gelombang',
        ])->where('no_kuitansi', $no_kuitansi)->firstOrFail();

        $itemsWithLabel = $pembayaran->items->map(function ($item) {
            return [
                'kategori' => $item->kategori,
                'nama' => KeuanganService::KATEGORI_CONFIG[$item->kategori]['nama'] ?? $item->kategori,
                'nominal' => $item->nominal,
                'nominal_format' => 'Rp '.number_format($item->nominal, 0, ',', '.'),
            ];
        });

        return response()->json([
            'success' => true,
            'kuitansi' => [
                'no_kuitansi' => $pembayaran->no_kuitansi,
                'tgl_bayar' => $pembayaran->tgl_bayar?->format('d F Y') ?: Carbon::now('Asia/Makassar')->format('d F Y'),
                'metode_pembayaran' => $pembayaran->metode_pembayaran,
                'total_bayar' => $pembayaran->total_bayar,
                'total_bayar_format' => 'Rp '.number_format($pembayaran->total_bayar, 0, ',', '.'),
                'terbilang' => KeuanganService::terbilang($pembayaran->total_bayar).' Rupiah',
                'catatan' => $pembayaran->catatan,
                'nama_petugas' => $pembayaran->nama_petugas ?: 'Bendahara SPMB 2027',
                'items' => $itemsWithLabel,
                'student' => [
                    'no_registrasi' => $pembayaran->pendaftaran?->no_registrasi,
                    'nama_lengkap' => $pembayaran->pendaftaran?->calonSiswa?->nama_lengkap ?: '-',
                    'asal_sekolah' => $pembayaran->pendaftaran?->calonSiswa?->asal_sekolah ?: '-',
                    'jurusan' => $pembayaran->pendaftaran?->jurusan?->nama_jurusan ?: '-',
                    'jalur' => $pembayaran->pendaftaran?->jalur?->nama_jalur ?: '-',
                    'gelombang' => $pembayaran->pendaftaran?->gelombang?->nama_gelombang ?: '-',
                ],
            ],
        ]);
    }

    /**
     * Batalkan / Void 1 kuitansi transaksi pembayaran
     */
    public function voidKuitansi(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'no_kuitansi' => ['required', 'string', 'exists:pembayaran_siswa,no_kuitansi'],
            'alasan' => ['required', 'string', 'min:3', 'max:500'],
        ]);

        $adminName = Auth::user()?->name ?: 'Petugas Keuangan SPMB';

        try {
            $billing = $this->keuanganService->voidKuitansi(
                $validated['no_kuitansi'],
                $validated['alasan'],
                $adminName
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kuitansi '.$validated['no_kuitansi'].' berhasil dibatalkan (void).',
                    'billing' => $billing,
                ]);
            }

            return redirect()->route('admin.keuangan')->with('toast', [
                'type' => 'success',
                'title' => 'Kuitansi Dibatalkan',
                'message' => 'Kuitansi '.$validated['no_kuitansi'].' telah dibatalkan.',
            ]);
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->with('toast', [
                'type' => 'error',
                'title' => 'Gagal Membatalkan Kuitansi',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Reset seluruh transaksi pembayaran siswa kembali ke Rp 0
     */
    public function resetPayment(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'no_registrasi' => ['required', 'string', 'exists:pendaftaran,no_registrasi'],
            'alasan' => ['required', 'string', 'min:3', 'max:500'],
        ]);

        $pendaftaran = Pendaftaran::where('no_registrasi', $validated['no_registrasi'])->firstOrFail();
        $adminName = Auth::user()?->name ?: 'Petugas Keuangan SPMB';

        try {
            $billing = $this->keuanganService->resetStudentPayments(
                $pendaftaran,
                $validated['alasan'],
                $adminName
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Seluruh pembayaran siswa '.$pendaftaran->no_registrasi.' berhasil direset ke Rp 0.',
                    'billing' => $billing,
                ]);
            }

            return redirect()->route('admin.keuangan')->with('toast', [
                'type' => 'success',
                'title' => 'Pembayaran Direset',
                'message' => 'Status pembayaran siswa '.$pendaftaran->no_registrasi.' dikembalikan ke Rp 0.',
            ]);
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->with('toast', [
                'type' => 'error',
                'title' => 'Gagal Mereset Pembayaran',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Hapus data siswa dummy beserta tagihannya (Khusus Admin Utama)
     */
    public function deleteStudent(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'no_registrasi' => ['required', 'string', 'exists:pendaftaran,no_registrasi'],
        ]);

        $pendaftaran = Pendaftaran::with('calonSiswa')->where('no_registrasi', $validated['no_registrasi'])->firstOrFail();
        $adminName = Auth::user()?->name ?: 'Admin SPMB';

        try {
            $noReg = $pendaftaran->no_registrasi;
            $this->keuanganService->deleteStudentWithFinances($pendaftaran, $adminName);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data siswa '.$noReg.' beserta tagihannya berhasil dihapus.',
                ]);
            }

            return redirect()->route('admin.keuangan')->with('toast', [
                'type' => 'success',
                'title' => 'Data Siswa Dihapus',
                'message' => 'Data siswa '.$noReg.' beserta tagihannya berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->with('toast', [
                'type' => 'error',
                'title' => 'Gagal Menghapus Siswa',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Bulk action: Reset pembayaran beberapa siswa terpilih
     */
    public function resetSelected(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'no_registrasi' => ['required', 'array', 'min:1'],
            'no_registrasi.*' => ['required', 'string', 'exists:pendaftaran,no_registrasi'],
            'alasan' => ['required', 'string', 'min:3', 'max:500'],
        ]);

        $adminName = Auth::user()?->name ?: 'Petugas Keuangan SPMB';
        $resetCount = 0;

        foreach ($validated['no_registrasi'] as $noReg) {
            $pendaftaran = Pendaftaran::where('no_registrasi', $noReg)->first();
            if ($pendaftaran) {
                $this->keuanganService->resetStudentPayments($pendaftaran, $validated['alasan'], $adminName);
                $resetCount++;
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Pembayaran {$resetCount} siswa terpilih berhasil direset ke Rp 0.",
            ]);
        }

        return redirect()->route('admin.keuangan')->with('toast', [
            'type' => 'success',
            'title' => 'Reset Terpilih Berhasil',
            'message' => "Pembayaran {$resetCount} siswa terpilih berhasil direset.",
        ]);
    }

    /**
     * Bulk action: Hapus beberapa siswa terpilih beserta tagihannya
     */
    public function destroySelected(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'no_registrasi' => ['required', 'array', 'min:1'],
            'no_registrasi.*' => ['required', 'string', 'exists:pendaftaran,no_registrasi'],
        ]);

        $adminName = Auth::user()?->name ?: 'Admin SPMB';
        $deletedCount = 0;

        foreach ($validated['no_registrasi'] as $noReg) {
            $pendaftaran = Pendaftaran::where('no_registrasi', $noReg)->first();
            if ($pendaftaran) {
                $this->keuanganService->deleteStudentWithFinances($pendaftaran, $adminName);
                $deletedCount++;
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$deletedCount} data siswa beserta tagihannya berhasil dihapus.",
            ]);
        }

        return redirect()->route('admin.keuangan')->with('toast', [
            'type' => 'success',
            'title' => 'Hapus Terpilih Berhasil',
            'message' => "{$deletedCount} data siswa terpilih berhasil dihapus.",
        ]);
    }
}
