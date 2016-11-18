<?php


function get_data_from_html($xpath) {
    $items = [
        'name' => [],
        'img' => [],
        'descr' => [],
        'attributes' => [],
        'category' => []
    ];
    //category container
    foreach ($xpath->evaluate('//div[contains(@class,"view-options")]') as $node) {

        $category =  $xpath->evaluate('string(.//h2[contains(@class,"heading")])', $node);

        if (!$category) {
            continue;
        }

        var_dump($category);

        //view items container
        foreach($xpath->evaluate('.//div[contains(@class,"cabinets-list-view")]',$node) as $items_container) {
            var_dump('view items container');
            //product container
            foreach($xpath->evaluate('.//div[contains(@class,"cabinets-list-item")]',$items_container) as $product) {
                var_dump('product container');
                $name = $xpath->evaluate('string(.//h3[contains(@class,"data-wish-item-name")])',$product);

               // $name = str_replace('"','', $name);
                $img = $xpath->evaluate('string(.//a/@href)', $product);
                $descr =  $xpath->evaluate('string(.//ul[contains(@class,"data-wish-item-descr")])', $product);


                $allowed_attr_names = [
                   'Code', 'Width', 'Height', 'Depth'
                ];
                $attr_headers = [];
                //attributes container
                foreach($xpath->evaluate('.//div[contains(@class,"cabinets-info")]//table',$product) as $attributes_container) {
                    var_dump('attributes container ');
                    //attributes headers
                    $row = 0;
                    foreach($xpath->evaluate('.//tr[contains(@class,"table-header")]//th',$attributes_container) as $attr_header) {
                       // var_dump('attributes headers ');
                       // var_dump($attr_header->nodeValue);
                        $attr_name = $attr_header->nodeValue;

                        if (in_array($attr_name, $allowed_attr_names)) {
                            $attr_headers[$row] = $attr_name;
                        }

                        $row++;

                      //  $attr_name = $xpath->evaluate('string(.//th)',$attr_header);
                     //   var_dump($attr_name);
                    }

                    var_dump($attr_headers);

                    //attributes values
                    $attr_values = [];
                    $row = 0;
                    foreach($xpath->evaluate('.//tr[contains(@class,"wish-item-container")]//td',$attributes_container) as $source_attr_values) {
                         //var_dump('attributes values ');

                        if (isset($attr_headers[$row])) {
                          //  var_dump($source_attr_values->nodeValue);
                            $attr_value = str_replace('"','',$source_attr_values->nodeValue);
                            $attr_values[$row] = $attr_value;
                        }

//                        $attr_value = $source_attr_values->nodeValue;
//
//                        if (in_array($attr_name, $allowed_attr_names)) {
//                            $attr_headers[] = $attr_name;
//                        }

                        //  $attr_name = $xpath->evaluate('string(.//th)',$attr_header);
                        //   var_dump($attr_name);
                        $row++;
                    }

                    var_dump($attr_values);


                }
                $assoc_attributes = [];
                foreach ($attr_headers as $key => $val) {
                    $assoc_attributes[$val] = $attr_values[$key];
                }

//                var_dump($name);
//                var_dump($img);
//                var_dump($assoc_attributes);
//                var_dump($items);


                if (!$name || !$img) {//|| !$descr
                    continue;
                }
             //   continue;
                $category = str_replace(' ','-',strtolower($category));

                $items['name'][] = $name;
                $items['img'][] = $img;
                $items['descr'][] = $descr;
                $items['attributes'][] = $assoc_attributes;
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

function prepare_csv_data($products) {
    $csv_data = [];
    $csv_data[] = array('title','img','descr','product_category', 'code', 'width', 'height','depth');

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

        $code = '';
        $width = '';
        $height = '';
        $depth = '';
        var_dump($products);
      //  exit;
        if (isset($product['attributes'])) {
            foreach ($product['attributes'] as $key => $val) {
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
        }

        $product_category = $product['category'];
        $csv_data[] = array($product['name'], $product['img'], $descr, $product_category, $code, $width, $height, $depth);
      //  exit;
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
