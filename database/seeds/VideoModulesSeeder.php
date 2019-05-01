<?php

use Illuminate\Database\Seeder;
use App\Module;

class VideoModulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('Modules')->truncate();
        for ($i = 1; $i <= 7; $i++){
            Module::insert([
                [
                    'course_key' => 'html',
                    'name' => 'HTML Module ' . $i
                ],

                [
                    'course_key' => 'css',
                    'name' => 'CSS Module ' . $i
                ],

                [
                    'course_key' => 'js',
                    'name' => 'Javascript Module ' . $i
                ]
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
