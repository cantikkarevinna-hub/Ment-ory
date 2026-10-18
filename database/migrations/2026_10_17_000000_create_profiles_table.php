<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Chantikka Revinna');
            $table->string('employee_id')->default('QC-2026-0808');
            $table->string('role')->default('QC Inspector / Staff PKL');
            $table->string('department')->default('Quality Control (QC)');
            $table->string('location')->default('Plant 1 - Karawang Barat');
            $table->string('email')->default('chantikka.rev@company.com');
            $table->string('phone')->default('+62 85692354292');
            $table->string('photo_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
