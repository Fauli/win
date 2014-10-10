<?php

require_once 'vendor/autoload.php';

$woops = new \Whoops\Run;
$woops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$woops->register();

$url = 'http://otter.topsy.com/search.js';

$client = new Artax\Client;
$scraper = new TweetScraper\Scraper($client);
//$body = $scraper->scrape($url);

$body = file_get_contents('data/testdata.json');

$parser = new TweetScraper\Parser;
$parsed = $parser->parse($body);

var_dump($parsed);

die;

$content = '';

//$content = file_get_contents('http://otter.topsy.com/search.js?q=bitcoin&offset=10&perpage=50&window=a&sort_method=-date&apikey=09C43A9B270A470B8EB8F2946A9369F3');
$content = file_get_contents('data/testdata.json');
$content = json_decode($content);

foreach ($content->response->list as $item) {
    var_dump($item->content);die;
}

echo '' . PHP_EOL;
