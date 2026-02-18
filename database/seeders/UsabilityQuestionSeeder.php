<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsabilityQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $instrument = \App\Models\UsabilityInstrument::where(
            'name',
            'System Usability Scale (SUS)'
        )->firstOrFail();

        $questions = [
            1 => ['Saya ingin menggunakan sistem ini secara sering.', 'positive'],
            2 => ['Saya merasa sistem ini terlalu kompleks.', 'negative'],
            3 => ['Saya merasa sistem ini mudah digunakan.', 'positive'],
            4 => ['Saya membutuhkan bantuan teknis untuk dapat menggunakan sistem ini.', 'negative'],
            5 => ['Saya merasa berbagai fungsi dalam sistem ini terintegrasi dengan baik.', 'positive'],
            6 => ['Saya merasa terdapat terlalu banyak inkonsistensi dalam sistem ini.', 'negative'],
            7 => ['Saya merasa kebanyakan orang akan mudah mempelajari penggunaan sistem ini dengan cepat.', 'positive'],
            8 => ['Saya merasa sistem ini sangat merepotkan untuk digunakan.', 'negative'],
            9 => ['Saya merasa sangat percaya diri saat menggunakan sistem ini.', 'positive'],
            10 => ['Saya perlu mempelajari banyak hal sebelum dapat menggunakan sistem ini.', 'negative'],
        ];

        foreach ($questions as $number => [$text, $polarity]) {
            \App\Models\UsabilityQuestion::updateOrCreate(
                [
                    'usability_instrument_id' => $instrument->id,
                    'number' => $number,
                ],
                [
                    'question' => $text,
                    'polarity' => $polarity,
                    'is_active' => true,
                ]
            );
        }
    }
}
