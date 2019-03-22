<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
	/**
	 * Returns the next module to be watched by the user
	 * @return [Module] [Next module based on display_order]
	 */
   	public function getNextModule(){
   		$order = $this->display_order;
   		$order = $order + 1;
   		$nextModule = Module::where('display_order', $order)->where('course_key',$this->course_key)->first();
   		return $nextModule;
   }

}
