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
    /**
     * [setModuleReminder description]
     * @param Request $request [description]
     */
    public function setModuleReminder(Request $request){
        $email = $request->email;
        $user = User::where('email', $email)->first();
        //$infusionsoft = new InfusionsoftHelper();
        //Test products
        $test = "ipa,iea,iaa";
        $status = null;
        $message = null;
        $reminderTag = null;
        $modules = collect([]);
        //$infusionsoft->getContact($email);
        
        //Extracting completed modules for the user
        $products = preg_split('/[\s,]+/', $test);
        
        $nextModuleOfInterest = null;
        foreach($products as $product){
            //TO DO
            $nextModuleOfInterest = $this->getNextModuleOfInterest($user, $product);
            if($nextModuleOfInterest)
                break;
        }

        dd($nextModuleOfInterest);
    }

    /**
     * Extracts next module of interest to trigger email sequence on, for a given product based on what user has already completed
     * @return Module Next module that we want the user to view
     */
    private function getNextModuleOfInterest($user, $product){

        $nextModuleOfInterest = null;
        
        //Get completed modules for the product
        $completed = $user->completed_modules()->where('course_key', $product)->orderBy('pivot_updated_at', 'desc')->get();
            
        //Get all modules for that product
        $modules = Module::where('course_key', $product)
                            ->orderBy('display_order')
                            ->get();
        
        //If there are no completed modules, then return the first module of the first product
        if($completed->isEmpty()){
            $nextModuleOfInterest = $modules->first();
            return $nextModuleOfInterest;
        }

        //Compute uncompleted modules for the product
        $uncompleted = $modules->diff($completed);

        //If there are any uncompleted modules, we find the last completed module and compute the next module that is not completed
        if(count($uncompleted) >= 0){
            $lastCompleted = $completed->first();
            
            do{
                $lastCompleted = $lastCompleted->getNextModule();
            }while($lastCompleted && $completed->contains($lastCompleted));
            
            if($lastCompleted){ 
                $nextModuleOfInterest = $lastCompleted; 
                return $nextModuleOfInterest;
            }                   
        }

        return null;
    }

}
