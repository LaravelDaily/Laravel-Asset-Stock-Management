<?php

use App\Asset;
use App\Team;
use Illuminate\Database\Seeder;

/**
 * Class AssetsTableSeeder
 */
class AssetsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $assets = [
            'Adobo',
            'Afritada',
            'Bulalo',
            'Beef Mami',
            'Beef Tapa',
            'Chicken Fried',
            'Chopsuey',
            'Corned Beef',
            'Dinuguan',
            'Menudo',
            'Mechado',
            'Sinigang',
        ];

        foreach ($assets as $asset) {
            Asset::factory()->create([
                'name'        => $asset,
                'description' => $asset
            ]);
        }
    }
}
