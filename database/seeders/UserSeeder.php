<?php

namespace Database\Seeders;

use App\Helpers\Functions;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'      => 'User 1',
            'email'     => 'admin@admin.com',
            'password'  => bcrypt('12345678'),
            'uid'       => Functions::generateUID('U', User::class),
        ]);

        User::create([
            'name'      => 'User 2',
            'email'     => 'admin2@admin.com',
            'password'  => bcrypt('12345678'),
            'uid'       => Functions::generateUID('U', User::class),
        ]);
    }
}
