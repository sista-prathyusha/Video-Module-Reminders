<?php

use Illuminate\Database\Seeder;
use App\Module;

class AddDisplayOrderToModules extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $display_order = [
        	"IPA Module 1" => "1",
        	"IPA Module 2" => "2",
        	"IPA Module 3" => "3",
        	"IPA Module 4" => "4",
        	"IPA Module 5" => "5",
        	"IPA Module 6" => "6",
        	"IPA Module 7" => "7",
        	"IEA Module 1" => "1",
        	"IEA Module 2" => "2",
        	"IEA Module 3" => "3",
        	"IEA Module 4" => "4",
        	"IEA Module 5" => "5",
        	"IEA Module 6" => "6",
        	"IEA Module 7" => "7",
        	"IAA Module 1" => "1",
        	"IAA Module 2" => "2",
        	"IAA Module 3" => "3",
        	"IAA Module 4" => "4",
        	"IAA Module 5" => "5",
        	"IAA Module 6" => "6",
        	"IAA Module 7" => "7",

        ];
        foreach ($display_order as $key => $order) {
        	Module::where('name', $key)->update(['display_order' => $order]);
        }
    }
}
