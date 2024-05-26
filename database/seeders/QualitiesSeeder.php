<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QualitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $qualities = [
            ['name' => 'Consumer Grade', 'color' => '#B0C3D9'],
            ['name' => 'Industrial Grade', 'color' => '#5E98D9'],
            ['name' => 'Mil-Spec Grade', 'color' => '#4B69FF'],
            ['name' => 'Restricted', 'color' => '#8847FF'],
            ['name' => 'Classified', 'color' => '#D32CE6'],
            ['name' => 'Covert', 'color' => '#EB4B4B'],
            ['name' => 'Contraband', 'color' => '#E4AE39'],
            ['name' => 'Base Grade', 'color' => '#A0A0A0'],
            ['name' => 'High Grade', 'color' => '#0000FF'],
            ['name' => 'Remarkable', 'color' => '#FF69B4'],
            ['name' => 'Exotic', 'color' => '#FFD700'],
            ['name' => 'Extraordinary', 'color' => '#FF4500'],
            ['name' => 'Distinguished', 'color' => '#FFD700'],
            ['name' => 'Exceptional', 'color' => '#8A2BE2'],
            ['name' => 'Superior', 'color' => '#32CD32'],
            ['name' => 'Master', 'color' => '#FF0000']
        ];

        foreach ($qualities as $quality) {
            DB::table('qualities')->updateOrInsert(
                ['name' => $quality['name']], // Condition to check
                [
                    'color' => $quality['color'], // Add color field
                    'created_at' => now(),
                    'updated_at' => now()
                ] // Values to update or insert
            );
        }
    }
}
