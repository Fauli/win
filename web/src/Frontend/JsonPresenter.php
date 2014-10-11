<?php

namespace Web\Frontend;

use Http\Response;
use Twig_Environment;

class JsonPresenter
{
    private $response;
    private $dataService;
    private $notificationService;

    public function __construct(
        Response $response, 
        DataService $dataService, 
        NotificationService $notificationService
    ) {
        $this->response = $response;
        $this->dataService = $dataService;
        $this->notificationService = $notificationService;
    }

    public function showChartData($params)
    {
        $data = $this->dataService->fetchForName($params['name'], $params['from'], $params['to']);
        $json = json_encode($data, JSON_PRETTY_PRINT);
        
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setContent($json);
    }

    public function showNotificationData()
    {
        $data = $this->notificationService->getNotifications();

        $json = json_encode($data, JSON_PRETTY_PRINT);
        
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setContent($json);
    }
}