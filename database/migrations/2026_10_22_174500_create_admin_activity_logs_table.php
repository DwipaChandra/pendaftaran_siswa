<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('admin_activity_logs')) {
            Schema::create('admin_activity_logs', function (Blueprint $table) {
                $table->id();
                $table->string('admin_name')->default('Admin');
                $table->string('action_type', 50);
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('badge_label', 50)->nullable();
                $table->string('badge_color', 30)->default('blue');
                $table->string('icon', 30)->default('activity');
                $table->string('no_registrasi', 50)->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_activity_logs');
    }
};
