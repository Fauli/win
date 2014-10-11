<?php
return [
    ['GET', '/', [
        'Web\Frontend\HtmlPresenter',
        'showHomepage',
    ]],
    ['GET', '/charts/getJsonData/{name}/{from}/{to}', [
        'Web\Frontend\JsonPresenter',
        'showChartData',
    ]],
    ['GET', '/notifications/getJsonData', [
        'Web\Frontend\JsonPresenter',
        'showNotificationData',
    ]],
];