<?php
/**
 * Created by PhpStorm.
 * User: Xenia
 * Date: 24/03/15
 * Time: 17:24
 */


namespace Hubs3d;

use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

class Api
{
    /**
     * @var string
     */
    private $host = 'http://3dhubs.localhost';
    /**
     * @var string
     */
    private $consumer_key;
    /**
     * @var string
     */
    private $consumer_secret;
    /**
     * @var Oauth1;
     */
    private $oauth;
    /**
     * @var Client;
     */
    private $client;

    /**
     * @param $settings
     * @throws \Exception
     */
    public function __construct($settings)
    {
        if ( !isset($settings['consumer_key']) || !isset($settings['consumer_secret']))
        {
            throw new \Exception('Make sure you are passing in the correct parameters');
        }

        $this->consumer_key = $settings['consumer_key'];
        $this->consumer_secret = $settings['consumer_secret'];

        $this->init();
    }

    /**
     * init HTTP client
     */
    public function init()
    {
        //initalize the client
        $this->client = new Client([
            'base_url' => $this->host . '/api/v1/',
            'defaults' => ['auth' => 'oauth', 'headers' => ['Content-type' => 'application/json'] ],
        ]);

        $this->oauth = new Oauth1([
            'consumer_key'    => $this->consumer_key,
            'consumer_secret' => $this->consumer_secret,
        ]);

        $this->client->getEmitter()->attach($this->oauth);
    }

    /**
     * @param $model
     * @return string
     */
    public function createModel($model)
    {
        $model = json_decode($model);

        $data = [
            'file' => base64_encode(file_get_contents($model->file)),
            'fileName' => $model->fileName,
        ];

        $res = $this->_post('model', $data);

        // Get the result.
        $result = $res->json();

        $obj = new \stdClass();
        if($result['result'] == 'success'){
            $obj->modelId= $result['modelId'];
            $obj->quantity = 1;
        } else {
            //do error handling
            $obj->error = true;
        }

        return json_encode($obj);
    }


    /**
     * @param $models
     * @return mixed
     */
    public function createCart($models)
    {
        $models = json_decode($models);

        if(is_array($models)){
            $data = array();
            foreach($models as $index => $modelObj){
                $data['items[' . $modelObj->modelId . '][modelId]'] =  $modelObj->modelId;
                $data['items[' .  $modelObj->modelId . '][quantity]'] =  $modelObj->quantity;
            }
        } else {
            $data = array();
            $data['items[' . $models->modelId . '][modelId]'] =  $models->modelId;
            $data['items[' .  $models->modelId . '][quantity]'] =  $models->quantity;
        }
        $res = $this->_post('cart', $data);

        // All done, output result.
        return $res->json();
    }

    /**
     * @param $url
     * @param $data
     * @param $options
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    private function _post($url, $data, $options= [])
    {
        $request = $this->client->createRequest('POST', $url, $options);
        $postBody = $request->getBody();

        if(is_array($data)){
            foreach($data as $name => $value){
                $postBody->setField($name, $value);
            }
        }

        //Make the request to add a model.
        return $this->client->send($request);
    }

}