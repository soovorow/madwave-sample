<?php

namespace common\components\importer;

use common\components\MailChimp;
use common\models\AvailabilitySubscriber;
use common\models\Product;
use common\models\products\Balance;
use common\models\products\Category;
use common\models\products\Color;
use common\models\products\Currency;
use common\models\products\Fob;
use common\models\products\FobRegion;
use common\models\products\Price;
use common\models\products\ProductAttribute;
use common\models\products\Size;
use common\models\products\Store;
use common\models\products\Vendor;
use common\models\ProductVariant;

/**
 * Import 1c database data
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class Importer
{
    public static function startUp($total_time = 0)
    {
        if ($total_time > 100) return null;

        $time_start = microtime(true);
        $dependency = [
            'colors',
            'stores',
            'currency',
            'sizes',
            'category',
            //'Vendors',
            'catalog',
            'balance',
            'b2b_price',
        ];

        Product::getDb()->createCommand('SET FOREIGN_KEY_CHECKS=0;')->execute();

        foreach ($dependency as $filename) {
            $f = 'import_'.strtolower($filename);
            $run = 'false';
            if (self::validateFile($filename)) {
                $response = self::import($filename, $f);
                if (!empty($response)) {
                    echo  print_r($response, true);
                }
                $run = true;
            }
            echo $f.' - has benn run: '. $run ."\n";
        }

        Product::getDb()->createCommand('SET FOREIGN_KEY_CHECKS=1;')->execute();

        self::notifySubscribers();

        self::generateSlugs();


        $time_end = microtime(true);
        $execution_time = (double) substr(($time_end - $time_start), 0, 6);
        $total_time += $execution_time;
        echo '<b>Total Execution Time:</b> '. $execution_time.' sec. Total recursion time: ' .$total_time .'sec.';
    }

    private static function generateSlugs()
    {
        $products = Product::find()->where(['slug' => null])->all();
        foreach ($products as $p) {
            /**
             * @var $p Product
             */
            $p->generateSlug();
        }
    }

    private static function notifySubscribers() {
        $subscribers = AvailabilitySubscriber::find()->all();

        foreach ($subscribers as $s) {
            $v = ProductVariant::findOne($s->sku);
            if ($v && $v->hasInStock()) {
                if ($s->validate('email')) {
                    \Yii::$app
                        ->mailer
                        ->compose(['html' => 'productAvailable-html', 'text' => 'productAvailable-text'], ['v' => $v])
                        ->setFrom(\Yii::$app->params['adminEmail'])
                        ->setTo($s->email)
                        ->setSubject('Mad Wave - интересующий вас товар снова в наличии')
                        ->send();
                }
                $s->delete();
            }
        }
    }

    public static function getImportDir()
    {
        return '/var/www/admin/www/stash.madwave.ru/1C/export';
    }

    private function validateFile($fileName)
    {
        $h = History::findOne(['filename' => $fileName]);

        if ($h && $fileTime = $h->filetime) {
            if (filectime(self::getImportDir().'/'.$fileName.'.xml') > $fileTime ) {
                $h->filetime = time();
                $h->save(false);
                return true;
            }
            return false;
        }

        $h = new History(['filename' => $fileName, 'filetime' => time()]);
        $h->save(false);
        return true;
    }

    private function import($fileName, $functionName)
    {
        $xml = @simplexml_load_file(self::getImportDir().'/'.$fileName.'.xml');
        if ($xml) {
            return self::$functionName($xml);
        }
        echo 'failed simplexml';
        return false;
    }

    private function import_colors($xml)
    {
        $errors = [];
        foreach ($xml->{'Цвета'}->{'Цвет'} as $v) {
            $color_id = (string)$v->attributes()->{'Код'};
            $name_ru = (string)$v->attributes()->{'Имя'};
            $name_en = (string)$v->attributes()->{'Color'};
            if (!empty($color_id)) {
                $color = Color::findOne(['color_id' => $color_id]);
                if (!$color) {
                    $color = new Color();
                }
                $color->color_id = $color_id;
                $color->name_ru = $name_ru;
                $color->name_en = $name_en;
                $color->timestamp = time();
                $color->save();
            } else {
                $errors[] = 'В элементе не указан "Код"';
            }
        }

        return $errors;
    }

    private function import_stores($xml)
    {
        $errors = [];
        foreach ($xml->{'Склады'}->{'Склад'} as $v) {
            $store_id = (string)$v->attributes()->{'Код'};
            $name = (string)$v->attributes()->{'Имя'};
            if (!empty($store_id)) {
                $store = Store::findOne(['store_id' => $store_id]);
                if (!$store) {
                    $store = new Store();
                }
                $store->store_id = $store_id;
                $store->name = $name;
                $store->timestamp = time();
                $store->save(false);
            } else {
                $errors[] = 'В элементе не указан "Код"';
            }
        }
        return $errors;
    }

    private function import_currency($xml)
    {
        $errors = [];
        foreach ($xml->{'Валюты'}->{'Валюта'} as $v) {
            $code = (string)$v->attributes()->{'Валюта'} == 'руб.' ? 'RUB' : (string)$v->attributes()->{'Валюта'};
            $suffix = (string)$v->attributes()->{'Валюта'};
            $quote = (string)$v->attributes()->{'Курс'};
            if (!empty($code)) {
                $c = Currency::findOne(['code' => $code]);
                if (!$c) {
                    $c = new Currency();
                }
                $c->code = $code;
                $c->suffix = $suffix;
                $c->quote = $quote;
                $c->timestamp = time();
                $c->save();
            } else {
                $errors[] = 'В элементе не указан "Валюта"';
            }
        }
        return $errors;
    }

    private function import_sizes($xml)
    {
        $errors = [];
        foreach ($xml->{'Размеры'}->{'Размер'} as $v) {
            $size_id = (string)$v->attributes()->{'Код'};
            if (!empty($size_id)) {
                $size = Size::findOne(['size_id' => $size_id]);
                if (!$size) {
                    $size = new Size();
                }
                $size->size_id = $size_id;
                $size->name = (string)$v->attributes()->{'Имя'};

                foreach ($v->{'Свойства'}->{'Свойство'} as $opt) {
                    $opt_val = (string)$opt->attributes()->{'Значение'};
                    switch ((string)$opt->attributes()->{'Имя'}) {
                        case 'Возраст': $size->age = $opt_val; break;
                        case 'Высота посадки': $size->seat_height = $opt_val; break;
                        case 'Обхват бедер': $size->hip_girth = $opt_val; break;
                        case 'Обхват талии': $size->waist_grith = $opt_val; break;
                        case 'Размер FR': $size->size_fr = $opt_val; break;
                        case 'Размер MW (Наименование)': $size->size_mw = $opt_val; break;
                        case 'Размер RUS': $size->size_rus = $opt_val; break;
                        case 'Рост': $size->height = $opt_val; break;
                    }
                }
                $size->timestamp = time();
                $size->save(false);
            } else {
                $errors[] = 'В элементе не указан "Код"';
            }
        }
        return $errors;
    }

    private function import_category($xml)
    {
        $errors = [];

        foreach ($xml->{'Категории'}->{'Категория_1'} as $v) {
            $v_id = (string)$v->attributes()->{'Код'};
            if (!empty($v_id)) {

                $category = Category::findOne(['category_id' => $v_id]);
                if (!$category) {
                    $category = new Category();
                    $category->category_id = $v_id;
                }
                $category->parent_id = null;
                $category->name_ru = (string)$v->attributes()->{'Имя'};
                $category->name_en = (string)$v->attributes()->{'Англ'};
                $category->level = 1;
                $category->path = '/' . $v_id . '/';
                $category->timestamp = time();
                $category->save(false);

                foreach ($v->{'Категории_1'}->{'Категория_2'} as $v2) {

                    $v2_id = (string)$v2->attributes()->{'Код'};

                    if ($v2_id !== $v_id && !empty($v2_id)) {

                        $category = Category::findOne(['category_id' => $v2_id, 'parent_id' => $v_id]);
                        if (!$category) {
                            $category = new Category();
                        }
                        $category->category_id = $v2_id;
                        $category->parent_id = $v_id;
                        $category->name_ru = (string)$v2->attributes()->{'Имя'};
                        $category->name_en = (string)$v2->attributes()->{'Англ'};
                        $category->level = 2;
                        $category->path = '/' . $v_id . '/' . $v2_id . '/';
                        $category->timestamp = time();
                        $category->save(false);

                        foreach ($v2->{'Категории_2'}->{'Категория'} as $v3) {

                            $v3_id = (string)$v3->attributes()->{'Код'};

                            if ($v3_id !== $v2_id && !empty($v3_id)) {

                                $category = Category::findOne(['category_id' => $v3_id, 'parent_id' => $v2_id]);
                                if (!$category) {
                                    $category = new Category();
                                }
                                $category->category_id = $v3_id;
                                $category->parent_id = $v2_id;
                                $category->name_ru = (string)$v3->attributes()->{'Имя'};
                                $category->name_en = (string)$v3->attributes()->{'Англ'};
                                $category->level = 3;
                                $category->path = '/' . $v_id . '/' . $v2_id . '/' . $v3_id . '/';
                                $category->timestamp = time();
                                $category->save(false);
                            } else {
                                $errors[] = 'В элементе не указан "Код"';
                            }
                        }
                    } else {
                        $errors[] = 'В элементе не указан "Код"';
                    }
                }
            }
        }

        Category::deleteAll(['<', 'timestamp', strtotime('-1 day')]);

        return $errors;
    }

