<?php
/**
 * Created by 3D Hubs
 */

namespace Hubs3d;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

class Api
{
    /**
     * @var string
     */
    private $host = 'https://www.3dhubs.com';
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

        if (isset($settings['host'])){
            $this->host = $settings['host'];
        }

        $this->init();
    }

    /**
     * init HTTP client
     */
    public function init()
    {
        //initalize the client

        $stack = HandlerStack::create();

        $middleware = new Oauth1([
            'consumer_key'    => $this->consumer_key,
            'consumer_secret' => $this->consumer_secret,
            'token'           => null,
            'token_secret'    => null,
        ]);
        $stack->push($middleware);

        $this->client = new Client([
            'base_uri' => $this->host . '/api/v1/',
            'handler' => $stack,
            'auth' => 'oauth',
            'headers' => ['Content-type' => 'application/json']
        ]);
    }

    /**
     * @param $file_path
     * @param null $filename
     * @throws \Exception
     * @return array
     */
    public function createModel($file_path, $filename = null)
    {
        if(!$filename){
            $filename = basename($file_path);
        }

        $data = [
            'file' => base64_encode(file_get_contents($file_path)),
            'fileName' => $filename,
        ];

        //$res = $this->_post('model', $data);
        $res = $this->_post('model', $data);

        // Return the result.
        return json_decode((string)$res->getBody(), true);
    }


    /**
     * @param $items - array of modelId and quantity object
     * @throws \Exception
     * @return array
     */
    public function createCart($items)
    {
        $res = $this->_post('cart', $items);
        // All done, output result.
        return json_decode((string)$res->getBody(), true);
    }

    /**
     * @param $url
     * @param $data
     * @param bool $isjson
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    private function _post($url, $data, $isjson = true)
    {
        if($isjson){
            return $this->client->post($url, array(
                'headers' => array('Content-type' => 'application/json'),
                'json' => $data
            ));
        } else {
            //x-www-formurlencoded this is deprecated
            $request = $this->client->createRequest('POST', $url);
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
}
