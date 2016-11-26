<?php
/**
 * Created by PhpStorm.
 * User: Victor
 * Date: 26/11/2016
 * Time: 12:08
 */

namespace App\Models;

use \GuzzleHttp\Client;


abstract class ZalandoApi
{

    protected $base_uri = "https://api.zalando.com/";
    protected $client;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => $this->base_uri, 'verify' => false]);
    }

    protected function fetch($endpoint, $method, $params=[])
    {
        $res = $this->client->request($method, $endpoint, $params);
        $body = $res->getBody();
        // Should work... most of the times... I guess :)
        return json_decode($body->__toString());
    }

    abstract public function getBaseEndpoint();
}