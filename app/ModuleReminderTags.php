<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ModuleReminderTags extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'module_reminder_tags';

 	protected $fillable = [
 			'tag_id', 'tag_name', 'tag_description'];

 	/**
 	 * Creates a new module reminder tag
 	 * @param  [Object] $tag [Contains tag id, name and description]
 	 * @return [none]       	 
 	 * */
 	public function createNew($tag){
 		$this->tag_id = $tag->id;
        $this->tag_name = $tag->name;
   	    $this->tag_description = $tag->description;
        $this->save();
 	}
}
