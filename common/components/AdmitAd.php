<?php
namespace common\components;

use common\models\ProductVariant;
use Nette\Utils\Json;
use Yii;
use yii\web\View;

/**
 * Admit Ad component
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */

class AdmitAd
{
    public static function pushCartItems()
    {
        /**
         * @var $cart \yz\shoppingcart\ShoppingCart
         * @var $product ProductVariant
         */

        $cart = Yii::$app->cart;

        if ($cart->isEmpty) {
            return false;
        }

        $products = [];

        foreach ($cart->getPositions() as $product) {
            $products[] = [
                'id' => $product->variant_id,
                'number' => $product->quantity
            ];
        }

        Yii::$app->view->registerJs(
            '
            window.ad_products = '.Json::encode($products).';
            window._retag = window._retag || [];
            window._retag.push({code: "9ce88872f7", level: 3});
            (function () {
                var id = "admitad-retag";
                if (document.getElementById(id)) {return;}
                var s = document.createElement("script");
                s.async = true; s.id = id;
                var r = (new Date).getDate();
                s.src = (document.location.protocol == "https:" ? "https:" : "http:") + "//cdn.lenmit.com/static/js/retag.min.js?r="+r;
                var a = document.getElementsByTagName("script")[0];
                a.parentNode.insertBefore(s, a);
            })()'
        );
    }

    public static function setIndexPage()
    {
        Yii::$app->view->registerJs(
            '
            window._retag = window._retag || [];
            window._retag.push({code: "9ce88872e8", level: 0});
            (function () {
                var id = "admitad-retag";
                if (document.getElementById(id)) {return;}
                var s = document.createElement("script");
                s.async = true; s.id = id;
                var r = (new Date).getDate();
                s.src = (document.location.protocol == "https:" ? "https:" : "http:") + "//cdn.lenmit.com/static/js/retag.min.js?r="+r;
                var a = document.getElementsByTagName("script")[0]
                a.parentNode.insertBefore(s, a);
            })();',
            View::POS_END
        );
    }

    public static function setCategoryPage($categoryId)
    {
        Yii::$app->view->registerJs(
            '
            window.ad_category = "'. $categoryId .'";   // required
            window._retag = window._retag || [];
            window._retag.push({code: "9ce88872e9", level: 1});
            (function () {
                var id = "admitad-retag";
                if (document.getElementById(id)) {return;}
                var s=document.createElement("script");
                s.async = true; s.id = id;
                var r = (new Date).getDate();
                s.src = (document.location.protocol == "https:" ? "https:" : "http:") + "//cdn.lenmit.com/static/js/retag.min.js?r="+r;
                var a = document.getElementsByTagName("script")[0]
                a.parentNode.insertBefore(s, a);
            })();',
            View::POS_END
        );
    }

    public static function setProductPage($variantId, $productName, $categoryId)
    {
        Yii::$app->view->registerJs(
            '
            window.ad_product = {
            "id": "'.$variantId.'",   // required
            "vendor": "",
            "price": "",
            "url": "",
            "picture": "",
            "name": "'.$productName.'",
            "category": "'.$categoryId.'"
        };

        window._retag = window._retag || [];
        window._retag.push({code: "+", level: 2});
        (function () {
            var id = "admitad-retag";
            if (document.getElementById(id)) {return;}
            var s = document.createElement("script");
            s.async = true; s.id = id;
            var r = (new Date).getDate();
            s.src = (document.location.protocol == "https:" ? "https:" : "http:") + "//cdn.lenmit.com/static/js/retag.min.js?r="+r;
            var a = document.getElementsByTagName("script")[0]
            a.parentNode.insertBefore(s, a);
        })();',
            View::POS_END
        );
    }

    public static function setCartPage($products)
    {

        $printProducts = '';

        if (!$products)  {
            return false;
        }

        foreach ($products as $product) {
            $printProducts .= '
                {
                    "id": "' . $product->variant_id . '",
                    "number": "' . $product->getQuantity() . '"
                },
            ';

        }

        if ($printProducts != '') {
            Yii::$app->view->registerJs(
                '
                window.ad_products = [' . $printProducts . '];
                window._retag = window._retag || [];
                window._retag.push({code: "9ce88872f7", level: 3});
                (function () {
                    var id = "admitad-retag";
                    if (document.getElementById(id)) {return;}
                    var s = document.createElement("script");
                    s.async = true; s.id = id;
                    var r = (new Date).getDate();
                    s.src = (document.location.protocol == "https:" ? "https:" : "http:") + "//cdn.lenmit.com/static/js/retag.min.js?r="+r;
                    var a = document.getElementsByTagName("script")[0]
                    a.parentNode.insertBefore(s, a);
                })();',
                View::POS_END
            );
        }
    }

    public static function setCompletePage($orderItems, $orderId, $ordertotal)
    {
        if (!empty($_COOKIE['admitad_uid'])) {
            $orderItemsPrint = '';

            foreach ($orderItems as $key => $item) {
                $orderItemsPrint .= '{
                    "id": "'.$item->id.'",
                    "number": "'.$item->quantity.'"
                },';
            }

            if ($orderItemsPrint != '') {
                Yii::$app->view->registerJs(
                    '
                    window.ad_order = "' . $orderId . '";    // required
                    window.ad_amount = "' . $ordertotal . '";
                    window.ad_products = ['. $orderItemsPrint .'];
                    window._retag = window._retag || [];
                    window._retag.push({code: "9ce88872f4", level: 4});
                    (function () {
                    var id = "admitad-retag";
                    if (document.getElementById(id)) {return;}
                    var s = document.createElement("script");
                    s.async = true; s.id = id;
                    var r = (new Date).getDate();
                    s.src = (document.location.protocol == "https:" ? "https:" : "http:") + "//cdn.lenmit.com/static/js/retag.min.js?r="+r;
                    var a = document.getElementsByTagName("script")[0]
                    a.parentNode.insertBefore(s, a);
                    })();',
                    View::POS_END
                );
            }
        }
    }
}
