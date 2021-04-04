<?php

use App\Team;
use App\User;
use Illuminate\Database\Seeder;

class TeamsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SET TO 1 LOOP ONLY
        for ($i = 0; $i < 1; $i++) {
            $randomNumber = rand(123, 789);

            $team = Team::factory()->create([
                'name' => "InventoryStore",
            ]);

            $manager = User::factory()->create([
                'name'           => "Manager $randomNumber",
                'email'          => "manager@admin.com",
                'password'       => bcrypt('password'),
                'team_id'        => $team->id,
                'remember_token' => null,
            ]);
            $manager->roles()->sync(2);

            $staff = User::factory()->create([
                'name'           => "Staff $randomNumber",
                'email'          => "staff@admin.com",
                'password'       => bcrypt('password'),
                'team_id'        => $team->id,
                'remember_token' => null,
            ]);
            $staff->roles()->sync(3);
        }
    }
}
