<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\SuperUser;

class SuperUsersTableSeeder extends Seeder
{
    public function run()
    {
        SuperUser::factory()->count(10)->create();
    }
}

