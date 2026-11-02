<?php

namespace Database\Factories;

use App\Models\AdminActivityLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AdminActivityLog>
 */
class AdminActivityLogFactory extends Factory
{
    protected $model = AdminActivityLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'admin_name' => 'Admin SPMB',
            'action_type' => 'daftar_ulang',
            'title' => 'Mengubah Status Daftar Ulang',
            'description' => 'Mengubah status daftar ulang siswa menjadi Sudah Daftar Ulang',
            'badge_label' => 'Daftar Ulang',
            'badge_color' => 'emerald',
            'icon' => 'check',
            'no_registrasi' => fake()->numerify('2026###'),
        ];
    }
}
