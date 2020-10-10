<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $rand_pas = Hash::make('test@123');
        $rand_tok = Hash::make('cistpk@123');
        for ($i=1; $i < 10; $i++) { 
            DB::table('users')->insert([
                'id' => $i,
                'login_id' => 'test'.$i,
                'email' => 'test'.$i.'@test.com',
                'password' => $rand_pas,
                'token' => $rand_tok,
                'created_at'=>date('Y-m-d H:i:s')
            ]);
    	}
    }
}
