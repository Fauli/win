<?php
$injector = new \Auryn\Provider;

$injector->alias('Http\Response', 'Http\HttpResponse');
$injector->share('Http\HttpRequest');
$injector->define('Http\HttpRequest', [
    ':get' => $_GET,
    ':post' => $_POST,
    ':cookies' => $_COOKIE,
    ':files' => $_FILES,
    ':server' => $_SERVER,
]);
$injector->alias('Http\Request', 'Http\HttpRequest');
$injector->share('Http\HttpResponse');

$injector->share('Twig_Environment');
$injector->delegate('Twig_Environment', function() use ($injector) {
    $loader = new Twig_Loader_Filesystem('../templates');
    return new Twig_Environment($loader);
});

$injector->share('PDO');
$injector->delegate('PDO', function() {
    $db = json_decode(file_get_contents('../../db.json'));

    $host = $db->host;
    $dbname = $db->dbname;
    $user = $db->user;
    $password = $db->password;

    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
});


return $injector;