<?php


function get_data_from_html($xpath) {
    $items = [];////div[contains(@class,"'.$container_class.'")]
    foreach ($xpath->evaluate('//div[contains(@class,"view-options")]') as $node) {

        $category =  $xpath->evaluate('string(.//h2[contains(@class,"heading")])', $node);

        if (!$category) {
            continue;
        }

        var_dump($category);

        foreach($xpath->evaluate('.//div[contains(@class,"finishes-list-view")]',$node) as $node2) {
            foreach($xpath->evaluate('.//div[contains(@class,"finishes-list-item")]',$node2) as $node3) {

                $name = $xpath->evaluate('string(.//h4[contains(@class,"data-wish-item-name")])',$node3);
                $name = str_replace('"','', $name);
                $img = $xpath->evaluate('string(.//a/@href)', $node3);
                $descr =  $xpath->evaluate('string(.//ul[contains(@class,"data-wish-item-descr")])', $node3);

                if (!$name || !$img) {//|| !$descr
                    continue;
                }

                $category = str_replace(' ','-',strtolower($category));

                $items['name'][] = $name;
                $items['img'][] = $img;
                $items['descr'][] = $descr;
                $items['category'][] = $category;

                var_dump($items);
            }
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

        $descr = '';
        if ($product['descr']) {
            $descr_lines = explode("\n", $product['descr']);

            $descr = '<ul class="data-wish-item-descr">';
            foreach ($descr_lines as $line) {
                $trimmed_line = trim($line);
                if ($trimmed_line) {
                    $descr .= '<li>' . $trimmed_line  . '</li>';
                }
            }
            $descr .= '</ul>';
        }

        $product_category = isset($product['category']) ? $product['category'] : $product_category;
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
