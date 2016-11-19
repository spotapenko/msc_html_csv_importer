<?php

require_once __DIR__ . '/config.php';
require_once 'functions.php';

//$url = 'http://localhost/test/msc/lightning.html';
//$url = 'lightning.html';
$url = 'kitchens-pantry-cabinets.html';
$page =  @file_get_contents(SITE_URL . $url);
//get heads
$items = [
    'name' => [],
    'img' => [],
    'descr' => [],
    'attributes' => [],
    'category' => [],
];
$dom = new DOMDocument();
$dom->loadHTML($page);
$xpath = new DOMXPath($dom);

//$container_class = 'toilets-list-view';
//$container_class = 'finishes-list-view';
$items = get_data_from_html($xpath);



//$products = map_products_from_data($items);
//var_dump($items);
$csv_data = prepare_csv_data($items);

print "<pre>";
print_r($csv_data);
print "</pre>";

$filename = 'kitchens-pantry-cabinets-data.csv';

//generate csv
write_csv_file($filename, $csv_data);
