<?php

namespace Database\Seeders;

use App\Models\TargetInsect;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TargetInsectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define initial target insects based on the existing hard-coded values
        $insects = [
            ['name' => 'Cockroaches', 'value' => 'cockroaches'],
            ['name' => 'Rodents', 'value' => 'rodents'],
            ['name' => 'Flying Insects', 'value' => 'flying_insects'],
            ['name' => 'Ants', 'value' => 'ants'],
            ['name' => 'Snakes', 'value' => 'snakes'],
            ['name' => 'Scorpions', 'value' => 'scorpions'],
            ['name' => 'Lizard', 'value' => 'lizard'],
            ['name' => 'Bed Bug', 'value' => 'bed_bug'],
            ['name' => 'Termites Before Building', 'value' => 'termites_before'],
            ['name' => 'Termites After Building', 'value' => 'termites_after'],
        ];

        foreach ($insects as $insect) {
            TargetInsect::create([
                'name' => $insect['name'],
                'value' => $insect['value'],
                'slug' => Str::slug($insect['name']),
                'description' => 'Initial ' . $insect['name'] . ' entry',
                'active' => true,
            ]);
        }
    }
}
