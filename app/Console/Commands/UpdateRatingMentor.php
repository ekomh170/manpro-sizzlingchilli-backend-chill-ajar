<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Mentor;
use App\Models\Testimoni;

class UpdateRatingMentor extends Command
{
    protected $signature = 'mentor:update-rating';
    protected $description = 'Update rating semua mentor berdasarkan rata-rata testimoni';

    public function handle()
    {
        $mentors = Mentor::all();
        $updated = 0;
        foreach ($mentors as $mentor) {
            $testimonis = $mentor->testimoni()->whereNotNull('rating')->pluck('rating');
            if ($testimonis->count() > 0) {
                $avg = round($testimonis->avg(), 2);
                $mentor->rating = $avg;
                $mentor->save();
                $updated++;
            }
        }
        $this->info("Update rating selesai. Mentor diupdate: $updated");
        return 0;
    }
}
