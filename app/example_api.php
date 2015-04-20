<?php


require( dirname(__FILE__) .'/../vendor/autoload.php');
include_once( dirname(__FILE__) .'/../Hubs3d/Api.php');


$settings = array(
    'consumer_key' => 'YOUR_CONSUMER_KEY_HERE',
    'consumer_secret' => 'YOUR_CONSUMER_SECRET_HERE'
);


$element = new stdClass();
$element->file = dirname(__FILE__) . '/' . 'marvin.stl';
$element->fileName = 'marvin';


$file = json_encode($element);


/** ACTUAL API **/
use \Hubs3d\Api;

$hubs3d = new Api($settings);

////////////////////////////////////////////////////////////////////////////////
// Send the models to the API.
////////////////////////////////////////////////////////////////////////////////
$model = $hubs3d->createModel($file);

////////////////////////////////////////////////////////////////////////////////
// Create the cart with the uploaded models.
////////////////////////////////////////////////////////////////////////////////
$result = $hubs3d->createCart($model);

print_r($result);
echo "All done. Visit the url to claim the cart! ".PHP_EOL;
echo $result['url'];