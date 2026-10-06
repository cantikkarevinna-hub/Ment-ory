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
        Schema::create('deleted_items', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang');
            $table->string('no_seri');
            $table->string('barcode')->nullable();
            $table->integer('jumlah');
            $table->string('keterangan')->nullable();
            $table->date('tanggal');
            $table->string('status');
            $table->timestamp('deleted_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deleted_items');
    }
};
