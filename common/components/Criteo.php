<?php namespace common\components;

use common\models\Pricing;
use common\models\ProductVariant;
use common\models\User;
use Yii;
use yii\web\View;

class Criteo
{
    public static function setIndexPage()
    {
        self::setScriptFile();

        Yii::$app->view->registerJs(
            '
            window.criteo_q = window.criteo_q || [];
            var deviceType = /iPad/.test(navigator.userAgent) ? "t" : /Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Silk/.test(navigator.userAgent) ? "m" : "d";
            window.criteo_q.push(
             { event: "setAccount", account: 47523},
             { event: "setEmail", email: "' . self::getEmail() . '"}, 
             { event: "setSiteType", type: deviceType},
             { event: "viewHome"});',
            View::POS_END
        );
    }

    public static function setCategoryPage($productsDataProvider)
    {
        $item = [];
        foreach ($productsDataProvider->models as $model) {
            $item[] = $model->variant_id;
        }

        if (!empty($item)) {
            $item = '["' . join($item, '", "') . '"]';

            self::setScriptFile();

            Yii::$app->view->registerJs(
                '
                window.criteo_q = window.criteo_q || [];
                var deviceType = /iPad/.test(navigator.userAgent) ? "t" : /Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Silk/.test(navigator.userAgent) ? "m" : "d";
                window.criteo_q.push(
                { event: "setAccount", account: 47523},
                { event: "setEmail", email: "' . self::getEmail() . '"},
                { event: "setSiteType", type: deviceType},
                { event: "viewList", item: ' . $item . '});',
                View::POS_END
            );
        }
    }

    public static function setProductPage($variantId)
    {
        self::setScriptFile();

        Yii::$app->view->registerJs(
            '
            window.criteo_q = window.criteo_q || [];
            var deviceType = /iPad/.test(navigator.userAgent) ? "t" : /Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Silk/.test(navigator.userAgent) ? "m" : "d";
            window.criteo_q.push(
             { event: "setAccount", account: 47523},
             { event: "setEmail", email: "' . self::getEmail() . '" },
             { event: "setSiteType", type: deviceType},
             { event: "viewItem", item: "'.$variantId.'" });
             ',
            View::POS_END
        );
    }

    public static function setCartPage($products)
    {
        $printProducts = '';
        if (!$products) {
            return false;
        }
        foreach ($products as $product) {
            $printProducts .= '
                {
                    id: "' . $product->variant_id . '",
                    price: "' . Pricing::getItemSumWithDiscount($product) . '",
                    quantity: "' . $product->getQuantity() . '"
                },
            ';
        }

        if ($printProducts != '') {
            self::setScriptFile();

            Yii::$app->view->registerJs(
                '
                window.criteo_q = window.criteo_q || [];
                var deviceType = /iPad/.test(navigator.userAgent) ? "t" : /Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Silk/.test(navigator.userAgent) ? "m" : "d";
                window.criteo_q.push(
                { event: "setAccount", account: 47523},
                { event: "setEmail", email: "' . self::getEmail() . '" },
                { event: "setSiteType", type: deviceType},
                { event: "viewBasket", item: ['.$printProducts.']}
                );'
            );
        }
    }

    public static function setCompletePage($orderItems, $orderId, $email)
    {
        $orderItemsPrint = '';
        foreach ($orderItems as $item) {
            $variant = ProductVariant::find()->where(['like', 'vendore_code', $item->vendore_code])->one();

            $orderItemsPrint .= '
                {
                    id: "' . $variant->variant_id . '",
                    price: "' . $item->clearPriceWDiscount . '",
                    quantity: "' . $item->quantity . '"
                }, 
            ';
        }

        if ($orderItemsPrint != '') {
            self::setScriptFile();

            Yii::$app->view->registerJs(
                '
               window.criteo_q = window.criteo_q || [];
                var deviceType = /iPad/.test(navigator.userAgent) ? "t" : /Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Silk/.test(navigator.userAgent) ? "m" : "d";
                window.criteo_q.push(
                 { event: "setAccount", account: 47523}, // Это значение остается неизменным
                { event: "setEmail", email: "' . $email . '" },
                 { event: "setSiteType", type: deviceType},
                 { event: "trackTransaction", id: '. $orderId .', item: [' . $orderItemsPrint . ']}
                );'
            );
        }
    }

    public static function getEmail()
    {
        if (!Yii::$app->user->isGuest) {
            $user = User::findOne(Yii::$app->user->identity->getId());
            return $user->email;
        }

        return '';
    }

    public static function setScriptFile()
    {
        Yii::$app->view->registerJsFile('//static.criteo.net/js/ld/ld.js', ['async' => 'true'], View::POS_END);
    }
}
