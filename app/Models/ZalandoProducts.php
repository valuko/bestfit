<?php
/**
 * Created by PhpStorm.
 * User: Victor
 * Date: 26/11/2016
 * Time: 12:07
 */

namespace App\Models;


class ZalandoProducts extends ZalandoApi
{

    protected $base_endpoint = "products/";

    public function __construct()
    {

        parent::__construct();
    }

    /**
     * @return array
     */
    public function fetchArticles($params=[])
    {
        return $this->fetch('articles', 'get', $params);
    }

    public function fetchArticle($id, $params=[])
    {
        return $this->fetch("articles/{$id}", 'get', $params);
    }

    public function getBaseEndpoint()
    {
        return $this->base_endpoint;
    }
}