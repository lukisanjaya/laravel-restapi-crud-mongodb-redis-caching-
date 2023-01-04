<?php

use App\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name'     => 'Admin',
            'username' => 'admin',
            'email'    => 'admin@gmail.com',
            'password' => 12345678,
            'roles'    => 'ADMIN'
        ]);

        User::create([
            'name'     => 'Reporter',
            'username' => 'reporter',
            'email'    => 'reporter@gmail.com',
            'password' => 12345678,
            'roles'    => 'REPORTER'
        ]);

        User::create([
            'name'     => 'Redaktur',
            'username' => 'redaktur',
            'email'    => 'redaktur@gmail.com',
            'password' => 12345678,
            'roles'    => 'REDAKTUR'
        ]);
    }
}
