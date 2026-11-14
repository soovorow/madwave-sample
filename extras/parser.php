<?php
$global_timestamp = time();
$mem0 = memory_get_usage();
$con = mysqli_connect('localhost', 'products', 'REDACTED_BEFORE_PUBLICATION', 'products');
?>
    <pre><?php import() ?></pre>
<?php
mysqli_close($con);
function get_import_dir() {
    return '/var/www/admin/www/madwave.ru/sites/default/files/1s_import';
}
function import($total_time = 0) {
    if ($total_time > 100) {
        return null;
    }
    $time_start = microtime(true);
    $dependency = [
        'activity',
        'attributes',
        'colors',
        'stores',
        'currency',
        'sizes',
        'category',
        'Vendors',
        'catalog',
        'balance',
        'b2b_price',
    ];
    foreach ($dependency as $filename) {
        $f = 'import_' . strtolower($filename);
        $run = 'false';
        if (validate_file($filename)) {
            prepare_to_import($filename, $f);
            $run = 'true';
        }
        echo 'The function <b>'.$f.'</b> has benn run: '. $run ."\n";
    }
//    sleep(10);
    $time_end = microtime(true);
    $execution_time = (double) substr(($time_end - $time_start), 0, 6);
    $total_time += $execution_time;
    notifySubscribers();
    createSlugs();
    echo '<b>Total Execution Time:</b> '. $execution_time.' sec. Total recursion time: ' .$total_time ."sec.\n";
//    import($total_time);
}

function validate_file($filename) {
    $new_filetime = filectime(get_import_dir().'/'.$filename.'.xml');
    $query[]['query'] = 'SELECT filetime FROM __history WHERE filename="'.$filename.'";';
    if (isset(db_query($query, true)[0]['result']) && is_object(db_query($query, true)[0]['result'])) {
        $old_filetime = db_query($query, true)[0]['result']->filetime;
    }
    if (isset($old_filetime) && !empty($old_filetime)) {
        if ( $new_filetime > $old_filetime ) {
            $query[]['query'] = 'INSERT INTO __history (`filename`, `filetime`) VALUES ("'.$filename.'", "'.time().'") ON DUPLICATE KEY UPDATE filetime="'.time().'";';
            db_query($query);
            return true;
        }
        return false;
    }
    $query[]['query'] = 'INSERT INTO __history (`filename`, `filetime`) VALUES ("'.$filename.'", "'.time().'");';
    db_query($query);
    return true;
}

function prepare_to_import($filename, $function) {
    $xml = @simplexml_load_file(get_import_dir().'/'.$filename.'.xml');
    if ($xml) {
        return call_user_func($function, $xml);
    }
    echo "simplexml_load_file has been crashed \n";
    return false;
}

function notifySubscribers() {
    shell_exec('php /var/www/admin/www/yii2.madwave.ru/yii notification/product-available');
}

function createSlugs() {
    shell_exec('php /var/www/admin/www/yii2.madwave.ru/yii action/generate-slugs');
}

function db_insert_all($array, $table) {
    $result = db_query(db_construct_queries($array, $table));
//    global $global_timestamp;
//    $delete_result = db_query([
//        ['query' => 'SET FOREIGN_KEY_CHECKS=0;'],
//        ['query' => 'DELETE FROM '.$table.' WHERE `timestamp` < '.$global_timestamp.' OR `timestamp` IS NULL'],
//        ['query' => 'SET FOREIGN_KEY_CHECKS=1;'],
//    ]);
//    print_r($delete_result);
}

