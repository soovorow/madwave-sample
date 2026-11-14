<?php

namespace frontend\modules\checkout\ru\models\payment_methods;

use common\models\Order;
use common\models\OrderItem;
use common\models\User;
use frontend\modules\checkout\ru\models\pricing\CardOnly;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\httpclient\Client;

class MethodPsBank extends PaymentMethod
{
    public $label = 'Оплата картой';
    public $widgetClass = 'PsBank';
    public $type = 'terminal';
    public $description = 'Оплата заказа онлайн через сервис "Промсвязьбанк"';
    public $code = 'ТГ507040';
    public $action = 'https://3ds.payment.ru/cgi-bin/cgi_link';
    public $weight = 0;

    public function setting($orderId)
    {
        $order = Order::findOne($orderId);

        return [
            'amount' => number_format($order->total, 2, '.', ''),
            'currency' => 'RUB',
            'merch_name' => 'MADWAVE.RU',
            'merchant' => 'REDACTED_BEFORE_PUBLICATION',
            'terminal' => '35079401',
            'email' => 'managerinternet@madwave.ru',
            'trtype' => '1',
            'order_id' => str_pad($order->getId(), 6, '0', STR_PAD_LEFT),
            'timestamp' => gmdate('YmdHis'),
            'nonce' => substr(md5(rand(1,9999).microtime()), 0, 16),
            'backref' => Url::base(true) . '/'.\Yii::$app->request->pathInfo.'?id=' . $orderId,
            'key' => 'REDACTED_BEFORE_PUBLICATION'
        ];
    }

    public function getDescription()
    {
        return $this->description . '<br>-'.CardOnly::DISCOUNT.'% при оплате онлайн';
    }


    public function response($getData, $postData)
    {
        // AMOUNT, CURRENCY, ORDER, DESC, TERMINAL,
        // TRTYPE, MERCH_NAME, MERCHANT, EMAIL, TIMESTAMP,
        // NONCE, BACKREF,  RESULT, RC, RCTEXT, AUTHCODE, RRN, INT_REF, P_SIGN, NAME, CARD

        if (empty($postData['ORDER'])) {
            return false;
        }

        $order = Order::findOne(ltrim($postData['ORDER'], 0));

        if ($order) {
            $setting = $this->setting($order->getId());

            $hmacData = [
                'AMOUNT' => $setting['amount'],
                'CURRENCY' => $postData['CURRENCY'],
                'ORDER' => $setting['order_id'],
                'MERCH_NAME' => $postData['MERCH_NAME'],
                'MERCHANT' => $setting['merchant'],
                'TERMINAL' => $setting['terminal'],
                'EMAIL' => $postData['EMAIL'],
                'TRTYPE' => $postData['TRTYPE'],
                'TIMESTAMP' => $postData['TIMESTAMP'],
                'NONCE' => $postData['NONCE'],
                'BACKREF' => $postData['BACKREF'],
                'RESULT' => $postData['RESULT'],
                'RC' => $postData['RC'],
                'RCTEXT' => $postData['RCTEXT'],
                'AUTHCODE' => $postData['AUTHCODE'],
                'RRN' => $postData['RRN'],
                'INT_REF' => $postData['INT_REF']
            ];

            if ($postData['RESULT'] == 0) {
                $hmac = $this->generationHmac($hmacData, $setting['key']);
                if ($postData['P_SIGN'] == $hmac) {
                    $order->status = $order::STATUS_PAID;
                    $order->save(false);
                    $this->sendPayTureCheck($order);
                    return true;
                }
            }


            if ($postData['RESULT'] == 2 || $postData['RESULT'] == 3) {
                \Yii::$app->session->setFlash(
                    'danger',
                    'Ваш заказ не оплачен. Пожалуйста, 
                    повторите попытку или свяжитесь с нами по телефону: 8 800 100 57 19'
                );
            }
        }

        return false;
    }

    public function post($orderId)
    {
        $order = Order::findOne($orderId);
        $user = User::findByEmail($order->customer_email)->getId();
        $paymentSetting = $this->setting($orderId);

        $hmacData = [
            'AMOUNT' => $paymentSetting['amount'],
            'CURRENCY' => $paymentSetting['currency'],
            'ORDER' => $paymentSetting['order_id'],
            'MERCH_NAME' => $paymentSetting['merch_name'],
            'MERCHANT' => $paymentSetting['merchant'],
            'TERMINAL' => $paymentSetting['terminal'],
            'EMAIL' =>  $paymentSetting['email'],
            'TRTYPE' => '1',
            'TIMESTAMP' => $paymentSetting['timestamp'],
            'NONCE' =>  $paymentSetting['nonce'],
            'BACKREF' =>  $paymentSetting['backref'],
        ];

        $params = [
            'user' => $user,
            'hmac' => $this->generationHmac($hmacData, $paymentSetting['key']),
            'action' => $this->action,
        ];

        return  array_merge($params, $paymentSetting);
    }

    private function generationHmac($parameters, $key)
    {
        $hmac = '';
        foreach ($parameters as $parm) {
            if (strlen($parm) != 0) {
                $hmac .= strlen($parm) . $parm;
            } else {
                $hmac .= '-';
            }
        }

        return strtoupper(hash_hmac('sha1', $hmac, pack('H*', $key)));
    }

    private function sendPayTureCheck(Order $order)
    {
        $order_items = $order->getOrderItems();

        $total = $order->total;

        $positions = [];

        $sum = 0;

        foreach ($order_items as $order_item) {
            /**
             * @var $order_item OrderItem
             */

            $data = [
                'Quantity' => (integer) number_format($order_item->quantity, 3,'.',''),
                'Price' => (double) $order_item->sum / $order_item->quantity ,
                'Tax' => 6,
                'Text' => $order_item->name .' - '. $order_item->vendore_code
            ];

            $positions[] = $data;

            $sum += $data['Price'];

        }

        if ($total > $sum) {
            $positions[] = [
                'Quantity' => 1,
                'Price' => $total - $sum,
                'Tax' => 6,
                'Text' => 'Доставка'
            ];
        }

        if ($total < $sum) {
            $total = $total + abs($total - $sum);
        }

        $data = [
            'Key' => 'MadwaveCheque',
            'Password' => 'REDACTED_BEFORE_PUBLICATION',
            'Cheque' => [
                'Id' => (string)$order->getId(),
                'INN' => '7806150861',
                'Group' => 'madwave.ru',
                'Content' => [
                    'Type' => 1,
                    'Positions' => $positions,
                    'CheckClose' => [
                        'Payments' => [
                            [
                                'Type' => 2,
                                'Amount' => $total
                            ]
                        ],
                        'TaxationSystem' => 2
                    ],
                    'CustomerContact' => $order->customer_email,
                    'Message' => 'Оплата заказа #'.$order->getId(),
                ]
            ]
        ];

        $client = new Client();
        $result = $client->createRequest()
            ->setMethod('post')
            ->setUrl('https://secure.payture.com/apicheque/Create/')
            ->addHeaders(['content-type' => 'application/json'])
            ->setContent(Json::encode($data))
            ->send();
        ;

        return $result->data;
    }
}