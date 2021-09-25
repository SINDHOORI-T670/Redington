<?php

use Illuminate\Database\Seeder;
use App\User;
class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::table('users')->truncate();
        User::create([
            'name' => 'Admin',
            'email' => 'admin@redington.com',
            'password' => bcrypt('123456'),
            'verify_status' => 1,
            'phone' => '+(974) 95478515',
            'type' => 1,
            'status' => 0
        ]);
    }
}
