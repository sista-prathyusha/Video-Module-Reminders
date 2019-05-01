<?php

use Illuminate\Database\Seeder;
use App\ModuleReminderTags;
use App\Module;

class ReminderTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('module_reminder_tags')->truncate();
        $allTags = [
            "tag1" => ["tag_id" => 100,
                "tag_name" => "Module reminders completed"],
            "tag2" => ["tag_id" => 101,
                "tag_name" => "Start HTML Module 1 Reminders"],
            "tag3" => ["tag_id" => 102,
                "tag_name" => "Start HTML Module 2 Reminders"],
            "tag4" => ["tag_id" => 103,
                "tag_name" => "Start HTML Module 3 Reminders"],
            "tag5" => ["tag_id" => 104,
                "tag_name" => "Start HTML Module 4 Reminders"],
            "tag6" => ["tag_id" => 105,
                "tag_name" => "Start HTML Module 5 Reminders"],
            "tag7" => ["tag_id" => 106,
                "tag_name" => "Start HTML Module 6 Reminders"],
            "tag8" => ["tag_id" => 107,
                "tag_name" => "Start HTML Module 7 Reminders"],
            "tag9" => ["tag_id" => 108,
                "tag_name" => "Start CSS Module 1 Reminders"],
            "tag10" => ["tag_id" => 109,
                "tag_name" => "Start CSS Module 2 Reminders"],
            "tag11" => ["tag_id" => 110,
                "tag_name" => "Start CSS Module 3 Reminders"],
            "tag12" => ["tag_id" => 111,
                "tag_name" => "Start CSS Module 4 Reminders"],
            "tag13" => ["tag_id" => 112,
                "tag_name" => "Start CSS Module 5 Reminders"],
            "tag14" => ["tag_id" => 113,
                "tag_name" => "Start CSS Module 6 Reminders"],
            "tag15" => ["tag_id" => 114,
                "tag_name" => "Start CSS Module 7 Reminders"],
            "tag16" => ["tag_id" => 115,
                "tag_name" => "Start Javascript Module 1 Reminders"],
            "tag17" => ["tag_id" => 116,
                "tag_name" => "Start Javascript Module 2 Reminders"],
            "tag18" => ["tag_id" => 117,
                "tag_name" => "Start Javascript Module 3 Reminders"],
            "tag19" => ["tag_id" => 118,
                "tag_name" => "Start Javascript Module 4 Reminders"],
            "tag20" => ["tag_id" => 119,
                "tag_name" => "Start Javascript Module 5 Reminders"],
            "tag21" => ["tag_id" => 120,
                "tag_name" => "Start Javascript Module 6 Reminders"],
            "tag22" => ["tag_id" => 121,
                "tag_name" => "Start Javascript Module 7 Reminders"]
        ];
        foreach ($allTags as $tag) {
        	(new ModuleReminderTags())->createNew($tag);
        }
    }
}
