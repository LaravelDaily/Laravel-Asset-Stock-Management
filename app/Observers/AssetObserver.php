<?php

namespace App\Observers;

use App\Asset;
use App\Stock;
use App\Team;

/**
 * Class AssetObserver
 * @package App\Observers
 */
class AssetObserver
{
    /**
     * Handle the asset "created" event.
     *
     * @param Asset $asset
     * @return void
     */
    public function created(Asset $asset)
    {
        $teams  = Team::all();

        foreach ($teams as $team) {
            Stock::factory()->create([
                'asset_id' => $asset->id,
                'team_id'  => $team->id,
            ]);
        }
    }

    /**
     * Handle the asset "updated" event.
     *
     * @param Asset $asset
     * @return void
     */
    public function updated(Asset $asset)
    {
        //
    }

    /**
     * Handle the asset "deleted" event.
     *
     * @param Asset $asset
     * @return void
     */
    public function deleted(Asset $asset)
    {
        //
    }

    /**
     * Handle the asset "restored" event.
     *
     * @param Asset $asset
     * @return void
     */
    public function restored(Asset $asset)
    {
        //
    }

    /**
     * Handle the asset "force deleted" event.
     *
     * @param Asset $asset
     * @return void
     */
    public function forceDeleted(Asset $asset)
    {
        //
    }
}
