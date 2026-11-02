<?php

namespace App\Services;

use App\Models\Pendaftaran;

class SyaratBerkasService
{
    /**
     * Master definisi seluruh berkas persyaratan yang didukung di sistem sesuai PDF SPMB
     *
     * @var array<string, array<string, mixed>>
     */
    public const BERKAS_DEFINITIONS = [
        'berkas_foto' => [
            'key' => 'berkas_foto',
            'label' => 'Pasfoto Formal',
            'title' => 'Pasfoto Siswa (Formal)',
            'desc' => 'Ukuran 3x4 latar polos (merah/biru), format JPG/PNG/WEBP. Maks. 10 MB.',
            'accept' => 'image/jpeg,image/png,image/webp,image/jpg',
            'mimes' => 'jpg,jpeg,png,webp',
            'icon' => 'assets/img/icon/photo.svg',
            'is_image' => true,
        ],
        'berkas_ijazah' => [
            'key' => 'berkas_ijazah',
            'label' => 'Fotokopi Ijazah / SKL',
            'title' => 'Fotokopi Ijazah / SKL',
            'desc' => 'Surat Keterangan Lulus (SKL) atau Ijazah SMP/MTs dilegalisir. Format JPG/PNG/PDF. Maks. 10 MB.',
            'accept' => 'image/jpeg,image/png,image/webp,image/jpg,.pdf,application/pdf',
            'mimes' => 'jpg,jpeg,png,webp,pdf',
            'icon' => 'assets/img/icon/document-text.svg',
            'is_image' => false,
        ],
        'berkas_akta' => [
            'key' => 'berkas_akta',
            'label' => 'Akta Kelahiran Asli',
            'title' => 'Akta Kelahiran Asli',
            'desc' => 'Scan atau foto Akta Kelahiran resmi dari Dukcapil / Surat Kenal Lahir. Format JPG/PNG/PDF. Maks. 10 MB.',
            'accept' => 'image/jpeg,image/png,image/webp,image/jpg,.pdf,application/pdf',
            'mimes' => 'jpg,jpeg,png,webp,pdf',
            'icon' => 'assets/img/icon/check-circle.svg',
            'is_image' => false,
        ],
        'berkas_kk' => [
            'key' => 'berkas_kk',
            'label' => 'Kartu Keluarga (KK)',
            'title' => 'Kartu Keluarga (KK)',
            'desc' => 'Scan atau foto jelas Kartu Keluarga (KK) asli seluruh anggota keluarga. Format JPG/PNG/PDF. Maks. 10 MB.',
            'accept' => 'image/jpeg,image/png,image/webp,image/jpg,.pdf,application/pdf',
            'mimes' => 'jpg,jpeg,png,webp,pdf',
            'icon' => 'assets/img/icon/document-text.svg',
            'is_image' => false,
        ],
        'berkas_nisn' => [
            'key' => 'berkas_nisn',
            'label' => 'Fotokopi / Bukti NISN',
            'title' => 'Fotokopi / Bukti NISN',
            'desc' => 'Tangkapan layar/cetak data NISN dari Kemdikbud atau surat keterangan sekolah. Format JPG/PNG/PDF. Maks. 10 MB.',
            'accept' => 'image/jpeg,image/png,image/webp,image/jpg,.pdf,application/pdf',
            'mimes' => 'jpg,jpeg,png,webp,pdf',
            'icon' => 'assets/img/icon/document-text.svg',
            'is_image' => false,
        ],
        'berkas_prestasi' => [
            'key' => 'berkas_prestasi',
            'label' => 'Sertifikat / Piagam Juara (Dilegalisir)',
            'title' => 'Sertifikat / Piagam Juara',
            'desc' => 'Sertifikat atau piagam prestasi akademik/non-akademik dilegalisir pihak berwenang. Format JPG/PNG/PDF. Maks. 10 MB.',
            'accept' => 'image/jpeg,image/png,image/webp,image/jpg,.pdf,application/pdf',
            'mimes' => 'jpg,jpeg,png,webp,pdf',
            'icon' => 'assets/img/icon/award.svg',
            'is_image' => false,
        ],
        'berkas_sktm' => [
            'key' => 'berkas_sktm',
            'label' => 'Surat Keterangan Kurang Mampu (SKTM)',
            'title' => 'Surat Keterangan Kurang Mampu (SKTM)',
            'desc' => 'Surat Keterangan Kurang Mampu resmi dari Kepala Desa / Lurah setempat. Format JPG/PNG/PDF. Maks. 10 MB.',
            'accept' => 'image/jpeg,image/png,image/webp,image/jpg,.pdf,application/pdf',
            'mimes' => 'jpg,jpeg,png,webp,pdf',
            'icon' => 'assets/img/icon/document-text.svg',
            'is_image' => false,
        ],
        'berkas_permohonan' => [
            'key' => 'berkas_permohonan',
            'label' => 'Surat Permohonan Keringanan Biaya',
            'title' => 'Surat Permohonan Keringanan Biaya',
            'desc' => 'Surat permohonan keringanan biaya yang ditandatangani orang tua/wali siswa. Format JPG/PNG/PDF. Maks. 10 MB.',
            'accept' => 'image/jpeg,image/png,image/webp,image/jpg,.pdf,application/pdf',
            'mimes' => 'jpg,jpeg,png,webp,pdf',
            'icon' => 'assets/img/icon/document-text.svg',
            'is_image' => false,
        ],
        'berkas_kip' => [
            'key' => 'berkas_kip',
            'label' => 'Kartu Indonesia Pintar (KIP)',
            'title' => 'Kartu Indonesia Pintar (KIP)',
            'desc' => 'Scan/foto fisik Kartu Indonesia Pintar (KIP) asli atas nama calon siswa. Format JPG/PNG/PDF. Maks. 10 MB.',
            'accept' => 'image/jpeg,image/png,image/webp,image/jpg,.pdf,application/pdf',
            'mimes' => 'jpg,jpeg,png,webp,pdf',
            'icon' => 'assets/img/icon/document-text.svg',
            'is_image' => false,
        ],
        'berkas_rekomendasi_mou' => [
            'key' => 'berkas_rekomendasi_mou',
            'label' => 'Surat Keterangan / Form Rekomendasi MOU',
            'title' => 'Surat Keterangan / Rekomendasi MOU',
            'desc' => 'Surat Keterangan Warga Desa MOU atau Form Rekomendasi SMP/MTs mitra sekolah. Format JPG/PNG/PDF. Maks. 10 MB.',
            'accept' => 'image/jpeg,image/png,image/webp,image/jpg,.pdf,application/pdf',
            'mimes' => 'jpg,jpeg,png,webp,pdf',
            'icon' => 'assets/img/icon/document-text.svg',
            'is_image' => false,
        ],
        'berkas_pernyataan_warga' => [
            'key' => 'berkas_pernyataan_warga',
            'label' => 'Surat Pernyataan Warga Sekolah Bermaterai 10.000',
            'title' => 'Surat Pernyataan Warga Sekolah',
            'desc' => 'Surat pernyataan saudara kembar/kandung/guru/staf dengan materai Rp 10.000. Format JPG/PNG/PDF. Maks. 10 MB.',
            'accept' => 'image/jpeg,image/png,image/webp,image/jpg,.pdf,application/pdf',
            'mimes' => 'jpg,jpeg,png,webp,pdf',
            'icon' => 'assets/img/icon/document-text.svg',
            'is_image' => false,
        ],
        'berkas_panti' => [
            'key' => 'berkas_panti',
            'label' => 'Surat Keterangan Panti Asuhan MOU / Raport',
            'title' => 'Surat Keterangan Panti Asuhan / Raport Sem 5',
            'desc' => 'Surat resmi dari pengurus Panti Asuhan rekanan atau fotokopi raport semester 5. Format JPG/PNG/PDF. Maks. 10 MB.',
            'accept' => 'image/jpeg,image/png,image/webp,image/jpg,.pdf,application/pdf',
            'mimes' => 'jpg,jpeg,png,webp,pdf',
            'icon' => 'assets/img/icon/document-text.svg',
            'is_image' => false,
        ],
        'berkas_ojol' => [
            'key' => 'berkas_ojol',
            'label' => 'Bukti Keanggotaan Ojek Online',
            'title' => 'Bukti Keanggotaan Ojek Online',
            'desc' => 'Tangkapan layar profil akun aktif mitra driver ojek online orang tua/wali. Format JPG/PNG/PDF. Maks. 10 MB.',
            'accept' => 'image/jpeg,image/png,image/webp,image/jpg,.pdf,application/pdf',
            'mimes' => 'jpg,jpeg,png,webp,pdf',
            'icon' => 'assets/img/icon/document-text.svg',
            'is_image' => false,
        ],
        'berkas_minat_bakat' => [
            'key' => 'berkas_minat_bakat',
            'label' => 'Portofolio / Video Bakat Calon Siswa',
            'title' => 'Portofolio / Dokumen Bakat',
            'desc' => 'Portofolio karya, piagam seni/olahraga, atau tautan video penampilan bakat. Format PDF/JPG/PNG. Maks. 10 MB.',
            'accept' => 'image/jpeg,image/png,image/webp,image/jpg,.pdf,application/pdf',
            'mimes' => 'jpg,jpeg,png,webp,pdf',
            'icon' => 'assets/img/icon/document-text.svg',
            'is_image' => false,
        ],
        'berkas_medsos_review' => [
            'key' => 'berkas_medsos_review',
            'label' => 'Bukti Unggah Medsos & Google Review',
            'title' => 'Bukti Share Medsos & Google Review',
            'desc' => 'Tangkapan layar postingan medsos pribadi, ulasan Google Review & follow medsos sekolah. Format JPG/PNG. Maks. 10 MB.',
            'accept' => 'image/jpeg,image/png,image/webp,image/jpg',
            'mimes' => 'jpg,jpeg,png,webp',
            'icon' => 'assets/img/icon/photo.svg',
            'is_image' => true,
        ],
        'berkas_voucher' => [
            'key' => 'berkas_voucher',
            'label' => 'Bukti Fisik / Foto Voucher PPDB',
            'title' => 'Voucher Rekomendasi / HUT / Lomba',
            'desc' => 'Foto atau scan voucher rekomendasi alumni/siswa aktif atau voucher HUT/lomba. Format JPG/PNG/PDF. Maks. 10 MB.',
            'accept' => 'image/jpeg,image/png,image/webp,image/jpg,.pdf,application/pdf',
            'mimes' => 'jpg,jpeg,png,webp,pdf',
            'icon' => 'assets/img/icon/document-text.svg',
            'is_image' => false,
        ],
    ];

