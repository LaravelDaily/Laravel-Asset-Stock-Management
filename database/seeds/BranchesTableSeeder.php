<?php

namespace Database\Seeders;

use App\Branch;
use Illuminate\Database\Seeder;

class BranchesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $branches = [
            [
                'id'    => 1,
                'name' => 'Batangas',
            ],
            [
                'id'    => 2,
                'name' => 'Cavite',
            ],
            [
                'id'    => 3,
                'name' => 'Laguna',
            ],
        ];

        Branch::insert($branches);
    }
}