//    private function import_vendors($xml)
//    {
//
//        $errors = [];
//        foreach ($xml->{'Vendors'}->{'Region'} as $v) {
//            $region_id = (string)$v->{'regname'};
//            if (!empty($region_id))
//            {
//                $fobRegion = FobRegion::findOne(['region_id' => $region_id]);
//                if (!$fobRegion) {
//                    $fobRegion = new FobRegion();
//                }
//                $fobRegion->region_id = $region_id;
//                $fobRegion->timestamp = time();
//                $fobRegion->save(false);
//
//                foreach ($v as $f)
//                {
//                    $fob_id = (string)$f->{'fobname'};
//                    if (!empty($fob_id)) {
//
//                        $fob = Fob::findOne(['fob_id' => $fob_id]);
//                        if (!$fob) {
//                            $fob = new Fob();
//                        }
//                        $fob->fob_id = $fob_id;
//                        $fob->region_id = (string)$v->{'regname'};
//                        $fob->timestamp = time();
//                        $fob->save(false);
//                        foreach ($f as $ve)
//                        {
//                            $vendor_id = (string)$ve->{'id'};
//                            if (!empty($vendor_id)) {
//                                $vendor = Vendor::findOne(['vendor_id' => $vendor_id]);
//                                if (!$vendor) {
//                                    $vendor = new Vendor();
//                                }
//                                $vendor->vendor_id = $vendor_id;
//                                $vendor->fob_id = (string)$f->{'fobname'};
//                                $vendor->name = (string)$ve->{'name'};
//                                $vendor->print_name = trim((string)$ve->{'ПечИмя'});
//                                $vendor->min_sum = (string)$ve->{'min_summ'};
//                                $vendor->surcharge = (string)$ve->{'surcharge'};
//                                $vendor->alias = (string)$ve->{'alias'};
//                                $vendor->vendor_email = (string)$ve->{'vendor_email'};
//                                $vendor->timestamp = time();
//                                $vendor->save(false);
//                            } else {
//                                $errors[] = 'В элементе не указан "id"';
//                            }
//                        }
//                    } else {
//                        $errors[] = 'В элементе не указан "fobname"';
//                    }
//                }
//            } else {
//                $errors[] = 'В элементе не указан "regname"';
//            }
//        }
//        return $errors;
//    }

    private function import_catalog($xml)
    {
        $array = [];
        $sku = [];
        $attr = [];
        $errors = [];
        foreach ($xml->{'Товары'}->{'Товар'} as $v) {
            $product_id = (string)$v->attributes()->{'id'};
            if (!empty($product_id)) {
                $product = Product::findOne(['product_id' => $product_id]);
                if (!$product) {
                    $product = new Product();
                }
                $product->product_id = $product_id;
                $product->category_id = (string)$v->{'Категория'};
                $product->vendor_id = (string)$v->{'Vendor'};
                $product->brand = (string)$v->{'brand'};
                $product->activity_id = (string)$v->{'Activity'};
                $product->video = (string)$v->{'Видео'};
                $product->unit = (string)$v->{'Единица'};
                $product->box_capacity = (string)$v->{'box_capacity'};
                $product->min_boxes = (string)$v->{'min_boxes'};
                $product->min_pcs = (string)$v->{'min_pcs'};
                $product->comment = (string)$v->{'Комментарий'};
                $product->TNVED = (string)$v->{'ТНВЭД'};
                $product->ce = (string)$v->{'CE'};
                $product->eac = (string)$v->{'EAC'};
                $product->is_on_sale = (string)$v->{'Sale'};
                $product->is_new = (string)$v->{'NewArrival'};
                $product->is_bestseller = (string)$v->{'BestSeller'};
                $product->type = (string)$v->{'ВидТовара'};
                $product->setDescRu((string)$v->{'Описание'});
                $product->setDescEn((string)$v->{'ОписаниеАнгл'});
                $product->compositions = (string)$v->{'Состав'};
                $product->compositions_en = (string)$v->{'composition'};
                $product->name = (string)$v->{'НаименованиеАнгл'};
                $product->timestamp = time();
                $product->save(false);

                foreach ($v->{'ОсобенныеПризнаки'}->{'Признак'} as $p)
                {
                    $attribute_id = (string)$p;
                    if (!empty($attribute_id)) {
                        $productAttribute = ProductAttribute::findOne([
                            'attribute_id' => $attribute_id,
                            'product_id' => $product_id
                        ]);
                        if (!$productAttribute) {
                            $productAttribute = new ProductAttribute();
                        }
                        $productAttribute->product_id = $product_id;
                        $productAttribute->attribute_id = $attribute_id;
                        $productAttribute->timestamp = time();
                        $productAttribute->save(false);
                    }
                }

                foreach ($v->{'Варианты'}->{'Вариант'} as $s)
                {
                    $product_id = (string)$v->attributes()->{'id'};
                    $size_id = (string)$s->{'Размер'};
                    $color_id = (string)$s->{'ЦветОсновной'};
                    $variant_id = (string)$s->{'ИД'};
                    if (!empty($variant_id)) {
                        $productVariant = ProductVariant::findOne(['variant_id' => $variant_id]);
                        if (!$productVariant) {
                            $productVariant = new ProductVariant();
                        }
                        $productVariant->product_id = $product_id;
                        $productVariant->variant_id = $variant_id;
                        $productVariant->color_id = $color_id;
                        $productVariant->size_id = $size_id;
                        $productVariant->gender = (string)$s->{'Пол'};
                        $productVariant->vendore_code = (string)$s->{'Артикул'};
                        $productVariant->weight = (string)$s->{'Вес'};
                        $productVariant->gross_weight = (string)$s->{'ВесБрутто'};
                        $productVariant->volume = (string)$s->{'Объем'};
                        $productVariant->preorder = (string)$s->{'preorder'};
                        $productVariant->stock = (string)$s->{'Stock'};
                        $productVariant->purchase_vendor = (string)$s->{'purchase_fv'};
                        $productVariant->barcode = (string)$s->{'ШтрихКод'};
                        $productVariant->asin = (string)$s->{'ASIN'};
                        $productVariant->min_order = (string)$s->{'МинЗаказПозиция'};
                        $productVariant->timestamp = time();
                        $productVariant->status = 0;
                        $productVariant->save(false);

//                        $mc = new MailChimp();
//                        $mc->checkProduct($productVariant);
                    }
                }
            }
        }

//        ProductVariant::deleteAll(['<', 'timestamp', strtotime('-1 day')]);

        ProductVariant::updateAll([
            'status' => 1,
            'purchase_vendor' => null,
            'preorder' => 0,
        ], ['<', 'timestamp', strtotime('-1 day')]);

        return $errors;
    }

    private function import_balance($xml)
    {
        $errors = [];

        $mailChimp = new MailChimp();

        foreach ($xml->{'Остатки'}->{'ОстатокТовар'} as $v)
        {
            $variant_id = (string)$v->attributes()->{'Товар'};
            foreach ($v->{'ОстатокСклад'} as $q) {
                $store_id = (string)$q->attributes()->{'Склад'};
                $_quantity = (string)$q->attributes()->{'Остаток'};
                $quantity = empty($_quantity) ? 0 : $_quantity;


                $balance = Balance::findOne([
                    'store_id' => $store_id,
                    'variant_id' => $variant_id
                ]);

                if (!$balance) {
                    $balance = new Balance();
                }

                if ($balance->quantity <> $quantity) {
                    try {
                        if ($v = ProductVariant::findOne($variant_id)) {
                            $mailChimp->updateProductQuantity($v, $quantity);
                        }
                    } catch (\Exception $e) {
                        \Yii::error('Can\'t update mailchimp product quantity');
                    }
                }

                $balance->store_id = $store_id;
                $balance->variant_id = $variant_id;
                $balance->quantity = $quantity;
                $balance->timestamp = time();

                if ($balance->holded_at > 0) {
                    if (!(time() <= $balance->holded_at + 10 * 60)) {
                        $balance->holded_at = null;
                        $balance->save(false);
                    }
                } else {
                    $balance->save(false);
                }
            }
        }

        Balance::deleteAll(['<', 'timestamp', strtotime('-1 day')]);

        return $errors;
    }

    private function import_b2b_price($xml)
    {
        $errors = [];
        $mailChimp = new MailChimp();
        foreach ($xml->{'Цены'}->{'Цена'} as $v)
        {
            $variant_id = (string)$v->attributes()->{'ИД'};
            $price = Price::findOne(['variant_id' => $variant_id]);
            $price_rub = (string)$v->attributes()->{'ЦенаРР'};

            if (!$price) {
                $price = new Price();
            }

            if (time() < 1508284800 || $price->price_rub <> $price_rub) {
                try {
                    if ($b = ProductVariant::findOne($variant_id)) {
                        $mailChimp->updateProductPrice($b, $price_rub);
                    }
                } catch (\Exception $e) {
                    \Yii::error('Can\'t update mailchimp product price');
                }
            }

            $price->variant_id = (string)$v->attributes()->{'ИД'};
            $price->price_rub = $price_rub;
            $price->price_rub_choky = (string)$v->attributes()->{'Таможня'};
            $price->price_rub_sale = (string)$v->attributes()->{'ЦенаСток'};
            $price->price_rub_wh = (string)$v->attributes()->{'Оптовая_руб'};
            $price->price_eur_wh = (string)$v->attributes()->{'EURO_WHOLESALE'};
            $price->price_eur_rrp = (string)$v->attributes()->{'RRP_EUR'};
            $price->price_usd_wh = (string)$v->attributes()->{'Оптовая'};
            $price->price_usd_rrp = (string)$v->attributes()->{'RRP_USD'};
            $price->price_usd_purchasing = (string)$v->attributes()->{'Закупочная'};
            $price->price_usd_preorder = (string)$v->attributes()->{'Preorder_usd'};
            $price->price_eur_preorder = (string)$v->attributes()->{'Preorder_int_EURO'};
            $price->price_usd_vendor = (string)$v->attributes()->{'Vendor'};
            $price->price_usd_int_wh = (string)$v->attributes()->{'wh_int_usd'};
            $price->timestamp = time();
            $price->save(false);
        }
        return $errors;
    }
}
