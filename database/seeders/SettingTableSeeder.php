<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data=array(
            'description'=>"Adultos jóvenes y niños en Lima",
            'short_des'=>"Clases de Baile de SALSA, BACHATA, MERENGUE, CUMBIA, VALS Y OTROS.",
            'photo' => 'upload/1769198234786298.jpg',
            'logo'=>'upload/1769198234787676.png',
            'address'=>"Avenida Abancay 162 oficina 305 tecer piso, Lima, Peru",
            'email'=>"ritmostar@hotmail.com",
            'phone'=>"( +51 ) 996 508 117",
        );
        DB::table('settings')->insert($data);
    }
}
