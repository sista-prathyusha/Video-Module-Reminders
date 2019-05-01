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
        	"HTML Module 1" => "1",
        	"HTML Module 2" => "2",
        	"HTML Module 3" => "3",
        	"HTML Module 4" => "4",
        	"HTML Module 5" => "5",
        	"HTML Module 6" => "6",
        	"HTML Module 7" => "7",
        	"CSS Module 1" => "1",
        	"CSS Module 2" => "2",
        	"CSS Module 3" => "3",
        	"CSS Module 4" => "4",
        	"CSS Module 5" => "5",
        	"CSS Module 6" => "6",
        	"CSS Module 7" => "7",
        	"Javascript Module 1" => "1",
        	"Javascript Module 2" => "2",
        	"Javascript Module 3" => "3",
        	"Javascript Module 4" => "4",
        	"Javascript Module 5" => "5",
        	"Javascript Module 6" => "6",
        	"Javascript Module 7" => "7",

        ];
        foreach ($display_order as $key => $order) {
        	Module::where('name', $key)->update(['display_order' => $order]);
        }
    }
}