    /**
     * Dapatkan daftar berkas persyaratan wajib dan berkas tambahan untuk pendaftar
     *
     * @return array<string, array<string, mixed>>
     */
    public static function getRequiredDocuments(
        ?string $jalurNama = null,
        ?string $subJalur = null,
        ?string $gelombangNama = null,
        ?string $voucherJenis = null,
        ?string $jalurName = null,
        ?string $gelombangName = null
    ): array {
        $jalurNama = $jalurNama ?? $jalurName;
        $gelombangNama = $gelombangNama ?? $gelombangName;

        $requiredKeys = [
            'berkas_foto',
            'berkas_kk',
            'berkas_akta',
        ];

        $jalurLower = strtolower($jalurNama ?? '');
        $isUmum = str_contains($jalurLower, 'umum');
        $isPrestasi = str_contains($jalurLower, 'prestasi');
        $isAfirmasi = str_contains($jalurLower, 'afirmasi') || str_contains($jalurLower, 'kurang mampu');
        $isKip = str_contains($jalurLower, 'kip');
        $isKhusus = str_contains($jalurLower, 'khusus');
        $isMinatBakat = str_contains($jalurLower, 'minat') || str_contains($jalurLower, 'bakat');

        // Berkas spesifik jalur
        if ($isPrestasi) {
            $requiredKeys[] = 'berkas_prestasi';
        } elseif ($isAfirmasi) {
            $requiredKeys[] = 'berkas_sktm';
            $requiredKeys[] = 'berkas_permohonan';
        } elseif ($isKip) {
            $requiredKeys[] = 'berkas_kip';
            $requiredKeys[] = 'berkas_permohonan';
        } elseif ($isMinatBakat) {
            $requiredKeys[] = 'berkas_minat_bakat';
        } elseif ($isKhusus) {
            $sub = strtolower($subJalur ?? '');
            if (str_contains($sub, 'mou')) {
                $requiredKeys[] = 'berkas_rekomendasi_mou';
                $requiredKeys[] = 'berkas_permohonan';
            } elseif (str_contains($sub, 'kembar') || str_contains($sub, 'saudara') || str_contains($sub, 'guru') || str_contains($sub, 'staf')) {
                $requiredKeys[] = 'berkas_pernyataan_warga';
                $requiredKeys[] = 'berkas_permohonan';
            } elseif (str_contains($sub, 'panti')) {
                $requiredKeys[] = 'berkas_panti';
                $requiredKeys[] = 'berkas_permohonan';
            } elseif (str_contains($sub, 'ojol')) {
                $requiredKeys[] = 'berkas_ojol';
                $requiredKeys[] = 'berkas_permohonan';
            } elseif (str_contains($sub, 'minat') || str_contains($sub, 'bakat')) {
                $requiredKeys[] = 'berkas_minat_bakat';
            }
        }

        // Syarat gratis pendaftaran gelombang khusus
        $gelombangLower = strtolower($gelombangNama ?? '');
        if (str_contains($gelombangLower, 'khusus') && empty($voucherJenis)) {
            $requiredKeys[] = 'berkas_medsos_review';
        }

        // Syarat voucher
        if (! empty($voucherJenis) && $voucherJenis !== 'tanpa_voucher') {
            $requiredKeys[] = 'berkas_voucher';
        }

        $docs = [];
        foreach (array_unique($requiredKeys) as $key) {
            if (isset(self::BERKAS_DEFINITIONS[$key])) {
                $docs[$key] = self::BERKAS_DEFINITIONS[$key];
            }
        }

        return $docs;
    }

    /**
     * Dapatkan daftar berkas untuk instance Pendaftaran
     *
     * @return array<string, array<string, mixed>>
     */
    public function getDocumentsForPendaftaran(Pendaftaran $pendaftaran): array
    {
        return self::getRequiredDocuments(
            $pendaftaran->jalur?->nama_jalur,
            $pendaftaran->sub_jalur,
            $pendaftaran->gelombang?->nama_gelombang,
            $pendaftaran->voucher_jenis
        );
    }

    /**
     * Peta balik antara jenis_berkas (label teks di DB) ke inputName
     *
     * @return array<string, string>
     */
    public static function getLabelToKeyMap(): array
    {
        $map = [];
        foreach (self::BERKAS_DEFINITIONS as $key => $def) {
            $map[$def['label']] = $key;
        }

        // Variasi label lama yang sudah tersimpan di database
        $map['Pasfoto Formal'] = 'berkas_foto';
        $map['Kartu Keluarga (KK)'] = 'berkas_kk';
        $map['Akta Kelahiran Asli'] = 'berkas_akta';

        return $map;
    }
}
