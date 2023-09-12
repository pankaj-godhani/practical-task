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
//    public function test_example()
//    {
//        $response = $this->http('/');
//        Http::fake()
//        $response->assertStatus(200);
//    }

    public function test_fetching_data_from_external_api()
    {
        $response = Http::get('https://randomuser.me/api/' );

        $this->assertTrue($response->status() === 200);
        // fetch users | activities
        // assert -> we got data from api
    }

//    public function test_getting_sorted_list()
//    {
//        $response = Http::get('https://randomuser.me/api/?results=10');
//        $data = json_decode($response->getBody(), true);
////        $unSortedUsers = $data['results'][0];
//
//        usort($data['results'], function ($a, $b) {
//            return strcasecmp($a['name']['last'], $b['name']['last']);
//        });
//
//
////
////        dd($user);
//        // assert sorting order
//    }
//
    public function test_returns_valid_xml_response()
    {
        // data prepare
        $response = Http::get('https://randomuser.me/api/?results=10');
        $data = json_decode($response->getBody(), true);
        $xml = new SimpleXMLElement('<users></users>');

        foreach ($data['results'] as $user) {
            $userElement = $xml->addChild('user');
            $userElement->addChild('full_name', $user['name']['first'] . ' ' . $user['name']['last']);
            $userElement->addChild('phone', $user['phone']);
            $userElement->addChild('email', $user['email']);
            $userElement->addChild('country', $user['location']['country']);
        }
        $testXml = simplexml_load_string($xml);
        dd($testXml);

        if ($testXml !== false && isset($testXml->message)) {

            $this->assertTrue($response->status() === 200);

            //either this
            var_dump($testXml->message);
            $this->assertEquals('Hi there PHP', $xml->message);

            //or this, should be stdClass
            $xmlObj = json_decode(json_encode((array) $testXml), 1);
            var_dump($xmlObj->message);
            $this->assertEquals('Hi there PHP', $xmlObj->message);
        } else {
            dd("else");
        }
        // xml prepare
        // assert -> valid xml or not
    }

    public function getUsers()
    {
        return [

        ];
    }
}
