<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfileSeeder extends Seeder
{
    public function run()
    {
        $profiles = [
            [
                'name' => 'Jessica',
                'age' => 24,
                'location' => 'New York',
                'bio' => 'Coffee addict ☕ | Designer 🎨',
            ],
            [
                'name' => 'David',
                'age' => 28,
                'location' => 'Brooklyn',
                'bio' => 'Software Engineer. I like clean code.',
            ],
            [
                'name' => 'Sarah',
                'age' => 22,
                'location' => 'Manhattan',
                'bio' => 'Student @ NYU. Love hiking 🌲',
            ],
             [
                'name' => 'Michael',
                'age' => 30,
                'location' => 'Queens',
                'bio' => 'Chef 👨‍🍳. I make the best pasta.',
            ],
        ];

        foreach ($profiles as $data) {
            $profileId = DB::table('profiles')->insertGetId(array_merge($data, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            // Add images from Unsplash
            DB::table('profile_images')->insert([
                [
                    'profile_id' => $profileId, 
                    'image_url' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400', 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
                [
                    'profile_id' => $profileId, 
                    'image_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400', 
                    'created_at' => now(), 
                    'updated_at' => now()
                ],
            ]);
        }
    }
}