<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseSchedule extends Model
{
    use HasFactory;

    protected $table = 'course_schedules';

    protected $fillable = [
        'course_id',
        'tanggal',
        'waktu',
        'keterangan',
    ];

    /**
     * Relasi ke Course
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Relasi ke Session (jika nanti session mengacu ke jadwal)
     */
    public function sessions()
    {
        return $this->hasMany(Session::class);
    }
}
