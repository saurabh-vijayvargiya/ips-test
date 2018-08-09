<?php

namespace App\Http\Controllers;

use App\Http\Helpers\InfusionsoftHelper;
use App\Module;
use App\Order;
use App\Tag;
use App\User;
use Illuminate\Http\Request;
use Response;

class ApiController extends Controller
{
    /**
     * Module reminder assigner
     */
    public function moduleReminderAssigner(Request $request)
    {
        $user = User::where('email', $request->input('email'))->first();
        $orders = $user->get_orders()->get(['course']);
        $completedModules = $user->completed_modules()->get();
        $nextModule = $this->nextModule($orders, $completedModules);
        if ($nextModule) {
            $newTagId = Tag::where('name', 'Start ' . $nextModule . ' Reminders')->first()->id;
        } else {
            $newTagId = Tag::where('name', 'Module reminders completed')->first()->id;
        }

        $infusionsoft = new InfusionsoftHelper();
        $contact = $infusionsoft->getContact($user->email);
        if (is_bool($contact)) {
            (new InfusionsoftController())->authorizeInfusionsoft();
        }
        if ($infusionsoft->addTag($contact['Id'], $newTagId)) {
            $infusionsoft->removeTag($contact['Id'], $user->tag_id);
            $user->tag_id = $newTagId;
            $user->save();
            return Response::json([
                'success' => true,
                'message' => 'Assigned module reminder successfully'
            ]);
        }

        return Response::json([
            'success' => false,
            'message' => 'Error occured while assigning module reminder'
        ]);
    }

    /**
     *
     * Method to get the current module of the user.
     *
     * @param $courseKey
     * @param $completedModules
     *
     * @return bool|null|string
     */
    private function currentModule($courseKey, $completedModules)
    {
        $currentModule = '';
        foreach ($completedModules as $completedModule) {
            if ($completedModule->course_key === $courseKey && strpos($completedModule->name, '7')) {
                return false;
            } elseif ($completedModule->course_key === $courseKey && !strpos($completedModule->name, '7')) {
                $currentModule = $completedModule['name'];
            } elseif ($completedModule->course_key === $courseKey) {
                $currentModule = null;
            }
        }
        return $currentModule;
    }

    /**
     *
     * Method to get the next module of the user.
     *
     * @param $orders
     * @param $completedModules
     *
     * @return array|bool|string
     */
    private function nextModule($orders, $completedModules)
    {
        foreach ($orders as $order) {
            if ($currentModule = $this->currentModule($order->course, $completedModules)) {
                $nextModule = str_split($currentModule, strlen($currentModule) - 1);
                $nextModule[1] = $nextModule[1] + 1;
                $nextModule = implode($nextModule, '');
                return $nextModule;
            } elseif (is_null($currentModule)) {
                $nextModule = strtoupper($order->course) . ' Module 1';
                return $nextModule;
            }
        }

        return false;
    }

    public function exampleCustomer(){

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

        // Inserting orders into orders table.
        Order::insert([
            [
                'user_id' => $user->id,
                'course' => 'ipa',
            ]
        ]);
        // attach IPA M1-3 & M5
        $user->completed_modules()->attach(Module::where('course_key', 'ipa')->limit(3)->get());
        $user->completed_modules()->attach(Module::where('name', 'IPA Module 5')->first());


        return $user;
    }

    /**
     *
     * Method to add module that user completes.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userModuleAdd(Request $request)
    {
        $user = User::where('email', $request->input('email'))->first();
        $moduleName = $request->input('moduleName');
        $userCourses = $user->get_orders()->get(['course']);

        $moduleInOrderedCourse = false;

        foreach ($userCourses as $userCourse) {
            $moduleInOrderedCourse = $userCourse->course === strtolower(explode(' ', $moduleName)[0]) ? true : false;
        }

        if ($user && $moduleInOrderedCourse) {
            $user->completed_modules()->attach(Module::where('name', $moduleName)->first());
            return Response::json([
                'success' => true,
                'message' => 'Module added successfully'
            ]);
        }
        return Response::json([
            'success' => false,
            'message' => 'Error occured while adding module'
        ]);
    }
}
