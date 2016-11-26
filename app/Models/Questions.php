<?php
/**
 * Created by PhpStorm.
 * User: Victor
 * Date: 26/11/2016
 * Time: 14:29
 */

namespace App\Models;

use GuzzleHttp\Client;


class Questions
{

    protected $base_uri = 'http://localhost:6000/questions/';
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function start($custId, $catId, $params=[])
    {
        $res = $this->client->get($this->base_uri."{$custId}", $params);
        $body = $res->getBody();
        return json_decode($body->__toString(), true);
    }

    public function fetchNext($custId, $catId, $params=[])
    {
        $res = $this->client->post($this->base_uri."{$custId}/answers", $params);
        $body = $res->getBody();
        // Should work... most of the times... I guess :)
        return json_decode($body->__toString(), true);
    }



}