<?php

namespace App\Http\Controllers;

use App\Http\Helpers\InfusionsoftHelper;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Request;
use Response;
use App\ModuleReminderTags;
use App\User;
use App\Module;
use Auth;

class ApiController extends Controller
{
    // Todo: Module reminder assigner

    private function exampleCustomer(){

        $infusionsoft = new InfusionsoftHelper();

        $uniqid = uniqid();

        $infusionsoft->createContact([
            'Email' => $uniqid.'@test.com',
            "_Products" => 'ipa,iea'
        ]);

        $user = User::create([
            'name' => 'Test ' . $uniqid,
            'email' => $uniqid.'@test.com',
            'password' => bcrypt($uniqid)
        ]);

        // attach IPA M1-3 & M5
        $user->completed_modules()->attach(Module::where('course_key', 'ipa')->limit(3)->get());
        $user->completed_modules()->attach(Module::where('name', 'IPA Module 5')->first());
        //Prats added
        $user->completed_modules()->attach(Module::where('name', 'IEA Module 4')->first());
        $user->completed_modules()->attach(Module::where('name', 'IEA Module 6')->first());


        return $user;
    }

    // public function getCourseTags(){
    //     $infusionsoft = new InfusionsoftHelper();
    //     $reminderTags = $infusionsoft->getAllTags();
    //     foreach ($reminderTags->toArray() as $tag) {
    //         (new ModuleReminderTags())->createNew($tag);
    //     }
    // }
    public function createUser(){
        $user = $this->exampleCustomer();
        return $user;
    }
    public function setModuleReminder(Request $request){
        $email = $request->email;
        $user = User::where('email', $email)->first();
        //$infusionsoft = new InfusionsoftHelper();
        //Test products
        $test = "ipa,iea";
        $status = null;
        $message = null;
        $toDo = null;
        $filtered = array();
        $modules = collect([]);
        //$infusionsoft->getContact($email);
        
        //Extracting completed modules for the user
        $products = preg_split('/[\s,]+/', $contact);
        $completed = $user->completed_modules()->orderBy('pivot_updated_at', 'desc')->get();
        foreach($products as $product){
            $modules = collect(Module::where('course_key', $product)->get())->pluck('id');
            if($completed->isEmpty()){
                $toDo = $modules->first();
                return $toDo;
            }
            $uncompleted = $modules->diff($completed->pluck('id'));
            if(count($uncompleted) > 0){
                $lastCompleted = $completed->first();

                //Extract the next module ID
                do{
                   $toDo = $lastCompleted->getNextModuleId(); 
                   return $toDo;
                }while(!$completed->has($toDo));  
            }else{
                $toDo = -1;
                return "To do: ".$toDo;
            }
        }

        return "To do: ".$toDo;
    }

}
