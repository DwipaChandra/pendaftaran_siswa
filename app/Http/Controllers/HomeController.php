<?php

namespace App\Http\Controllers;

use App\Models\GelombangPendaftaran;
use App\Models\JalurPendaftaran;
use App\Models\Jurusan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class HomeController extends Controller
{
    public function index(Request $request, ?string $modal = null): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $jurusanQuotas = $this->getJurusanQuotaData();
        $jalurList = JalurPendaftaran::all();
        $gelombangList = GelombangPendaftaran::all();
        $activeWave = Cache::get('ppdb_active_wave', 'Gelombang Khusus');
        $openModal = $modal ?? $request->query('modal');

        return view('landing.index', compact('jurusanQuotas', 'jalurList', 'gelombangList', 'activeWave', 'openModal'));
    }

    public function termsAndConditions(Request $request): View|RedirectResponse
    {
        return $this->index($request, 'syarat-ketentuan');
    }

    public function privacyPolicy(Request $request): View|RedirectResponse
    {
        return $this->index($request, 'kebijakan-privasi');
    }

    public function waveStatus(): JsonResponse
    {
        return response()->json([
            'wave' => Cache::get('ppdb_active_wave', 'Gelombang Khusus'),
        ])->header('Cache-Control', 'no-store');
    }

    public function quotaStatus(): JsonResponse
    {
        return response()->json([
            'jurusan' => $this->getJurusanQuotaData(),
        ])->header('Cache-Control', 'no-store');
    }

    /**
     * Download the registration guide and tuition fee breakdown PDF.
     */
    public function downloadPanduan(): BinaryFileResponse
    {
        $filePath = public_path('assets/docs/Panduan-dan-Rincian-Biaya-Pendaftaran-SMK-TI-Bali-Global-Badung.pdf');

        if (! file_exists($filePath)) {
            $fallbackPath = storage_path('app/public/docs/Panduan-dan-Rincian-Biaya-Pendaftaran-SMK-TI-Bali-Global-Badung.pdf');
            if (file_exists($fallbackPath)) {
                $filePath = $fallbackPath;
            } else {
                abort(404, 'File panduan pendaftaran tidak ditemukan.');
            }
        }

        return response()->download(
            $filePath,
            'Panduan-dan-Rincian-Biaya-Pendaftaran-SMK-TI-Bali-Global-Badung.pdf',
            [
                'Content-Type' => 'application/pdf',
            ]
        );
    }

    /**
     * @return list<array{id: int, nama_jurusan: string, kuota: int, terisi: int, tersisa: int, persentase: int, warna: string, status: string}>
     */
    private function getJurusanQuotaData(): array
    {
        return Jurusan::withCount([
            'pendaftaran as diterima_count' => fn ($query) => $query->where('status_berkas', 'Diterima'),
        ])
            ->get()
            ->unique(fn (Jurusan $jurusan) => $jurusan->kode)
            ->values()
            ->map(function (Jurusan $jurusan): array {
                $quota = max((int) ($jurusan->kuota ?: 72), 1);
                $acceptedCount = (int) $jurusan->diterima_count;
                $percentage = min(100, (int) round(($acceptedCount / $quota) * 100));
                $remainingQuota = max(0, $quota - $acceptedCount);
                $color = $percentage >= 80 ? 'red' : ($percentage >= 50 ? 'yellow' : 'green');
                $status = $remainingQuota === 0
                    ? 'Kuota penuh'
                    : ($percentage >= 80 ? 'Hampir penuh' : ($percentage >= 50 ? 'Terbatas' : 'Tersedia'));

                return [
                    'id' => (int) $jurusan->id_jurusan,
                    'nama_jurusan' => $jurusan->nama_jurusan,
                    'kuota' => $quota,
                    'terisi' => $acceptedCount,
                    'tersisa' => $remainingQuota,
                    'persentase' => $percentage,
                    'warna' => $color,
                    'status' => $status,
                ];
            })
            ->all();
    }
}
