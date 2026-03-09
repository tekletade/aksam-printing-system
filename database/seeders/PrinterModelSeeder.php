<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PrinterModel;

class PrinterModelSeeder extends Seeder
{
    public function run(): void
    {
        $models = [
            // Konica
            [
                'name' => 'Konica Minolta Bizhub C368',
                'brand' => 'Konica',
                'model_number' => 'C368',
                'is_color' => true,
                'is_duplex_supported' => true,
                'default_paper_capacity' => 500,
                'specifications' => json_encode(['speed' => '36 ppm', 'resolution' => '1200x1200 dpi']),
                'supported_media_types' => json_encode(['plain', 'glossy', 'recycled']),
            ],
            [
                'name' => 'Konica Minolta Bizhub C458',
                'brand' => 'Konica',
                'model_number' => 'C458',
                'is_color' => true,
                'is_duplex_supported' => true,
                'default_paper_capacity' => 500,
                'specifications' => json_encode(['speed' => '45 ppm', 'resolution' => '1200x1200 dpi']),
                'supported_media_types' => json_encode(['plain', 'glossy', 'recycled']),
            ],
            [
                'name' => 'Konica Minolta Bizhub 558',
                'brand' => 'Konica',
                'model_number' => '558',
                'is_color' => false,
                'is_duplex_supported' => true,
                'default_paper_capacity' => 500,
                'specifications' => json_encode(['speed' => '55 ppm', 'resolution' => '1200x1200 dpi']),
                'supported_media_types' => json_encode(['plain', 'recycled']),
            ],

            // Canon
            [
                'name' => 'Canon imageRUNNER ADVANCE C5535i',
                'brand' => 'Canon',
                'model_number' => 'C5535i',
                'is_color' => true,
                'is_duplex_supported' => true,
                'default_paper_capacity' => 500,
                'specifications' => json_encode(['speed' => '35 ppm', 'resolution' => '1200x1200 dpi']),
                'supported_media_types' => json_encode(['plain', 'glossy', 'recycled']),
            ],
            [
                'name' => 'Canon imageRUNNER 2525',
                'brand' => 'Canon',
                'model_number' => '2525',
                'is_color' => false,
                'is_duplex_supported' => true,
                'default_paper_capacity' => 500,
                'specifications' => json_encode(['speed' => '25 ppm', 'resolution' => '600x600 dpi']),
                'supported_media_types' => json_encode(['plain', 'recycled']),
            ],

            // HP
            [
                'name' => 'HP LaserJet Enterprise M608',
                'brand' => 'HP',
                'model_number' => 'M608',
                'is_color' => false,
                'is_duplex_supported' => true,
                'default_paper_capacity' => 500,
                'specifications' => json_encode(['speed' => '52 ppm', 'resolution' => '1200x1200 dpi']),
                'supported_media_types' => json_encode(['plain', 'recycled']),
            ],
            [
                'name' => 'HP Color LaserJet Enterprise M652',
                'brand' => 'HP',
                'model_number' => 'M652',
                'is_color' => true,
                'is_duplex_supported' => true,
                'default_paper_capacity' => 500,
                'specifications' => json_encode(['speed' => '47 ppm', 'resolution' => '1200x1200 dpi']),
                'supported_media_types' => json_encode(['plain', 'glossy', 'recycled']),
            ],

            // KIP (Plotters)
            [
                'name' => 'KIP 9900',
                'brand' => 'KIP',
                'model_number' => '9900',
                'is_color' => false,
                'is_duplex_supported' => false,
                'default_paper_capacity' => 100,
                'specifications' => json_encode(['speed' => '6 D-size/min', 'resolution' => '600x600 dpi']),
                'supported_media_types' => json_encode(['bond', 'vellum', 'film']),
            ],
            [
                'name' => 'KIP Color 80',
                'brand' => 'KIP',
                'model_number' => 'C80',
                'is_color' => true,
                'is_duplex_supported' => false,
                'default_paper_capacity' => 100,
                'specifications' => json_encode(['speed' => '4 D-size/min', 'resolution' => '600x600 dpi']),
                'supported_media_types' => json_encode(['bond', 'glossy', 'film']),
            ],
        ];

        foreach ($models as $model) {
            PrinterModel::create($model);
        }
    }
}
