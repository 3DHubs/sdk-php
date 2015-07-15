<?php

$settings = array(
    'consumer_key' => 'YOUR_CONSUMER_KEY_HERE',
    'consumer_secret' => 'YOUR_CONSUMER_SECRET_HERE'
);

define('API_HOST', 'https://www.3dhubs.com');

require( dirname(__FILE__) .'/../vendor/autoload.php');

use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

$client = new Client([
    'base_url' => API_HOST . '/api/v1/',
    'defaults' => ['auth' => 'oauth'],
    ]);

$oauth = new Oauth1([
    'consumer_key'    => $settings['consumer_key'],
    'consumer_secret' => $settings['consumer_secret'],
  ]);

$client->getEmitter()->attach($oauth);

////////////////////////////////////////////////////////////////////////////////
// Send the models to the API.
////////////////////////////////////////////////////////////////////////////////

// Define the file to be uploaded
$files = array(
  'marvin.stl' => 3,
  'gopro.stl' => 1
);

foreach($files as $filename => $quantity){
  $data = [
    'file' => base64_encode(file_get_contents(dirname(__FILE__) . '/' . $filename)),
    'fileName' => $filename,
  ];

  $request = $client->createRequest('POST', 'model');
  $postBody = $request->getBody();
  foreach($data as $name => $value){
    $postBody->setField($name, $value);
  }
  // Make the request to add a model.
  $res = $client->send($request);

  // Get the result.
  $result = $res->json();

  // Save the modelid
  $modelIds[$result['modelId']] = $quantity;

  echo 'Saved model number ' . $result['modelId'] . PHP_EOL;
}

////////////////////////////////////////////////////////////////////////////////
// Create the cart with the uploaded models.
////////////////////////////////////////////////////////////////////////////////
$data = array();
foreach($modelIds as $modelId => $quantity){
  $data['items[' . $modelId . '][modelId]'] = $modelId;
  $data['items[' . $modelId . '][quantity]'] = $quantity;
}
$request = $client->createRequest('POST', 'cart');

$postBody = $request->getBody();
foreach($data as $name => $value){
  $postBody->setField($name, $value);
}

//Make the request to add a model.
$res = $client->send($request);

// All done, output result.
$result = $res->json();
echo 'All done. Visit the url to claim the cart!' . PHP_EOL;
echo $result['url'] . PHP_EOL;




