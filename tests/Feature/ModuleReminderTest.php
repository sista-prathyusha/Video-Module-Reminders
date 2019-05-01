<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Helpers\InfusionsoftHelper;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Module;
use App\ModuleReminderTags;
use App\User;
use Carbon;
use DB;

class ModuleReminderTest extends TestCase
{

	protected $newUser = null;
    
    protected function createUser($products){


    	$uniqid = uniqid();
    	$email = $uniqid."@test.com";

		$contactId = $uniqid;

        $user = User::create([
            'name' => 'Test ' . $uniqid,
            'email' => $email,
            'password' => bcrypt($uniqid)
        ]);

        $result = [
            'user' => $user,
            'contactId' => $contactId
        ];

        return $result;

    }

    /**
     * Test to check if "Module reminders completed" tag is returned if all modules are completed
     * @return Json with success and the corresponding reminder tag
     */
    public function testAllModulesCompleted(){

    	$products = "html,css";
        $result = $this->createUser($products);
    	$newUser = $result['user'];
        //Attach all modules to user 
        $newUser->completed_modules()->attach(Module::get());
        $response = $this->json('POST', '/api/module_reminder_assigner', [
            'email' => $newUser->email,
            'contactId' => $result['contactId'],
            'products' => $products
        ]);
        $response->assertStatus(200);
        $response->assertJson(['success'=>true, 'message'=>'Module reminders completed']);
    }

    // /**
    //  * Test to check if first module of first tag is returned if no modules are completed
    //  * @return Json with success and the corresponding reminder tag
    //  */
    public function testNoModulesCompleted(){
    	$products = "css,js";
    	$result = $this->createUser($products);
    	$newUser = $result['user'];
        //No modules are attached to user 
        $response = $this->json('POST', '/api/module_reminder_assigner', ['email' => $newUser->email,
            'contactId' => $result['contactId'],
            'products' => $products]);
        $response->assertStatus(200);
        $response->assertJson(['success'=>true, 'message'=>'Start CSS Module 1 Reminders']);
    }

    /**
     * Test to check if all modules of first product are completed and the next uncompleted module of the second product is returned 
     * @return Json with success and the corresponding reminder tag
     */
    public function testAllModulesOfFirstProductCompleted(){
    	$products = "css,html";
    	$result = $this->createUser($products);
    	$newUser = $result['user'];
        //Attach all first modules  to user
        $newUser->completed_modules()->attach(Module::where('course_key', 'css')->get()); 
        $newUser->completed_modules()->attach(Module::where('name', 'HTML Module 4')->first());
        $response = $this->json('POST', '/api/module_reminder_assigner', ['email' => $newUser->email,
            'contactId' => $result['contactId'],
            'products' => $products]);
        $response->assertStatus(200);
        $response->assertJson(['success'=>true, 'message'=>'Start HTML Module 5 Reminders']);
    }

    /**
     * Test to check if any or last module of the first product is completed and the next uncompleted module of the second product is returned 
     * @return Json with success and the corresponding reminder tag
     */
    public function testLastModulesOfFirstProductCompleted(){
    	$products = "js,html";
    	$result = $this->createUser($products);
    	$newUser = $result['user'];
        //Attach last module of first product to user
        $newUser->completed_modules()->attach(Module::where('course_key', 'js')->limit(4)->get()); 
        $newUser->completed_modules()->attach(Module::where('name', 'Javascript Module 7')->first());
        $newUser->completed_modules()->attach(Module::where('name', 'HTML Module 2')->first());
        $setUpdateTime = $newUser->completed_modules()->where('name', 'Javascript Module 7')->first();		

        $setUpdateTime->pivot->updated_at = Carbon\Carbon::now()->addSeconds(60);
        $setUpdateTime->pivot->save();
        $response = $this->json('POST', '/api/module_reminder_assigner', ['email' => $newUser->email,
            'contactId' => $result['contactId'],
            'products' => $products]);
        $response->assertStatus(200);
        $response->assertJson(['success'=>true, 'message'=>'Start HTML Module 3 Reminders']);
    }

    /**
     * Test to check if last modules of the first and second products are completed and the next uncompleted module of the third product is returned 
     * @return Json with success and the corresponding reminder tag
     */
    public function testLastModulesOfFirstSecondProductCompleted(){
    	$products = "css,html,js";
    	$result = $this->createUser($products);
    	$newUser = $result['user'];
        //Attach last module of first and second products to user
        $newUser->completed_modules()->attach(Module::where('course_key', 'css')->limit(4)->get()); 
        $newUser->completed_modules()->attach(Module::where('name', 'CSS Module 7')->first());
        $newUser->completed_modules()->attach(Module::where('course_key', 'html')->limit(2)->get());
        $newUser->completed_modules()->attach(Module::where('name', 'HTML Module 7')->first());
        $setUpdateTime = $newUser->completed_modules()->where('name', 'CSS Module 7')->first();		
        $setUpdateTime->pivot->updated_at = Carbon\Carbon::now()->addSeconds(50);
        $setUpdateTime->pivot->save();
        $setUpdateTime = $newUser->completed_modules()->where('name', 'HTML Module 7')->first();		
        $setUpdateTime->pivot->updated_at = Carbon\Carbon::now()->addSeconds(90);
        $setUpdateTime->pivot->save();
        $response = $this->json('POST', '/api/module_reminder_assigner', ['email' => $newUser->email,
            'contactId' => $result['contactId'],
            'products' => $products]);
        $response->assertStatus(200);
        $response->assertJson(['success'=>true, 'message'=>'Start Javascript Module 1 Reminders']);
    }
}
