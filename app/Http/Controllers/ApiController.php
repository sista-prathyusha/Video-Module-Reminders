<?php

namespace App\Http\Controllers;

use App\Http\Helpers\InfusionsoftHelper;
use Illuminate\Http\Request;
use Response;
use App\ModuleReminderTags;
use App\User;
use App\Module;
use Auth;

class ApiController extends Controller
{
    // Todo: Module reminder assigner

    private function exampleCustomer(){

        $uniqid = uniqid();
        $user = User::create([
            'name' => 'Test ' . $uniqid,
            'email' => $uniqid.'@test.com',
            'password' => bcrypt($uniqid)
        ]);
        $result = [
            'user' => $user,
            'contactId' => $uniqid
        ];
        return $result;
    }

    public function createUser(){
        return $this->exampleCustomer();
    }
    /**
     * Adds an appropriate reminder tag to infusionsoft API
     * @param Request $request 
     * return Json response with status and message
     */
    public function setModuleReminder(Request $request){
        $email = $request->email;
        $contactId = $request->contactId;
        $user = User::where('email', $email)->first(); 
        $result = null;
        $status = null;
        $message = null;
        $reminderTag = null;
        $modules = collect([]);    

        //Extracting completed modules for the user
        $products = preg_split('/[\s,]+/', $request->products);
        
        $nextModuleOfInterest = null;
        foreach($products as $product){
            //Get next uncompleted module
            $nextModuleOfInterest = $this->getNextModuleOfInterest($user, $product);
            if($nextModuleOfInterest)
                break;
        }

        $moduleReminderTag = ModuleReminderTags::forModule($nextModuleOfInterest)->first();
        //If module reminder tag is not null, function will return succes and the corresponding tag
        if($moduleReminderTag){
            $result = ['success' => true, 'message' => $moduleReminderTag->tag_name];
        }else{ 
        //Returns false if no tag is found
            $result = ['success' => false, 'message' => 'Reminder tag not found'];
        } 
        return Response::json($result);    
    }

    /**
     * Extracts next module of interest to trigger email sequence on, for a given product based on what user has already completed
     * @return Module - Next module that we want the user to view
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

        return $nextModuleOfInterest;
    }


}