function db_construct_queries($array, $table) {

    if (empty($table)) {
        echo "The table name does not specified \n";
        return false;
    }

    if (empty($array)) {
        echo "Queries does not specified \n";
        return false;
    }

    $query = [];

    foreach ($array as $key => $a) {

        $query[$key]['table'] = $table;
        $keys = array_keys($array[0]);
        $keys_count = count($keys);
        $into = '';
        $values = '';
        $update = '';

        foreach ($keys as $i => $k) {

            $comma = ',';

            $into.= '`'.$k.'`'.$comma;

            if (empty($a[$k])) {
                $values.= 'NULL'.$comma;
                $update.= $k.'=NULL'.$comma;
            } else {
                $values.= '"'.str_replace('"', '\"', $a[$k]).'"'.$comma;
                $update.= $k.'="'.str_replace('"', '\"', $a[$k]).'"'.$comma;
            }

        }

        $timestamp = time();

        $query[$key]['query'] = 'INSERT INTO '.$table.' ('.$into.' `timestamp`) VALUES('.$values.' '.$timestamp.') ON DUPLICATE KEY UPDATE '.$update.' `timestamp`='.$timestamp;
    }

    return $query;
}

function db_query($query = [], $success_result = false) {

    if (empty($query[0]['query'])) {
        echo 'The query does not specified <br>';
        return false;
    }

    if (mysqli_connect_errno()) {
        echo mysqli_connect_error();
        return false;
    }

    global $con;

    mysqli_set_charset($con,'utf8');

    //mysqli_query($con, 'SET FOREIGN_KEY_CHECKS=0;');

    foreach ($query as $i => $q) {

        $r = mysqli_query($con, $q['query']);

        if (!$r) {

            $result[$i]['table'] = $q['table'];
            $result[$i]['query'] = $query[$i]['query'];
            $result[$i]['error'] = mysqli_error($con);

        } elseif ($success_result) {

            $result[$i]['result'] = $r->fetch_object();

        }
    }

    //mysqli_query($con, 'SET FOREIGN_KEY_CHECKS=1;');


    return isset($result) ? $result : null;
}

function import_activity($xml) {
    $array = [];
    $errors = [];
    foreach ($xml->{'СписокАктивити'}->{'Активити'} as $v) {
        $activity_id = (string)$v->attributes()->{'Код'};
        if (!empty($activity_id)) {
            $array[] = [
                'activity_id' => $activity_id,
                'name' => (string)$v->attributes()->{'Имя'},
            ];
        } else {
            $errors[] = 'В элементе не указан "Код"';
        }
    }
    db_insert_all($array, 'activity');
    return $errors;
}

function import_attributes($xml) {

    $array = [];
    $errors = [];

    foreach ($xml->{'Признаки'}->{'Признак'} as $v) {
        $attribute_id = (string)$v->attributes()->{'Код'};
        if (!empty($attribute_id)) {
            $array[] = [
                'attribute_id' => $attribute_id,
                'name' => (string)$v->attributes()->{'Имя'},
            ];
        } else {
            $errors[] = 'В элементе не указан "Код"';
        }
    }

    db_insert_all($array, 'attribute');

    return $errors;
}

function import_colors($xml) {

    $array = [];
    $errors = [];

    foreach ($xml->{'Цвета'}->{'Цвет'} as $v) {
        $color_id = (string)$v->attributes()->{'Код'};
        if (!empty($color_id)) {
            $array[] = [
                'color_id' => $color_id,
                'name_ru' => (string)$v->attributes()->{'Имя'},
                'name_en' => (string)$v->attributes()->{'Color'},
            ];
        } else {
            $errors[] = 'В элементе не указан "Код"';
        }
    }

    db_insert_all($array, 'color');

    return $errors;
}

function import_stores($xml) {

    $array = [];
    $errors = [];

    foreach ($xml->{'Склады'}->{'Склад'} as $v) {
        $store_id = (string)$v->attributes()->{'Код'};
        if (!empty($store_id)) {
            $array[] = [
                'store_id' => $store_id,
                'name' => (string)$v->attributes()->{'Имя'},
            ];
        } else {
            $errors[] = 'В элементе не указан "Код"';
        }

    }

    db_insert_all($array, 'store');

    return $errors;
}

