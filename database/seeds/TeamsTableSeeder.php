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

            $team = factory(Team::class)->create([
                'name' => "Hospital $randomNumber",
            ]);

            $director = factory(User::class)->create([
                'name'           => "Director $randomNumber",
                'email'          => "director$randomNumber@gmail.com",
                'password'       => bcrypt('password'),
                'team_id'        => $team->id,
                'remember_token' => null,
            ]);
            $director->roles()->sync(2);

            $doctor = factory(User::class)->create([
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
