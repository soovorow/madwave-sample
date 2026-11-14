<?php

namespace frontend\controllers;

use common\components\MailChimp;
use common\models\Order;
use common\models\ProductVariant;
use DrewM\MailChimp\Batch;
use yii\web\Controller;

class ExtrasController extends Controller
{
    public function actionExportTracks()
    {
//        $orders = Order::find()->all();
//        foreach ($orders as $o) {
//            if (!empty($o->track_code)) {
//                $o->exportTrackCode();
//            }
//        }

    }

    public function actionMailChimpUpdate()
    {
//        $mailChimp = new MailChimp();
//        $store_id = 'MWPROD';
//        $response = $mailChimp->get("ecommerce/stores/$store_id/products", [
//            'count' => 3000
//        ]);
//        $batch = $mailChimp->new_batch();
//
//        return print_r($batch);
//        $i = 1;
//        foreach ($response['products'] as $product)
//        {
//            $p = [];
//            $variants = $product['variants'];
//
//            foreach ($variants as $k => $variant)
//            {
//                if ($v = ProductVariant::findOne($variant['sku']))
//                {
//                    $url = $v->getUrl();
//                    $image_url = $v->getImage(1, $v::IMG_LARGE, true);
//
//                    $variants[$k]['price'] = $v->getPrice();
//                    $variants[$k]['image_url'] = $image_url;
//
//                    if ($k == 0) {
//                        $p['image_url'] = $image_url;
//                        $p['url'] = $url;
//                    }
//                }
//            }
//            $p['variants'] = $variants;
//
//            $batch->patch("op".$i, "ecommerce/stores/$store_id/products/".$product['id'], $p);
//            $i++;
//        }
//
////        $result = $batch->execute();
//
//        return print_r($batch);
    }
}
