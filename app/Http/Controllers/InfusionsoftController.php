<?php

namespace App\Http\Controllers;

use App\Http\Helpers\InfusionsoftHelper;
use App\Tag;
use Request;
use Storage;
use Response;

class InfusionsoftController extends Controller
{
    public function authorizeInfusionsoft(){
        return (new InfusionsoftHelper())->authorize();
    }

    public function testInfusionsoftIntegrationGetEmail($email){

        $infusionsoft = new InfusionsoftHelper();

        return Response::json($infusionsoft->getContact($email));
    }

    public function testInfusionsoftIntegrationAddTag($contact_id, $tag_id){

        $infusionsoft = new InfusionsoftHelper();

        return Response::json($infusionsoft->addTag($contact_id, $tag_id));
    }

    public function testInfusionsoftIntegrationGetAllTags(){
        if (count($tags = Tag::all()) > 0) {
            return Response::json($tags);
        } else {
            $infusionsoft = new InfusionsoftHelper();
            $tags = $infusionsoft->getAllTags();
            // Checking if app has Infusionsoft auth.
            if (is_bool($tags)) {
                $this->authorizeInfusionsoft();
            }
            $tags = $tags->all();
            foreach ($tags as $tag) {
                $tagArray = $tag->attributesToArray();
                Tag::create($tagArray);
            }

            return Response::json($tags);
        }
    }

    public function testInfusionsoftIntegrationCreateContact(){

        $infusionsoft = new InfusionsoftHelper();

        return Response::json($infusionsoft->createContact([
            'Email' => uniqid().'@test.com',
            "_Products" => 'ipa,iea'
        ]));
    }
}
