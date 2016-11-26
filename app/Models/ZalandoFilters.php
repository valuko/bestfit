<?php
/**
 * Created by PhpStorm.
 * User: Victor
 * Date: 26/11/2016
 * Time: 13:00
 */

namespace App\Models;


class ZalandoFilters extends ZalandoApi
{

    protected $base_endpoint;

    public function __construct()
    {
        $this->base_endpoint = 'filters/';
        parent::__construct();
    }

    public function fetchFilters($params=[])
    {
        return $this->fetch($this->base_endpoint, $params);
    }

    public function fetchFilter($id, $params=[])
    {
        return $this->fetch($this->base_endpoint.$id, $params);
    }

    public function getBaseEndpoint()
    {
        return $this->base_endpoint;
    }
}