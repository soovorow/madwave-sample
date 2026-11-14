<?php namespace frontend\modules\checkout\ru\models;

use yii\base\Model;

class PaymentTerminal extends Model
{
    public function widget($order)
    {
        $payment = new Payment();
        $method = $payment->getMethodByCode($order->payment_method);

        if ($method) {
            $className = 'frontend\widgets\\' . $method->widgetClass;
            return $className::widget(['order_id' => $order->getId()]);
        }

        return false;
    }

    public function response($getData, $postData)
    {
        $paymentCode = urldecode(\Yii::$app->request->get('pcode'));
        $payment = new Payment();

        if (!$payment->getMethodByCode($paymentCode)) {
            return false;
        }

        return $payment->getMethodByCode($paymentCode)->response($getData, $postData);
    }
}
