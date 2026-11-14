<?php

namespace common\models;

use common\models\products\Currency;
use yii\base\Model;
use yii\helpers\Html;

/** Order Report
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class OrderReport extends Model
{

    public static function getTotalCharge($orderItems)
    {
        $totalCharge = 0;
        foreach ($orderItems as $k => $oi) {
            /**
             * @var $v ProductVariant
             */
            $v =  ProductVariant::findByVendorCode($k)->one();
            $currency = Currency::findOne(['code' => 'USD']);
            $charge = $v->priceList->price_usd_int_wh * $currency->quote * $oi['quantity'];
            $totalCharge += $charge;
        }
        return $totalCharge;
    }

    public static function getPrices($orderItems)
    {
        $prices = [];
        foreach ($orderItems as $key => $row) {
            $prices[$key] = $row['price'];
        }
        return $prices;
    }

    public static function getOrdersCount()
    {
        return self::orders()->count();
    }

    public static function getOrderItems()
    {
        $orders = self::orders()->all();
        $orderItems = [];
        foreach ($orders as $o) {
            /** @var $o \common\models\Order */
            $oItems = $o->getOrderItems();
            foreach ($oItems as $oi) {
                $vCode = str_replace(' ','',$oi->vendore_code);
                if (!isset($orderItems[$vCode])) {
                    $orderItems[$vCode] = ['quantity' => 0, 'price' => 0];
                }
                $orderItems[$vCode]['quantity'] += $oi->quantity;
                $orderItems[$vCode]['price'] += $oi->sum;
            }
        }

        return $orderItems;
    }

    public static function getOrdersTotal()
    {
        return self::orders()->sum('total');
    }

    public static function getOrdersDelivery()
    {
        return self::orders()->sum('delivery_price');
    }

    public static function getOrders()
    {
        return self::orders()->all();
    }

    public static function getFromTo()
    {
        $yesterday = strtotime('-1 day');

        if (isset($_GET['month'])) {
            $from = mktime(4, 0, 0, $_GET['month'], 1, date('Y'));
            $to = strtotime('+1 month', $from);
        } else {
            $from = mktime(4, 0, 0, date('n', $yesterday), date('j', $yesterday), date('Y', $yesterday));
            $to = strtotime('+1 day', $from);
        }


        return [
            'from' => $from,
            'to' => $to,
        ];
    }

    public static function orders()
    {
        $fromTo = self::getFromTo();
        $from = $fromTo['from'];
        $to = $fromTo['to'];

        return Order::find()->where(1)
            ->andWhere(['>', 'created_at', $from])
            ->andWhere(['<=', 'created_at', $to])
            ->andWhere(['test_status' => null])
            ->andWhere(['NOT LIKE', 'customer_firstname', 'тест'])
            ->andWhere(['NOT LIKE', 'customer_firstname', 'test'])
            ;
    }
}
