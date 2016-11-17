<?php

require_once __DIR__ . '/config.php';
require_once 'functions.php';

//$url = 'http://localhost/test/msc/lightning.html';
$url = 'lightning.html';
//$url = 'kitchens-finishes.html';
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


$items = get_data_from_html($xpath);

$products = map_products_from_data($items);

$csv_data = prepare_csv_data($products);

$filename = 'lighting.csv';

write_csv_file($filename, $csv_data);
//generate csv

