<?php

require_once 'vendor/autoload.php';

$woops = new \Whoops\Run;
$woops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$woops->register();

try {
    $pdo = new PDO("mysql:host=151.236.222.251;dbname=win", 'yolo', '#YOLOswag1337');
} catch(PDOException $e) {
    echo $e->getMessage();
    exit;
}

$url = 'http://otter.topsy.com/search.js';

$client = new Artax\Client;
$scraper = new TweetScraper\Scraper($client);

$date = new DateTime();
$date->setTimestamp(strtotime('2014-10-01'));

$body = $scraper->scrape($url, $date);
//$body = file_get_contents('data/testdata.json');

$parser = new TweetScraper\Parser;
$data = $parser->parse($body);

$persister = new TweetScraper\Persister($pdo);
$persister->persist('btc', $data);