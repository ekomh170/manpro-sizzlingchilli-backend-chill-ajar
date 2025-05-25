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
            'MANAJEMEN PROYEK',
            'KECERDASAN ARTIFISIAL',
            'CLOUD COMPUTING',
            'ETIKA PROFESI',
            'KEAMANAN KOMPUTER DAN JARINGAN',
            'JAMINAN KUALITAS PERANGKAT LUNAK',
            'PEMROGRAMAN FRONTEND',
            'VISUALISASI DATA',
            'REKAYASA PERANGKAT LUNAK',
            'BIG DATA',
            'KEWIRAUSAHAAN',
            'BAHASA INGGRIS 2',
            'TEORI BAHASA DAN OTOMATA',
            'PEMROGRAMAN BACKEND',
            'POLA DESAIN PERANGKAT LUNAK',
            'KEAMANAN WEB',
        ];
        $deskripsiKursus = [
            'MANAJEMEN PROYEK' => 'Belajar teknik perencanaan, pelaksanaan, dan evaluasi proyek IT secara profesional.',
            'KECERDASAN ARTIFISIAL' => 'Mengenal konsep, algoritma, dan aplikasi AI dalam dunia nyata.',
            'CLOUD COMPUTING' => 'Penerapan komputasi awan untuk solusi bisnis dan pengembangan aplikasi modern.',
            'ETIKA PROFESI' => 'Memahami etika, tanggung jawab, dan profesionalisme di bidang teknologi informasi.',
            'KEAMANAN KOMPUTER DAN JARINGAN' => 'Dasar-dasar keamanan sistem, jaringan, dan perlindungan data digital.',
            'JAMINAN KUALITAS PERANGKAT LUNAK' => 'Teknik pengujian, validasi, dan jaminan kualitas software.',
            'PEMROGRAMAN FRONTEND' => 'Membangun antarmuka web modern dengan HTML, CSS, dan JavaScript.',
            'VISUALISASI DATA' => 'Mengolah dan menampilkan data secara visual untuk insight bisnis.',
            'REKAYASA PERANGKAT LUNAK' => 'Prinsip, metodologi, dan praktik pengembangan perangkat lunak skala besar.',
            'BIG DATA' => 'Pengelolaan, analisis, dan pemanfaatan data berukuran besar.',
            'KEWIRAUSAHAAN' => 'Membangun mindset dan skill bisnis di bidang teknologi.',
            'BAHASA INGGRIS 2' => 'Peningkatan kemampuan bahasa Inggris untuk dunia profesional IT.',
            'TEORI BAHASA DAN OTOMATA' => 'Dasar teori komputasi, automata, dan bahasa formal.',
            'PEMROGRAMAN BACKEND' => 'Membangun API dan logika server dengan framework backend populer.',
            'POLA DESAIN PERANGKAT LUNAK' => 'Menerapkan design pattern untuk solusi software yang scalable.',
            'KEAMANAN WEB' => 'Strategi dan teknik melindungi aplikasi web dari serangan.',
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
                    'gayaMengajar' => $idx % 2 === 0 ? 'online' : 'offline',
                    'fotoKursus' => 'foto_kursus/kursus_' . ($idx + 1) . '.jpg',
                ];
            }
        }
        foreach ($kursusList as $kursus) {
            Kursus::create($kursus);
        }
    }
}
