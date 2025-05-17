<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    // Menentukan nama tabel yang digunakan oleh model ini
    protected $table = 'admin';

    // Kolom-kolom yang dapat diisi secara massal
    protected $fillable = [
        'user_id', // Menyimpan ID user yang terkait
    ];

    /**
     * Relasi dengan model User
     * Menunjukkan bahwa setiap Admin berhubungan dengan satu User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
