<?php

namespace Web\Frontend;

use Http\Response;

class Presenter
{
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function showHomepage()
    {
        $this->response->setContent('hello');
    }
}