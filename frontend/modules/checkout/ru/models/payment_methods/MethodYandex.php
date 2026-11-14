<?php

namespace frontend\modules\checkout\ru\models\payment_methods;

use common\models\Order;
use frontend\modules\checkout\ru\models\pricing\CardOnly;
use frontend\widgets\YandexKassa;

class MethodYandex extends PaymentMethod
{
    public $label = 'Оплата через сервис "Яндекс.Касса"';
    public $widgetClass = 'YandexKassa';
    public $type = 'terminal';
    public $description = 'Оплата заказа онлайн через сервис "Яндекс.Касса"';
    public $code = 'ТГ505444';
    public $weight = 1;

    public function getDescription()
    {
        return $this->description . '<br>-'.CardOnly::DISCOUNT.'% при оплате онлайн';
    }

    //Новый URL
    //https://www.madwave.ru/site/payment-response?pcode=ТГ505444&ypayment=checkOrder
    //https://www.madwave.ru/site/payment-response?pcode=ТГ505444&ypayment=paymentAviso

    //Старый
    //https://www.madwave.ru/?ypayment=checkOrder
    //https://www.madwave.ru/?ypayment=paymentAviso

    public function response($getData, $postData)
    {
        if ($getData['ypayment'] == 'checkOrder' || $getData['ypayment'] == 'paymentAviso') {
            $type = $getData['ypayment'];
            $model = new YandexKassa();
            $model->load($postData, '');

            if ($model->validate() == false) {
                return $model->buildResponse($type, $model->invoiceId, 1, 'Ожидаемый md5 неправильный');
            }

            $order = Order::findOne($model->orderNumber);
            $order->status = $order::STATUS_PAID;
            $order->save(false);

            return $model->buildResponse($type, $model->invoiceId, 0);
        }

        return false;
    }
}