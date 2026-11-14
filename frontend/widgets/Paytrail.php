<?php
namespace frontend\widgets;
use yii\base\Widget;
use common\models\Order;
use common\models\User;
use yii\helpers\Url;

class Paytrail extends Widget{

    public $order_id;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $order = Order::findOne($this->order_id);
        $user = User::findByEmail($order->customer_email)->getId();

        //demo data
//        $merchantSecret = 'REDACTED_BEFORE_PUBLICATION';
//        $merchant_id = '13466';

        if(!empty($_GET['payment'])){
            if($_GET['payment'] == 'success')
                \Yii::$app->session->setFlash('success', 'Thank you! Your order is paid!');

            if($_GET['payment'] == 'fail')
                \Yii::$app->session->setFlash(
                    'danger', 'Your order has not been paid. Please, try again or contact us by phone: +358 44 5295591');
        }


        $merchantSecret = 'REDACTED_BEFORE_PUBLICATION';
        $merchant_id = '39421';

        $amount = number_format($order->total, 2);
        $order_number = $order->getId();

        $success = Url::base(true) . '/checkout/cart/checkout-complete?id=' . $this->order_id . '&payment=success';
        $fail = Url::base(true) . '/checkout/cart/checkout-complete?id=' . $this->order_id . '&payment=fail';
        $notify = Url::base(true) . '/site/paytrail';

        $authcode = "$merchantSecret|$merchant_id|$amount|$order_number|||EUR|$success|$fail||$notify|S1|en_US||||";

        $params = [
            'merchant_id' => $merchant_id,
            'order' => $order,
            'order_id' => $order->getId(),
            'user' => $user,
            'total' => $amount,
            'success' => $success,
            'fail' => $fail,
            'notify' => $notify,
            'authcode' => strtoupper(md5($authcode))
        ];

        return $this->render('paytrail', $params);
    }

    public static function confirmPayment(){

        if(!empty($_GET["ORDER_NUMBER"]) && !empty($_GET["TIMESTAMP"]) && !empty($_GET["PAID"]) && !empty($_GET["METHOD"])){

            //demo data
//            $merchantSecret = 'REDACTED_BEFORE_PUBLICATION';
            $base = "{$_GET["ORDER_NUMBER"]}|{$_GET["TIMESTAMP"]}|{$_GET["PAID"]}|{$_GET["METHOD"]}|{$merchantSecret}";

            if($_GET["RETURN_AUTHCODE"] == strtoupper(md5($base))){
                $order = Order::findOne($_GET["ORDER_NUMBER"]);
                $order->status = $order::STATUS_PAID;
                $order->save(false);

                return  true;
            }
        }

        return false;
    }

}