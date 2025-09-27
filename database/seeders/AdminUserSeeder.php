<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['telegram_user_id' => config('telegram.user.user_id')],
            [
                'name' => config('telegram.user.name'),
                'email' => config('telegram.user.email'),
                'password' => config('telegram.user.password'),
            ]
        );
        $user->is_admin = true;
        $user->save();
    }
}
