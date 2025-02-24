<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model {
    use HasFactory;

    protected $fillable = ['project_id', 'isi', 'tanggal'];

    public function project() {
        return $this->belongsTo(Project::class);
    }
}
