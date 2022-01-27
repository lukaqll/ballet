<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User;
        $user->create([
            'name' => 'Admin',
            'email' => 'app@elleganceballet.com.br',
            'password' => bcrypt('123456'),
            'is_admin' => 1,
            'status' => 'A'
        ]);
    }
}
