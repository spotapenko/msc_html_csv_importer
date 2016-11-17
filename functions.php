<?php


function get_data_from_html($xpath) {
    $items = [];
    foreach($xpath->evaluate('//div[contains(@class,"toilets-list-view")]') as $node) {//toilets-list-view //finishes-list-view

        foreach ($node->childNodes as $child) {
            if (!$node->nodeName) {
                continue;
            }

            $name = $xpath->evaluate('string(.//h4)', $child);
            $img = $xpath->evaluate('string(.//a/@href)', $child);
            $descr =  $xpath->evaluate('string(.//ul[contains(@class,"data-wish-item-descr")])', $child);

            if (!$name || !$img || !$descr) {//|| !$descr
                continue;
            }

            $items['name'][] = $xpath->evaluate('string(.//h4)', $child);
            $items['img'][] = $xpath->evaluate('string(.//a/@href)', $child);
            $items['descr'][] = $xpath->evaluate('string(.//ul[contains(@class,"data-wish-item-descr")])', $child);


            // echo $child->nodeName, PHP_EOL;
        }
    }

    return $items;
}


function map_products_from_data($items) {
    $products = [];
    foreach ($items as $field => $values) {
        foreach ($values as $index => $value) {
            $products[$index][$field] =  $value;
        }
    }

    return $products;
}

function prepare_csv_data($products, $product_category) {
    $csv_data = [];
    $csv_data[] = array('title','img','descr','product_category');

    foreach($products as $product) {
        $descr_lines = explode("\n", $product['descr']);

        $descr = '<ul class="data-wish-item-descr">';
        foreach ($descr_lines as $line) {
            $trimmed_line = trim($line);
            if ($trimmed_line) {
                $descr .= '<li>' . $trimmed_line  . '</li>' . "\n";
            }
        }
        $descr .= '</ul>';

        $csv_data[] = array($product['name'], $product['img'], $descr, $product_category);
    }

    return $csv_data;
}

function write_csv_file($filename, $csv_data) {
    $fp = fopen('/var/www/html/test/msc_html_csv_importer/temp/' . $filename, 'w');

    foreach ($csv_data as $fields) {
        fputcsv($fp, $fields);
    }
    fclose($fp);
}
