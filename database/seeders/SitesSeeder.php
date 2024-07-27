<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SitesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sites = [
            ['name' => '6profis', 'site_url' => 'https://www.6profis.de/', 'script' => 'sixprofis.js'],
            ['name' => 'Erobella', 'site_url' => 'https://erobella.com/', 'script' => 'erobella.js'],
            ['name' => 'Ladies', 'site_url' => 'https://www.ladies.de/', 'script' => 'ladies.js'],
            ['name' => 'Modelle-Hamburg', 'site_url' => 'https://www.modelle-hamburg.de/', 'script' => 'modellehamburg.js'],
            ['name' => 'Erotika', 'site_url' => 'https://erotik.markt.de/', 'script' => 'erotik.js'],
            ['name' => 'test', 'site_url' => 'https://test.de/', 'script' => 'test.js']
        ];

        foreach ($sites as $site)
            Site::create(
                $site
            );
    }
}
