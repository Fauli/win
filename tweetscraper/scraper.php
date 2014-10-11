<?php

set_time_limit(0);

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

$client = new Artax\Client;
$scraper = new TweetScraper\Scraper($client);
$parser = new TweetScraper\Parser;
$persister = new TweetScraper\Persister($pdo);

$from = new DateTime('2014-07-01');
$to = new DateTime('2014-10-10');

$dateRangeScraper = new TweetScraper\DateRangeScraper($scraper, $parser, $persister);
$dateRangeScraper->setSleepInSeconds(15);
$dateRangeScraper->setAmount(1000);
$dateRangeScraper->scrape($from, $to);