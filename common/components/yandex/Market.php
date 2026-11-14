<?php

namespace common\components\yandex;

use common\components\multilocal\Local;
use common\models\LocalCategory;
use common\models\ProductVariant;

class Market
{
    public static function generateYml()
    {
        header('Content-type: text/html; charset=utf-8');
        mb_internal_encoding('UTF-8');
        $output = '<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE yml_catalog SYSTEM "shops.dtd">' . "\n";
        $output .= '<yml_catalog date="' . date('Y-m-d H:i') . '">' . "\n";
        $output .= '<shop>';
        $output .= '
            <name>Mad Wave</name>
            <company>ООО "Фан Спорт"</company>
            <url>https://www.madwave.ru</url>
            <platform>Drupal</platform>
            <version>7.39</version>
            <agency>Веб-студия Созонова Алексея</agency>
            <email>mail@sozonov-alexey.ru</email>
            <currencies><currency id="RUR" rate="1"/></currencies>' . "\n"
        ;


        if ($categories = LocalCategory::find()->all())
        {
            $output .= '<categories>' . "\n";

            foreach ($categories as $category)
            {
                /**
                 * @var $category LocalCategory
                 */

                $id = 'id="'.$category->id.'"';
                $parent_id = empty($category->parent_id) ? '' : ('parentId="'.$category->parent_id.'" ');
                $title = $category->title;

                if (!empty($category->title)) {
                    $output .= "<category $id $parent_id>$title</category>\n";
                }
            }
            $output .= '</categories>' . "\n";

            unset($id, $parent_id, $title);
        }

        $products = ProductVariant::find()->where(1)
            ->innerJoin('product', 'product.product_id = product_variant.product_id')
            ->andWhere(['product.status' => 0])
            ->innerJoin('price', 'price.variant_id = product_variant.variant_id')
            ->andWhere(['>', 'price.price_rub', 0])
            ->rightJoin('variant_stock vs', 'vs.variant_id = product_variant.variant_id')
            ->andWhere(['vs.store_id' => Local::choose([
                Local::RU => '00005',
                Local::EU => '00049',
            ])])
            ->andWhere(['>', 'vs.quantity', 0])
            ->groupBy('product_variant.variant_id')
            ->all();

        if ($products) {
            $output .= '<offers>' . "\n";
            foreach ($products as $key => $product)
            {
                /**
                 * @var $product ProductVariant
                 */
                $p = $product->product;
                $category = $p->getLocalCategory();

                $prices = $product->getPrices();

                $price = $prices['new'];
                $oldPrice = $prices['old'];

                $picture = $product->getImage(1, $product::IMG_LARGE, true, true);

                if (!empty($category) && $price > 0 && !empty($picture)) {

                    $output .= '<offer id="' . $product->variant_id . '" available="true" group_id="' . $p->id . '">' . "\n";
                    $output .= '<url>'.$product->getUrl().'</url>'."\n";



                    $output .= '<price>'.$price.'</price>'."\n";
                    if($oldPrice > $price) {
                        $discount = 100 - ($price / ($oldPrice / 100));
                        if ($discount < 95 && $discount > 5) {
                            $output .= '<oldprice>'.$oldPrice. '</oldprice>'."\n";
                        }
                    }

                    $output .= '<currencyId>RUR</currencyId>'."\n";
                    $output .= '<categoryId>' . $category->id . '</categoryId>' . "\n";
                    $output .= '<picture>'.$picture.'</picture>'."\n";
                    $output .= '<name>'.self::replaceString($category->yandex_market_title.' '.$p->name).'</name>'."\n";
                    $output .= '<vendor>Mad Wave</vendor>'."\n";

                    $output .= '<model>' . self::replaceString($category->yandex_market_title . ' ' . $p->name) .'</model>' . "\n";

                    $description = strip_tags($p->desc_ru);
                    $description = str_replace("&nbsp;", " ", $description);

                    $output .= '<description>'. self::replaceString($description) .'</description>'."\n";

                    $int = [
                        '3XS',
                        '2XS',
                        'XXS',
                        'XS',
                        'S',
                        'M',
                        'L',
                        'XL',
                        'XXL',
                        '2XL',
                        '3XL',
                    ];

                    $size = $product->size->name;

                    if (!empty($size)) {

                        $explode = explode('-', $size);

                        if (isset($explode[0], $explode[1])) {
                            if (in_array($explode[1], $int)) {
                                $output .= '<param name="Размер" unit="INT">' . $explode[1] . '</param>' . "\n";
                                $output .= '<param name="Размер" unit="height">' . $explode[0] . '</param>' . "\n";
                            } else {
                                $output .= '<param name="Размер" unit="RU">' . $size . '</param>' . "\n";
                            }

                        } else {
                            if (in_array($size, $int)) {
                                $output .= '<param name="Размер" unit="INT">' . $size . '</param>' . "\n";
                            } else {
                                $output .= '<param name="Размер">' . $size . '</param>' . "\n";
                            }
                        }
                    }



                    $output .= '<param name="Цвет">' . $product->color->name_ru . '</param>' . "\n";
                    $output .= '</offer>' . "\n";
                }

            }
            $output .= '</offers>' . "\n";
        }

        $output .= '</shop>';
        $output .= '</yml_catalog>';
        file_put_contents('/var/www/admin/www/yii2.madwave.ru/frontend/web/sites/default/files/yml/yandex.yml', "\xEF\xBB\xBF" . $output);
    }

    private static function replaceString($string)
    {
        $string = str_replace('"', '&quot;', $string);
        $string = str_replace('&', '&amp;', $string);
        $string = str_replace('>', '&gt;', $string);
        $string = str_replace('<', '&lt;', $string);
        $string = str_replace('\'', '&apos;', $string);

        return $string;
    }
}