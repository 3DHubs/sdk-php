<?php

require dirname(__FILE__) . '/vendor/autoload.php';

use Hubs3d\Api;

// Create consumer at: https://www.3dhubs.com/my-dashboard/api/oauth/consumer/add
$settings = array(
    'consumer_key' => '',
    'consumer_secret' => '',
);

$hubs3d = new Api($settings);

////////////////////////////////////////////////////////////////////////////////
// Send the models to the API.
////////////////////////////////////////////////////////////////////////////////
$model = $hubs3d->createModel(dirname(__FILE__) . '/marvin.stl');
var_dump($model);

////////////////////////////////////////////////////////////////////////////////
// Create the cart with the uploaded models.
////////////////////////////////////////////////////////////////////////////////
$items = array(
    'items' => array(
        array(
            'modelId' => $model['modelId'],
            'quantity' => 3,
        ),
    ),
  );

$result = $hubs3d->createCart($items);
var_dump($result);

