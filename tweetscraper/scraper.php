<?php
set_time_limit(0);

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'vendor/autoload.php';

$woops = new \Whoops\Run;
$woops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$woops->register();

$db = json_decode(file_get_contents('../db.json'));

if (!array_key_exists(1, $argv) || !array_key_exists(2, $argv)) {
    exit('Missing Date Arguments. Call script like this: php scraper.php 2014-09-08 2014-10-10' . PHP_EOL);
}

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

$from = new DateTime($argv[1]);
$to = new DateTime($argv[2]);

$dateRangeScraper = new TweetScraper\DateRangeScraper($scraper, $parser, $persister);
$dateRangeScraper->setSleepInSeconds(1);
$dateRangeScraper->setAmount(1000);
$dateRangeScraper->scrape($from, $to);
