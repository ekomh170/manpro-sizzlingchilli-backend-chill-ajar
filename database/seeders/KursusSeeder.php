<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kursus;
use App\Models\Mentor;

class KursusSeeder extends Seeder
{
    public function run()
    {
        $mentorIds = Mentor::pluck('id')->toArray();
        $namaKursusList = [
            'Manajemen Proyek',
            'Kecerdasan Artifisial',
            'Cloud Computing',
            'Etika Profesi',
            'Keamanan Komputer dan Jaringan',
            'Jaminan Kualitas Perangkat Lunak',
            'Pemrograman Frontend',
            'Visualisasi Data',
            'Rekayasa Perangkat Lunak',
            'Big Data',
            'Kewirausahaan',
            'Bahasa Inggris',
            'Teori Bahasa dan Otomata',
            'Pemrograman Backend',
            'Pola Desain Perangkat Lunak',
            'Keamanan Web',
        ];
        $deskripsiKursus = [
            'Manajemen Proyek' => 'Belajar teknik perencanaan, pelaksanaan, dan evaluasi proyek IT secara profesional.',
            'Kecerdasan Artifisial' => 'Mengenal konsep, algoritma, dan aplikasi AI dalam dunia nyata.',
            'Cloud Computing' => 'Penerapan komputasi awan untuk solusi bisnis dan pengembangan aplikasi modern.',
            'Etika Profesi' => 'Memahami etika, tanggung jawab, dan profesionalisme di bidang teknologi informasi.',
            'Keamanan Komputer dan Jaringan' => 'Dasar-dasar keamanan sistem, jaringan, dan perlindungan data digital.',
            'Jaminan Kualitas Perangkat Lunak' => 'Teknik pengujian, validasi, dan jaminan kualitas software.',
            'Pemrograman Frontend' => 'Membangun antarmuka web modern dengan HTML, CSS, dan JavaScript.',
            'Visualisasi Data' => 'Mengolah dan menampilkan data secara visual untuk insight bisnis.',
            'Rekayasa Perangkat Lunak' => 'Prinsip, metodologi, dan praktik pengembangan perangkat lunak skala besar.',
            'Big Data' => 'Pengelolaan, analisis, dan pemanfaatan data berukuran besar.',
            'Kewirausahaan' => 'Membangun mindset dan skill bisnis di bidang teknologi.',
            'Bahasa Inggris' => 'Peningkatan kemampuan bahasa Inggris untuk dunia profesional IT.',
            'Teori Bahasa dan Otomata' => 'Dasar teori komputasi, automata, dan bahasa formal.',
            'Pemrograman Backend' => 'Membangun API dan logika server dengan framework backend populer.',
            'Pola Desain Perangkat Lunak' => 'Menerapkan design pattern untuk solusi software yang scalable.',
            'Keamanan Web' => 'Strategi dan teknik melindungi aplikasi web dari serangan.',
        ];
        shuffle($namaKursusList);
        // Setiap mentor akan dapat beberapa kursus dari daftar
        $kursusList = [];
        $mentorCount = count($mentorIds);
        $kursusCount = count($namaKursusList);
        $kursusPerMentor = (int) ceil($kursusCount / max($mentorCount, 1));
        $idx = 0;
        foreach ($mentorIds as $mentorId) {
            for ($j = 0; $j < $kursusPerMentor && $idx < $kursusCount; $j++, $idx++) {
                $nama = $namaKursusList[$idx];
                $kursusList[] = [
                    'namaKursus' => $nama,
                    'deskripsi' => $deskripsiKursus[$nama] ?? ('Deskripsi untuk ' . $nama),
                    'mentor_id' => $mentorId,
                    'fotoKursus' => 'foto_kursus/default.jpg', // default semua
                ];
            }
        }
        foreach ($kursusList as $kursus) {
            Kursus::create($kursus);
        }
    }
}