function import_currency($xml) {

    $array = [];
    $errors = [];

    foreach ($xml->{'Валюты'}->{'Валюта'} as $v) {
        $code = (string)$v->attributes()->{'Валюта'} == 'руб.' ? 'RUB' : (string)$v->attributes()->{'Валюта'};
        if (!empty($code)) {
            $array[] = [
                'code' => $code,
                'suffix' => (string)$v->attributes()->{'Валюта'},
                'quote' => (string)$v->attributes()->{'Курс'},
            ];
        } else {
            $errors[] = 'В элементе не указан "Валюта"';
        }
    }

    db_insert_all($array, 'currency');

    return $errors;
}

function import_sizes($xml) {

    $array = [];
    $errors = [];

    foreach ($xml->{'Размеры'}->{'Размер'} as $v) {

        $size_id = (string)$v->attributes()->{'Код'};

        $values = [
            'size_id' => $size_id,
            'name' => (string)$v->attributes()->{'Имя'},
        ];

        foreach ($v->{'Свойства'}->{'Свойство'} as $opt) {

            $opt_val = (string)$opt->attributes()->{'Значение'};

            switch ((string)$opt->attributes()->{'Имя'}) {
                case 'Возраст': $values['age'] = $opt_val; break;
                case 'Высота посадки': $values['seat_height'] = $opt_val; break;
                case 'Обхват бедер': $values['hip_girth'] = $opt_val; break;
                case 'Обхват талии': $values['waist_grith'] = $opt_val; break;
                case 'Размер FR': $values['size_fr'] = $opt_val; break;
                case 'Размер MW (Наименование)': $values['size_mw'] = $opt_val; break;
                case 'Размер RUS': $values['size_rus'] = $opt_val; break;
                case 'Рост': $values['height'] = $opt_val; break;
            }
        }

        if (!empty($size_id)) {
            $array[] = $values;
        } else {
            $errors[] = 'В элементе не указан "Код"';
        }

    }

    db_insert_all($array, 'size');

    return $errors;
}

function import_category($xml) {

    $array = [];
    $errors = [];

    foreach ($xml->{'Категории'}->{'Категория_1'} as $v) {

        $v_id = (string)$v->attributes()->{'Код'};

        if (!empty($v_id)) {

            $array[] = [
                'category_id' => $v_id,
                'parent_id' => null,
                'name_ru' => (string)$v->attributes()->{'Имя'},
                'name_en' => (string)$v->attributes()->{'Англ'},
                'level' => 1,
                'path' => '/' . $v_id . '/',
            ];

            foreach ($v->{'Категории_1'}->{'Категория_2'} as $v2) {

                $v2_id = (string)$v2->attributes()->{'Код'};

                if ($v2_id !== $v_id && !empty($v2_id)) {

                    $array[] = [
                        'category_id' => $v2_id,
                        'parent_id' => $v_id,
                        'name_ru' => (string)$v2->attributes()->{'Имя'},
                        'name_en' => (string)$v2->attributes()->{'Англ'},
                        'level' => 2,
                        'path' => '/' . $v_id . '/' . $v2_id . '/',
                    ];

                    foreach ($v2->{'Категории_2'}->{'Категория'} as $v3) {

                        $v3_id = (string)$v3->attributes()->{'Код'};

                        if ($v3_id !== $v2_id && !empty($v3_id)) {


                            $array[] = [
                                'category_id' => $v3_id,
                                'parent_id' => $v2_id,
                                'name_ru' => (string)$v3->attributes()->{'Имя'},
                                'name_en' => (string)$v3->attributes()->{'Англ'},
                                'level' => 3,
                                'path' => '/' . $v_id . '/' . $v2_id . '/' . $v3_id . '/',
                            ];

                        } else {
                            $errors[] = 'В элементе не указан "Код"';
                        }
                    }
                } else {
                    $errors[] = 'В элементе не указан "Код"';
                }
            }
        } else {
            $errors[] = 'В элементе не указан "Код"';
        }
    }

    db_insert_all($array, 'category');

    return $errors;
}

