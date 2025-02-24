<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model {
    use HasFactory;

    protected $fillable = ['nama', 'deskripsi', 'user_id'];

    public function mahasiswa() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function laporans() {
        return $this->hasMany(Laporan::class);
    }
}
