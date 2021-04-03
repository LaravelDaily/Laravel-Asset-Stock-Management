<?php

namespace Database\Seeders;

use App\Branch;
use Illuminate\Database\Seeder;

class BranchesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $branches = [
            'Batangas',
            'Cavite',
            'Laguna',
        ];

        foreach ($branches as $branch) {
            Branch::factory()->create([
                'name'        => $branch
            ]);
        }
    }
}
