<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BrandProfile;

class BrandProfileSeeder extends Seeder
{
    public function run(): void
    {
        BrandProfile::create([
            'tagline' => 'Elevate Your Everyday Style',
            'story' => 'Didirikan pada tahun 2024, HIGH FIVE lahir dari visi sederhana: menciptakan pakaian berkualitas tinggi dengan desain minimalis yang dapat diakses oleh semua orang. Kami percaya bahwa gaya yang baik tidak harus rumit atau mahal.',
            'vision' => 'Menjadi merek fashion direct-to-consumer terkemuka di Indonesia yang menetapkan standar baru untuk kualitas, desain minimalis, dan praktik bisnis yang berkelanjutan.',
            'logo' => null,
            'banner' => 'https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?auto=format&fit=crop&q=80&w=2070',
        ]);
    }
}
