<?php
/**
 * Created by PhpStorm.
 * User: Victor
 * Date: 26/11/2016
 * Time: 12:33
 */

namespace App\Models;


class ZalandoCategories extends ZalandoApi
{

    protected $base_endpoint = "categories/";

    public function __construct()
    {

        parent::__construct();
    }

    public function getBaseEndpoint()
    {
        return $this->base_endpoint;
    }

    public function fetchCategories($params=[])
    {
        $this->fetch($this->base_endpoint, 'get', $params);
    }
}