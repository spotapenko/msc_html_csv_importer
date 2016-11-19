<?php


function get_data_from_html($xpath)
{
    $items = [
        'name' => [],
        'img' => [],
        'descr' => [],
        'attributes' => [],
        'category' => []
    ];

    $variable_products = [];

    //category container
    foreach ($xpath->evaluate('//div[contains(@class,"view-options")]') as $node) {

        $category = $xpath->evaluate('string(.//h2[contains(@class,"heading")])', $node);

        if (!$category) {
            continue;
        }

        var_dump($category);

        //view items container
        foreach ($xpath->evaluate('.//div[contains(@class,"cabinets-list-view")]', $node) as $items_container) {
            var_dump('view items container');

            //product container
            $product_num = 0;
            foreach ($xpath->evaluate('.//div[contains(@class,"cabinets-list-item")]', $items_container) as $product) {
                var_dump('product container');
                $name = $xpath->evaluate('string(.//h3[contains(@class,"data-wish-item-name")])', $product);

                // $name = str_replace('"','', $name);
                $img = $xpath->evaluate('string(.//a/@href)', $product);
                $descr = $xpath->evaluate('string(.//ul[contains(@class,"data-wish-item-descr")])', $product);


                $allowed_attr_names = [
                    'Code', 'Width', 'Height', 'Depth'
                ];
                $attr_headers = [];
                //attributes container
                foreach ($xpath->evaluate('.//div[contains(@class,"cabinets-info")]//table', $product) as $attributes_container) {
                    var_dump('attributes container ');
                    //attributes headers
                    $row = 0;
                    foreach ($xpath->evaluate('.//tr[contains(@class,"table-header")]//th', $attributes_container) as $attr_header) {
                        // var_dump('attributes headers ');
                        // var_dump($attr_header->nodeValue);
                        $attr_name = $attr_header->nodeValue;

                        if (in_array($attr_name, $allowed_attr_names)) {
                            $attr_headers[$row] = $attr_name;
                        }

                        $row++;
                    }

                    //attributes collections
                    $attr_collections = [];
                    foreach ($xpath->evaluate('.//tr[contains(@class,"wish-item-container")]', $attributes_container) as $source_attr_collections) {
                        //var_dump('attributes values ');

                        $attr_values = [];
                        //attr values
                        $row = 0;
                        foreach ($xpath->evaluate('.//td', $source_attr_collections) as $source_attr_values) {
                            if (isset($attr_headers[$row])) {
//                         var_dump('attributes values ');
//                         var_dump($source_attr_values->nodeValue);
                                //  var_dump($source_attr_values->nodeValue);
                                if ('Width' == $attr_headers[$row]  &&  'BLIND CORNER CABINETS' == $category) {
                                    $attr_value = str_replace('" Door', '', $source_attr_values->nodeValue);
                                } else {
                                    $attr_value = str_replace('"', '', $source_attr_values->nodeValue);
                                }

                             //   $attr_value = $source_attr_values->nodeValue;
                                $attr_values[$row] = $attr_value;
                            }
                            $row++;
                        }
                     //   var_dump('111111');
                     //   var_dump($attr_values);

                        $attr_collections[] = $attr_values;
                    }
                }
                $assoc_attributes = [];

                $i = 0;
                foreach ($attr_collections as $attributes) {
                    foreach ($attr_headers as $key => $val) {
                        $assoc_attributes[$i][$val] = $attributes[$key];
                    }
                    $i++;
                }

                if (!$name || !$img) { //|| !$descr
                    continue;
                }
                //   continue;
                $category = str_replace(' ', '-', strtolower($category));
                if ('base-cabinets' == $category) {
                    $category = 'base-cabinets-base-cabinets';
                }

                $variable_products[] = [
                    'name' => $name,
                    'img' => $img,
                    'descr' => $descr,
                    'attributes' => $assoc_attributes,
                    'category' => $category
                ];
            }
        }
    }

    return $variable_products;
}


function map_products_from_data($items)
{
    var_dump($items);
    exit;
    $products = [];
    foreach ($items as $field => $values) {
        foreach ($values as $index => $value) {
            $products[$index][$field] = $value;
        }
    }

    return $products;
}

function prepare_csv_data($products)
{
    // var_dump($products);
    // exit;
    $csv_data = [];
    $csv_data[] = array('title', 'img', 'descr', 'product_category', 'code', 'width', 'height', 'depth');

    foreach ($products as $product) {

        $descr = '';
        if ($product['descr']) {
            $descr_lines = explode("\n", $product['descr']);

            $descr = '<ul class="data-wish-item-descr">';
            foreach ($descr_lines as $line) {
                $trimmed_line = trim($line);
                if ($trimmed_line) {
                    $descr .= '<li>' . $trimmed_line . '</li>';
                }
            }
            $descr .= '</ul>';
        }

        $product_category = $product['category'];

        $code = '';
        $width = '';
        $height = '';
        $depth = '';

        foreach ($product['attributes'] as $attributes) {
            foreach ($attributes as $key => $val) {
                if ($key == 'Code') {
                    $code = $val;
                }
                if ($key == 'Width') {
                    $width = $val;
                }
                if ($key == 'Height') {
                    $height = $val;
                }
                if ($key == 'Depth') {
                    $depth = $val;
                }
            }

            $csv_data[] = array($product['name'], $product['img'], $descr, $product_category, $code, $width, $height, $depth);
        }
    }

    return $csv_data;
}

function write_csv_file($filename, $csv_data)
{
    $fp = fopen('/var/www/html/test/msc_html_csv_importer/temp/' . $filename, 'w');

    foreach ($csv_data as $fields) {
        fputcsv($fp, $fields);
    }
    fclose($fp);
}
