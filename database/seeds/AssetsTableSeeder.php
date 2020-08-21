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
            'shtrafa',
            'silikon',
            'silikon i zi',
            'silikon i bardhe',
        ];

        foreach ($assets as $asset) {
            factory(Asset::class)->create([
                'name'        => $asset,
                'description' => $asset
            ]);
        }
    }
}