function import_vendors($xml) {

    $errors = [];
    $fob_region = [];
    $fob = [];
    $vendor = [];

    foreach ($xml->{'Vendors'}->{'Region'} as $v) {

        $region_id = (string)$v->{'regname'};

        if (!empty($region_id)) {

            $fob_region[] = [
                'region_id' => $region_id
            ];

            foreach ($v as $f) {

                $fob_id = (string)$f->{'fobname'};

                if (!empty($fob_id)) {

                    $fob[] = [
                        'fob_id' => $fob_id,
                        'region_id' => (string)$v->{'regname'},
                    ];

                    foreach ($f as $ve) {

                        $vendor_id = (string)$ve->{'id'};

                        if (!empty($vendor_id)) {

                            $vendor[] = [
                                'vendor_id' => trim($vendor_id),
                                'fob_id' => trim((string)$f->{'fobname'}),
                                'name' => trim((string)$ve->{'name'}),
                                'print_name' => trim((string)$ve->{'ПечИмя'}),
                                'min_sum' => trim((string)$ve->{'min_summ'}),
                                'surcharge' => trim((string)$ve->{'surcharge'}),
                                'alias' => trim((string)$ve->{'alias'}),
                                'phones' => trim((string)$ve->{'Телефоны'}),
                                'actual_address' => trim((string)$ve->{'Фактический'}),
                                'vendor_email' => trim((string)$ve->{'vendor_email'})
                            ];

                        } else {
                            $errors[] = 'В элементе не указан "id"';
                        }
                    }
                } else {
                    $errors[] = 'В элементе не указан "fobname"';
                }
            }
        } else {
            $errors[] = 'В элементе не указан "regname"';
        }
    }

    db_insert_all($fob_region, 'fob_region');
    db_insert_all($fob, 'fob');
    db_insert_all($vendor, 'vendor');

    return $errors;
}

function import_catalog($xml) {

    $array = [];
    $sku = [];
    $attr = [];
    $errors = [];
    foreach ($xml->{'Товары'}->{'Товар'} as $v) {
        $product_id = (string)$v->attributes()->{'id'};
        if (!empty($product_id)) {

            $array[] = [
                'product_id' => $product_id,
                'category_id' => (string)$v->{'Категория'},
                'vendor_id' => (string)$v->{'Vendor'},
                'brand' => (string)$v->{'brand'},
                'activity_id' => (string)$v->{'Activity'},
                'video' => (string)$v->{'Видео'},
                'unit' => (string)$v->{'Единица'},
                'box_capacity' => (string)$v->{'box_capacity'},
                'min_boxes' => (string)$v->{'min_boxes'},
                'min_pcs' => (string)$v->{'min_pcs'},
                'comment' => (string)$v->{'Комментарий'},
                'TNVED' => (string)$v->{'ТНВЭД'},
                'ce' => (string)$v->{'CE'},
                'eac' => (string)$v->{'EAC'},
                'is_on_sale' => (string)$v->{'Sale'},
                'is_new' => (string)$v->{'NewArrival'},
                'is_bestseller' => (string)$v->{'BestSeller'},
                'type' => (string)$v->{'ВидТовара'},
                'desc_ru' => (string)$v->{'Описание'},
                'desc_en' => (string)$v->{'ОписаниеАнгл'},
                'compositions' => (string)$v->{'Состав'},
                'compositions_en' => (string)$v->{'composition'},
                'name' => (string)$v->{'НаименованиеАнгл'},
            ];

            foreach ($v->{'ОсобенныеПризнаки'}->{'Признак'} as $p) {

                $attr[] = [
                    'product_id' => (string)$v->attributes()->{'id'},
                    'attribute_id' => (string)$p
                ];

            }
            foreach ($v->{'Варианты'}->{'Вариант'} as $s) {

                $product_id = (string)$v->attributes()->{'id'};
                $size_id = (string)$s->{'Размер'};
                $color_id = (string)$s->{'ЦветОсновной'};
                $variant_id = (string)$s->{'ИД'};

                if (!empty($variant_id)) {
                    $sku[] = [
                        'product_id' => $product_id,
                        'variant_id' => $variant_id,
                        'color_id' => $color_id,
                        'size_id' => $size_id,
                        'gender' => (string)$s->{'Пол'},
                        'vendore_code' => (string)$s->{'Артикул'},
                        'weight' => (string)$s->{'Вес'},
                        'gross_weight' => (string)$s->{'ВесБрутто'},
                        'volume' => (string)$s->{'Объем'},
                        'preorder' => (string)$s->{'preorder'},
                        'stock' => (string)$s->{'Stock'},
                        'barcode' => (string)$s->{'ШтрихКод'},
                        'asin' => (string)$s->{'ASIN'}
                    ];
                }
            }
        }
    }

    db_insert_all($array, 'product');
    db_insert_all($attr, 'product_attribute');
    db_insert_all($sku, 'product_variant');

    return $errors;
}

