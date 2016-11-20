<?php

require_once __DIR__ . '/config.php';
require_once 'functions.php';

//$url = 'http://localhost/test/msc/lightning.html';
//$url = 'lightning.html';
//config
$url = 'kitchens-panels-fillers.html';
$filename = 'kitchens-panels-fillers-cabinets-data.csv';


$page =  @file_get_contents(SITE_URL . $url);
//get heads

$dom = new DOMDocument();
$dom->loadHTML($page);
$xpath = new DOMXPath($dom);

//$container_class = 'toilets-list-view';
//$container_class = 'finishes-list-view';
$items = get_data_from_html($xpath);


$csv_data = prepare_csv_data($items);



//generate csv
write_csv_file($filename, $csv_data);
