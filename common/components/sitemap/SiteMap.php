<?php

namespace common\components\sitemap;

use common\components\multilocal\Local;
use common\models\LocalCategory;
use common\models\Product;
use common\models\ProductVariant;
use frontend\models\ProductSearch;
use Yii;

/**
 * Generate sitemap
 */
class SiteMap
{
    const FILE_RU = 'sitemap.ru.xml';
    const FILE_EU = 'sitemap.eu.xml';

    public static function getFileName()
    {
        return Local::choose([
            Local::RU => self::FILE_RU,
            Local::EU => self::FILE_EU,
        ]);
    }

    public static function generate($filename, $homeUrl) {

        $file = Yii::getAlias('@extras').'/'.$filename;

//        if (file_exists($file) && date('d-m-y') == date('d-m-y', filemtime($file))) {
//            echo "\n".'Sitemap.xml has not been generated cause filetime had not been changed '."\n";
//            return false;
//        }

            $body = '<url><loc>'.$homeUrl.'</loc><priority>1</priority></url>';

        // CATEGORIES
        $categories = LocalCategory::find()->all();
        foreach ($categories as $cat) {
            /**
             * @var $cat LocalCategory
             */
            $path = $cat->getUrl(true);
            if (!empty($path)) {
                $body .= "\n";
                $url = $homeUrl . $path;
                $body .= '<url><loc>'.$url.'</loc><priority>0.8</priority></url>';
            }
        }

        // PRODUCTS
        $price = 'price_rub'; // 'price_rub'
        $products = ProductVariant::find()->where(1)
            ->innerJoin('product', 'product.product_id = product_variant.product_id')
            ->andWhere(['product.status' => 0])
            ->innerJoin('price', 'price.variant_id = product_variant.variant_id')
            ->andWhere(['>', 'price.'.$price, 0])
            ->groupBy('product_variant.product_id')
            ->all()
        ;
        foreach ($products as $p) {
            /** @var $p ProductVariant */

            $path = $p->product->getUrl(true);
            if (!empty($path)) {
                $url = $homeUrl . $path;
                $body .= "\n".'<url><loc>'.$url.'</loc><priority>0.8</priority></url>';
            }
        }


        $body .= "\n";

        $sitemap = implode("\n", [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<?xml-stylesheet type="text/xsl" href="'.$homeUrl.'/sitemap.xsl"?>',
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">',

            $body,

            '</urlset>',
        ]);


        echo $file;
        file_put_contents($file, $sitemap);

        echo "\n".'# '.__CLASS__.'::'.__FUNCTION__.' - success '."\n"."\n";
        return $sitemap;
    }
}