function quantityIsHold($variant_id, $store_id) {
    $holded_at = db_query([['query' => 'SELECT holded_at FROM `variant_stock` WHERE store_id="'.$store_id.'" AND variant_id = '.$variant_id]], true);

    if (isset($holded_at[0]['result']) && is_object($holded_at[0]['result'])) {
        $holded_at = $holded_at[0]['result']->holded_at;
        if ($holded_at > 0) {
            if (time() <= $holded_at + 10 * 60) {
                print_r($variant_id);
                return true;
            } else {
                db_query([['query' => 'UPDATE variant_stock SET holded_at = NULL WHERE store_id="'.$store_id.'" AND variant_id = '.$variant_id]]);
            }
        }
    }

    return false;
}

function import_balance($xml) {
    $array = [];
    $errors = [];

    foreach ($xml->{'Остатки'}->{'ОстатокТовар'} as $v) {

        $quantity = [
            'variant_id' => (string)$v->attributes()->{'Товар'},
        ];


        foreach ($v->{'ОстатокСклад'} as $q) {
            $quantity['store_id'] = (string)$q->attributes()->{'Склад'};

            $getQuantity = (string)$q->attributes()->{'Остаток'};
            if(empty($getQuantity))
                $getQuantity = 0;

            $quantity['quantity'] = $getQuantity;

            if (!quantityIsHold($quantity['variant_id'], $quantity['store_id'])) {
                $array[] = $quantity;
//                if ($quantity['variant_id'] == '10015972' && $quantity['store_id'] == '00005') {
//                    echo ' POINT!!! '.print_r($quantity, true);
//                }
            }
        }

    }

    db_insert_all($array, 'variant_stock');

    return $errors;
}

function import_b2b_price($xml) {
    $array = [];
    $errors = [];

    foreach ($xml->{'Цены'}->{'Цена'} as $v) {

        $price = [
            'variant_id' => (string)$v->attributes()->{'ИД'},
            'price_rub' => (string)$v->attributes()->{'ЦенаРР'},
            'price_rub_choky' => (string)$v->attributes()->{'Таможня'},
            'price_rub_sale' => (string)$v->attributes()->{'ЦенаСток'},
            'price_rub_wh' => (string)$v->attributes()->{'Оптовая_руб'},
            'price_eur_wh' => (string)$v->attributes()->{'EURO_WHOLESALE'},
            'price_eur_rrp' => (string)$v->attributes()->{'RRP_EUR'},
            'price_usd_wh' => (string)$v->attributes()->{'Оптовая'},
            'price_usd_rrp' => (string)$v->attributes()->{'RRP_USD'},
            'price_usd_purchasing' => (string)$v->attributes()->{'Закупочная'},
            'price_usd_preorder' => (string)$v->attributes()->{'Preorder_usd'},
            'price_usd_int_wh' => (string)$v->attributes()->{'wh_int_usd'},
        ];

        $array[] = $price;

    }
    db_insert_all($array, 'price');

    return $errors;
}

echo "Script memori: ".(memory_get_usage()-$mem0-sizeof($mem0))." bytes \n";