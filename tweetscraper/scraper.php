<?php

require_once 'vendor/autoload.php';

$woops = new \Whoops\Run;
$woops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$woops->register();

$db = json_decode(file_get_contents('../db.json'));

$host = $db->host;
$dbname = $db->dbname;
$user = $db->user;
$password = $db->password;

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
} catch(PDOException $e) {
    echo $e->getMessage();
    exit;
}

$url = 'http://otter.topsy.com/search.js';

$client = new Artax\Client;
$scraper = new TweetScraper\Scraper($client);

$date = new DateTime();
$date->setTimestamp(strtotime('2014-10-02'));

$body = $scraper->scrape($url, $date);
//$body = file_get_contents('data/testdata.json');

$parser = new TweetScraper\Parser;
$data = $parser->parse($body);

$persister = new TweetScraper\Persister($pdo);
$persister->persist('btc', $data);