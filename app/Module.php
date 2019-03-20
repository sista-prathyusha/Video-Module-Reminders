<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
   public function getNextModuleId(){
   		$order = $this->display_order;
   		$order = $order + 1;
   		$nextModule = Module::where('display_order', $order)->where('course_key',$this->course_key)->first();
   		return $nextModule->id;
   }

}
