<?php
return [
    ['GET', '/', [
        'Web\Frontend\HtmlPresenter',
        'showHomepage',
    ]],
    ['GET', '/charts/getJsonData/{name}', [
        'Web\Frontend\JsonPresenter',
        'showChartData',
    ]],
    ['GET', '/notifications/getJsonData', [
        'Web\Frontend\JsonPresenter',
        'showNotificationData',
    ]],
];