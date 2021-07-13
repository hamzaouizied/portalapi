<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    private $userData = [];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=0; $i < 10; $i++) {
            $userData[] = [
                'name'     => Str::random(10),
                'email'    => 'test'.Str::random(2).'@gmail.com',
                'password' => Hash::make('password')
            ];
        }

        foreach ($userData as $user) {
            User::create($user);
        }
    }
}
