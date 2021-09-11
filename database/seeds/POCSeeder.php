<?php

use Illuminate\Database\Seeder;
use App\Models\Poc;
class POCSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pocs')->truncate();
        DB::table('pocs')->insert([
            [ 
               'name' => 'Line Manager'
            ],
            [
                'name' => 'POC'
            ]
        ]);
    }
}
