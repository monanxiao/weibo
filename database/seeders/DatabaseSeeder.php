<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(UsersTableSeeder::class);//执行填充用户
        $this->call(StatusTableSeeder::class);// 填充微博假数据

        Model::reguard();
        // \App\Models\User::factory(10)->create();
    }
}
