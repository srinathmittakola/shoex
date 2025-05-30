<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Banner::insert([
            ['page_name' => 'Main Page', 'image' => 'mainBanner.jpg'],
            ['page_name' => 'Nike Page', 'image' => 'nikebanner.jpg'],
            ['page_name' => 'Adidas Page', 'image' => 'adidasbanner.jpg'],
            ['page_name' => 'Puma Page', 'image' => 'pumabanner.jpg'],
            ['page_name' => 'SKETCHERS  PAGE', 'image' => 'sketchersBanner.jpg'],
            ['page_name' => 'SPARX  PAGE', 'image' => 'sparxBanner.jpg'],
            ['page_name' => 'REDATAP  PAGE', 'image' => 'redtapBanner.jpg'],
            ['page_name' => 'BATA  PAGE', 'image' => 'batabanner.jpg'],
        ]);
    }
}
