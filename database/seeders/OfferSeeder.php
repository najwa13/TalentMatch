<?php

namespace Database\Seeders;

use App\Models\Offer;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OfferSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'rh@talentmatch.com'],
            [
                'name' => 'RH TalentMatch',
                'password' => bcrypt('password'),
            ]
        );

        Offer::factory()->count(5)->for($user)->create();

        Offer::factory()->count(3)->for($user)->withMultipleSkills()->create();
    }
}
