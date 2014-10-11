<?php

namespace Web\Frontend;

use Http\Response;
use Twig_Environment;

class Presenter
{
    private $response;
    private $twig;

    public function __construct(Response $response, Twig_Environment $twig)
    {
        $this->response = $response;
        $this->twig = $twig;
    }

    public function showHomepage()
    {
        $content = $this->twig->render('Dashboard.html');
        $this->response->setContent($content);
    }
}