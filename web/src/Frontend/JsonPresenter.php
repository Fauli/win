<?php

namespace Web\Frontend;

use Http\Response;
use Twig_Environment;

class JsonPresenter
{
    private $response;
    private $dataService;

    public function __construct(Response $response, DataService $dataService)
    {
        $this->response = $response;
        $this->dataService = $dataService;
    }

    public function showChartData($params)
    {
        $data = $this->dataService->fetchForName($params['name']);
        $json = json_encode($data, JSON_PRETTY_PRINT);
        
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setContent($json);
    }
}