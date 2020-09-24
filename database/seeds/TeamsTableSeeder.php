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
        for ($i = 0; $i < 5; $i++) {
            $randomNumber = rand(123, 789);

            $team = Team::factory()->create([
                'name' => "Hospital $randomNumber",
            ]);

            $director = User::factory()->create([
                'name'           => "Director $randomNumber",
                'email'          => "director$randomNumber@gmail.com",
                'password'       => bcrypt('password'),
                'team_id'        => $team->id,
                'remember_token' => null,
            ]);
            $director->roles()->sync(2);

            $doctor = User::factory()->create([
                'name'           => "Doctor $randomNumber",
                'email'          => "doctor$randomNumber@gmail.com",
                'password'       => bcrypt('password'),
                'team_id'        => $team->id,
                'remember_token' => null,
            ]);
            $doctor->roles()->sync(2);
        }
    }
}
