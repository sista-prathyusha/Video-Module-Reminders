<?php

use Illuminate\Database\Seeder;
use App\Http\Helpers\InfusionsoftHelper;
use App\ModuleReminderTags;

class InfusionsoftTagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('module_reminder_tags')->truncate();
        $infusionsoft = new InfusionsoftHelper();
        $allTags = $infusionsoft->getAllTags();
        foreach ($allTags->toArray() as $key => $tag) {
        	(new ModuleReminderTags())->createNew($tag);
        }
    }
}
