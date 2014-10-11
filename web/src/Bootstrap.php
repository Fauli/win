<?php

require_once('../vendor/autoload.php');

$woops = new \Whoops\Run;
$woops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$woops->register();

$injector = include('Dependencies.php');

$request = $injector->make('Http\HttpRequest');
$response = $injector->make('Http\HttpResponse');

echo $request->getParameter('t',123);