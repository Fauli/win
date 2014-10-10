<?php

require_once 'vendor/autoload.php';
$content = '';

//

$content = file_get_contents('http://otter.topsy.com/search.js?q=bitcoin&offset=10&perpage=50&window=a&sort_method=-date&apikey=09C43A9B270A470B8EB8F2946A9369F3');



echo $content . PHP_EOL;