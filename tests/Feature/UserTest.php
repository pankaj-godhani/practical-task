<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use SimpleXMLElement;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function test_fetching_data_from_external_api()
    {
        $response = Http::get('https://randomuser.me/api/' );
         if($response->status() === 200){
             $this->assertTrue($response->status() === 200);
         } else{
             $response2 = Http::get('https://www.boredapi.com/api/activity');
             if($response2->status() === 200){
                 $this->assertTrue($response2->status() === 200);
             } else{
                 $this->assertTrue(false);
             }
         }
    }

    public function test_getting_sorted_list()
    {
        $response = Http::get('https://randomuser.me/api/?results=10');
        if($response->status() === 200) {
            $data = json_decode($response->getBody(), true);
            $unsortedArray = $data['results'];
            usort($data['results'], function ($a, $b) {
                return strcasecmp($b['name']['last'], $a['name']['last']);
            });
            $this->assertNotEquals($data['results'], $unsortedArray);
        } else{
            $response2 = Http::get('https://www.boredapi.com/api/activity');
            if($response2->status() === 200){
                $data = json_decode($response->getBody(), true);
                $unsortedArray = $data['results'];
                usort($data['results'], function ($a, $b) {
                    return strcasecmp($b['type'], $a['type']);
                });
                $this->assertNotEquals($data['results'], $unsortedArray);
            } else{
                $this->assertTrue(false);
            }
        }
    }

    public function test_returns_valid_xml_response()
    {
        // data prepare
        $response = Http::get('https://randomuser.me/api/?results=10');
        $data = json_decode($response->getBody(), true);
        $xml = new SimpleXMLElement('<xml><users></users></xml>');

        foreach ($data['results'] as $user) {
            $userElement = $xml->addChild('user');
            $userElement->addChild('full_name', $user['name']['first'] . ' ' . $user['name']['last']);
            $userElement->addChild('phone', $user['phone']);
            $userElement->addChild('email', $user['email']);
            $userElement->addChild('country', $user['location']['country']);
        }

        $testXml = simplexml_load_string($xml->asXML());

        if ($testXml !== false && isset($testXml->users)) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    public function getUsers()
    {
        return [

        ];
    }
}
