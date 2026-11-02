<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminActivityLog extends Model
{
    use HasFactory;

    protected $table = 'admin_activity_logs';

    protected $fillable = [
        'admin_name',
        'action_type',
        'title',
        'description',
        'badge_label',
        'badge_color',
        'icon',
        'no_registrasi',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function setNoRegistrasiAttribute($value): void
    {
        $this->attributes['no_registrasi'] = is_numeric($value) ? str_pad((string) ((int) $value), 3, '0', STR_PAD_LEFT) : $value;
    }

    public function getNoRegistrasiAttribute($value): ?string
    {
        if ($value === null) {
            return null;
        }

        return is_numeric($value) ? str_pad((string) ((int) $value), 3, '0', STR_PAD_LEFT) : (string) $value;
    }

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class, 'no_registrasi', 'no_registrasi');
    }

    public function getNoRegistrasiFormattedAttribute(): ?string
    {
        if (empty($this->no_registrasi)) {
            return null;
        }

        return is_numeric($this->no_registrasi)
            ? str_pad((string) $this->no_registrasi, 3, '0', STR_PAD_LEFT)
            : (string) $this->no_registrasi;
    }

    /**
     * Helper to log an admin activity.
     */
    public static function record(
        string $actionType,
        string $title,
        ?string $description = null,
        ?string $noRegistrasi = null,
        ?string $badgeLabel = null,
        string $badgeColor = 'blue',
        string $icon = 'activity',
        ?string $adminName = null
    ): self {
        return self::create([
            'admin_name' => $adminName ?: (auth()->user()?->name ?: 'Admin SPMB'),
            'action_type' => $actionType,
            'title' => $title,
            'description' => $description,
            'badge_label' => $badgeLabel,
            'badge_color' => $badgeColor,
            'icon' => $icon,
            'no_registrasi' => $noRegistrasi,
        ]);
    }
}
