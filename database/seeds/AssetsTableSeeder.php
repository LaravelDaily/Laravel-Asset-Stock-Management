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
            'gloves',
            'masks',
            'respirators',
            'protective overalls',
            'protective glasses',
        ];

        foreach ($assets as $asset) {
            factory(Asset::class)->create([
                'name'        => $asset,
                'description' => $asset
            ]);
        }

        $teams  = Team::all();
        $assets = Asset::all();

        foreach ($teams as $team) {
            foreach ($assets as $asset) {
                factory(\App\Stock::class)->create([
                    'asset_id' => $asset->id,
                    'team_id'  => $team->id,
                ]);
            }
        }
    }
}
