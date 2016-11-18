<?php

require_once __DIR__ . '/config.php';
require_once 'functions.php';

//$url = 'http://localhost/test/msc/lightning.html';
//$url = 'lightning.html';
$url = 'kitchens-finishes.html';
$page =  @file_get_contents(SITE_URL . $url);
//get heads
$items = [
    'name' => [],
    'img' => [],
    'descr' => [],
];
$dom = new DOMDocument();
$dom->loadHTML($page);
$xpath = new DOMXPath($dom);

//$container_class = 'toilets-list-view';
//$container_class = 'finishes-list-view';
$items = get_data_from_html($xpath);

var_dump($items);

$products = map_products_from_data($items);

$csv_data = prepare_csv_data($products, '');

var_dump($csv_data);

$filename = 'finishes-data.csv';

write_csv_file($filename, $csv_data);
//generate csv

