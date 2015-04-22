<?php


require( dirname(__FILE__) .'/../vendor/autoload.php');
include_once( dirname(__FILE__) .'/../Hubs3d/Api.php');

//set consumer at: https://www.3dhubs.com/my-dashboard/api/oauth/consumer/add
$settings = array(
    'consumer_key' => 'YOUR_CONSUMER_KEY_HERE',
    'consumer_secret' => 'YOUR_CONSUMER_SECRET_HERE'
);

/** ACTUAL API **/
//initialise insanceof API that uses GuzzleHttp
use \Hubs3d\Api;
$hubs3d = new Api($settings);

////////////////////////////////////////////////////////////////////////////////
// Send the models to the API.
////////////////////////////////////////////////////////////////////////////////
$model = $hubs3d->createModel(dirname(__FILE__) . '/' . 'marvin.stl');
var_dump($model);

////////////////////////////////////////////////////////////////////////////////
// Create the cart with the uploaded models.
////////////////////////////////////////////////////////////////////////////////
$items = array(
    'items' => array(
        'modelId' => $model['modelId'],
        'quantity' => 3
    )
);
$result = $hubs3d->createCart($items);
var_dump($result);