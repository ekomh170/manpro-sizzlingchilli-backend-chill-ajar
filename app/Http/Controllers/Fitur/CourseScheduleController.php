<?php

namespace App\Http\Controllers\Fitur;

use App\Models\CourseSchedule;
use Illuminate\Http\Request;
use App\Http\Controllers\Fitur\FiturController;

class CourseScheduleController extends FiturController
{
    /**
     * Menampilkan daftar semua jadwal kursus
     */
    public function index()
    {
        return response()->json(CourseSchedule::with('course')->get());
    }

    /**
     * Menyimpan jadwal kursus baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:course,id',
            'tanggal' => 'required|date',
            'waktu' => 'required',
            'keterangan' => 'nullable|string',
        ]);
        $jadwal = CourseSchedule::create($request->all());
        return response()->json($jadwal, 201);
    }

    /**
     * Menampilkan detail jadwal kursus tertentu
     */
    public function show($id)
    {
        $jadwal = CourseSchedule::with('course')->findOrFail($id);
        return response()->json($jadwal);
    }

    /**
     * Memperbarui jadwal kursus
     */
    public function update(Request $request, $id)
    {
        $jadwal = CourseSchedule::findOrFail($id);
        $jadwal->update($request->all());
        return response()->json($jadwal);
    }

    /**
     * Menghapus jadwal kursus
     */
    public function destroy($id)
    {
        $jadwal = CourseSchedule::findOrFail($id);
        $jadwal->delete();
        return response()->json(null, 204);
    }
}
