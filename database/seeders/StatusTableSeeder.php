<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Status;// 引入微博模型

class StatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Status::factory()->count(100)->create();// 微博模型调用模型工厂，创建100条假数据
    }
}
