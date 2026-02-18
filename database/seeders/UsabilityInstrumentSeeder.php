<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsabilityInstrumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\UsabilityInstrument::updateOrCreate(
            ['name' => 'System Usability Scale (SUS)'],
            [
                'description' => 'Instrumen standar untuk mengukur usability sistem.',
                'is_active' => true,
            ]
        );
    }
}
