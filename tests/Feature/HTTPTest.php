<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Helpers\InfusionsoftHelper;
use App\Module;
use App\ModuleReminderTags;
use App\User;
use Carbon;
use DB;

class HTTPTest extends TestCase
{
	// use RefreshDatabase;

	protected $newUser = null;
    /**
     * A basic test example.
     *
     * @return void
     */
    // public function testExample()
    // {
    //    $this->assertTrue(true);
    // }

    private function createUser($products){
    	$infusionsoft = new InfusionsoftHelper();

        $uniqid = uniqid();

        $infusionsoft->createContact([
            'Email' => $uniqid.'@test.com',
            "_Products" => $products
        ]);

        $user = User::create([
            'name' => 'Test ' . $uniqid,
            'email' => $uniqid.'@test.com',
            'password' => bcrypt($uniqid)
        ]);
        return $user;
    }

    /**
     * Test to check if "Module reminders completed" tag is returned if all modules are completed
     * @return Json with success and the corresponding reminder tag
     */
    public function testAllModulesCompleted(){
    	$products = "ipa,iea";
    	$newUser = $this->createUser($products);
        //Attach all modules to user 
        $newUser->completed_modules()->attach(Module::get());
        $response = $this->json('POST', '/api/module_reminder_assigner', ['email' => $newUser->email]);
        $response->assertStatus(200);
        $response->assertJson(['success'=>true, 'message'=>'Module reminders completed']);
    }

    /**
     * Test to check if first module of first tag is returned if no modules are completed
     * @return Json with success and the corresponding reminder tag
     */
    public function testNoModulesCompleted(){
    	$products = "iea,iaa";
    	$newUser = $this->createUser($products);
        //No modules are attached to user 
        $response = $this->json('POST', '/api/module_reminder_assigner', ['email' => $newUser->email]);
        $response->assertStatus(200);
        $response->assertJson(['success'=>true, 'message'=>'Start IEA Module 1 Reminders']);
    }

    /**
     * Test to check if all modules of first product are completed and the next uncompleted module of the second product is returned 
     * @return Json with success and the corresponding reminder tag
     */
    public function testAllModulesOfFirstProductCompleted(){
    	$products = "iaa,ipa";
    	$newUser = $this->createUser($products);
        //Attach all first modules  to user
        $newUser->completed_modules()->attach(Module::where('course_key', 'iaa')->get()); 
        $newUser->completed_modules()->attach(Module::where('name', 'IPA Module 4')->first());
        $response = $this->json('POST', '/api/module_reminder_assigner', ['email' => $newUser->email]);
        $response->assertStatus(200);
        $response->assertJson(['success'=>true, 'message'=>'Start IPA Module 5 Reminders']);
    }

    /**
     * Test to check if any or last module of the first product is completed and the next uncompleted module of the second product is returned 
     * @return Json with success and the corresponding reminder tag
     */
    public function testLastModulesOfFirstProductCompleted(){
    	$products = "iaa,iea";
    	$newUser = $this->createUser($products);
        //Attach last module of first product to user
        $newUser->completed_modules()->attach(Module::where('course_key', 'iaa')->limit(4)->get()); 
        $newUser->completed_modules()->attach(Module::where('name', 'IAA Module 7')->first());
        $newUser->completed_modules()->attach(Module::where('name', 'IEA Module 2')->first());
        $setUpdateTime = $newUser->completed_modules()->where('name', 'IAA Module 7')->first();		

        $setUpdateTime->pivot->updated_at = Carbon\Carbon::now()->addSeconds(60);
        $setUpdateTime->pivot->save();
        $response = $this->json('POST', '/api/module_reminder_assigner', ['email' => $newUser->email]);
        $response->assertStatus(200);
        $response->assertJson(['success'=>true, 'message'=>'Start IEA Module 3 Reminders']);
    }

    /**
     * Test to check if last modules of the first and second products are completed and the next uncompleted module of the third product is returned 
     * @return Json with success and the corresponding reminder tag
     */
    public function testLastModulesOfFirstSecondProductCompleted(){
    	$products = "iea,ipa,iaa";
    	$newUser = $this->createUser($products);
        //Attach last module of first and second products to user
        $newUser->completed_modules()->attach(Module::where('course_key', 'iea')->limit(4)->get()); 
        $newUser->completed_modules()->attach(Module::where('name', 'IEA Module 7')->first());
        $newUser->completed_modules()->attach(Module::where('course_key', 'ipa')->limit(2)->get());
        $newUser->completed_modules()->attach(Module::where('name', 'IPA Module 7')->first());
        $setUpdateTime = $newUser->completed_modules()->where('name', 'IPA Module 7')->first();		
        $setUpdateTime->pivot->updated_at = Carbon\Carbon::now()->addSeconds(60);
        $setUpdateTime->pivot->save();
        $response = $this->json('POST', '/api/module_reminder_assigner', ['email' => $newUser->email]);
        $response->assertStatus(200);
        $response->assertJson(['success'=>true, 'message'=>'Start IAA Module 1 Reminders']);
    }

    protected function tearDown()
    {
        DB::statement('truncate table user_completed_modules');
    }
}
