<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use SimpleXMLElement;
use function PHPUnit\Framework\isEmpty;

class UserController extends Controller
{
   public function getData(){
       $client = new Client();
       $users = [];
       $activities = [];

       for ($i = 0; $i < 10; $i++) {
           $response = $client->get('https://randomuser.me/api/');
           $data = json_decode($response->getBody(), true);
           if(isset($data['results'])){
               $user = $data['results'][0];
               $full_name = $user['name']['first'] . ' ' . $user['name']['last'];
               $phone = $user['phone'];
               $email = $user['email'];
               $country = $user['location']['country'];

               $users[] = [
                   'full_name' => $full_name,
                   'phone' => $phone,
                   'email' => $email,
                   'country' => $country,
               ];

           }
           else{
                $response = $client->get('https://www.boredapi.com/api/activity');
                $activities[] = json_decode($response->getBody(), true);
           }
       }

       if(!empty($users)){
           usort($users, function ($a, $b) {
               return strcasecmp($b['full_name'], $a['full_name']);
           });

           $xml = new SimpleXMLElement('<users></users>');
           foreach ($users as $user) {
               $userElement = $xml->addChild('user');
               $userElement->addChild('full_name', $user['full_name']);
               $userElement->addChild('phone', $user['phone']);
               $userElement->addChild('email', $user['email']);
               $userElement->addChild('country', $user['country']);
           }
       }
       else{
           usort($activities, function ($a, $b) {
               return strcasecmp($b['type'], $a['type']);
           });
           $xml = new SimpleXMLElement('<activity></activity>');
           foreach ($activities as $activity) {
               $activityElement = $xml->addChild('activity');
               $activityElement->addChild('type', $activity['type']);
               $activityElement->addChild('price', $activity['price']);
               $activityElement->addChild('accessibility', $activity['accessibility']);
           }
       }

       echo  $xml->asXML();
   }
}
