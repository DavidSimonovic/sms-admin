<?php

namespace Database\Seeders;

use App\Models\Number;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestNumberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $numbers = [
            ['ad_title' => 'David', 'city' => 'Negotin', 'postcode' => '11111', 'number' => '+38163391116', 'site_id' => 6, 'active' => 1, 'bounced' => 0, 'url_id' => 110010001],
            ['ad_title' => 'Chris', 'city' => 'Berlin', 'postcode' => '11111', 'number' => '01747347642', 'site_id' => 6, 'active' => 1, 'bounced' => 0, 'url_id' => 110010001],
            ['ad_title' => 'Bart', 'city' => 'Merl', 'postcode' => '11111', 'number' => '+491774013569', 'site_id' => 6, 'active' => 1, 'bounced' => 0, 'url_id' => 110010001],
        ];

        foreach ($numbers as $number)
            Number::create(
                $number
            );
    }
}
