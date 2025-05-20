<?php

namespace App\Http\Controllers\Fitur;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Fitur\FiturController;

class CourseController extends FiturController
{
    /**
     * Menampilkan daftar mata kuliah
     */
    public function index()
    {
        return response()->json(Course::all());
    }

    /**
     * Menambahkan mata kuliah baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'namaCourse' => 'required|string',
            'deskripsi' => 'nullable|string',
            'mentor_id' => 'required|exists:mentor,id',
        ]);

        // Menambahkan mata kuliah baru
        $course = Course::create($request->all());

        return response()->json($course, 201);
    }

    /**
     * Menampilkan detail course tertentu
     */
    public function show($id)
    {
        $course = Course::with('mentor.user')->findOrFail($id);
        return response()->json($course);
    }

    /**
     * Memperbarui mata kuliah
     */
    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        $course->update($request->all());

        return response()->json($course);
    }

    /**
     * Menghapus mata kuliah
     */
    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return response()->json(null, 204);
    }

    /**
     * Mengunggah gambar kursus
     */
    public function uploadGambarKursus(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        if ($request->hasFile('gambar_kursus')) {
            $file = $request->file('gambar_kursus');
            $path = $file->store('gambar_kursus', 'public');
            $course->gambar_kursus = $path;
            $course->save();
            return response()->json(['message' => 'Gambar kursus berhasil diunggah', 'gambar_kursus' => $path]);
        }
        return response()->json(['message' => 'Tidak ada file yang diunggah'], 400);
    }
}
