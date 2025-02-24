<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('laporans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade'); // Relasi ke projects
            $table->text('isi'); // Isi laporan
            $table->date('tanggal'); // Tanggal laporan
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('laporans');
    }
};
