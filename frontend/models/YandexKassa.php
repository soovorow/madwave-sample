<?php
/**
 * Created by PhpStorm.
 * User: WDDA
 * Date: 24.01.2017
 * Time: 20:53
 */

namespace frontend\models;
use common\models\Order;
use yii\base\Model;

class YandexKassa extends Model
{
    public $action;
    public $orderSumAmount;
    public $orderSumCurrencyPaycash;
    public $orderSumBankPaycash;
    public $shopId;
    public $invoiceId;
    public $customerNumber;
    public $md5;
    public $orderNumber;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'action',
                'orderSumAmount',
                'orderSumCurrencyPaycash',
                'orderSumBankPaycash',
                'shopId',
                'invoiceId',
                'customerNumber',
                'orderNumber',
                'md5'], 'required'
            ],

            ['md5', 'validateMD5']
        ];
    }

    /**
     * Building XML response.
     * @param  string $action "checkOrder" or "paymentAviso" string
     * @param  string $invoiceId     transaction number
     * @param  string $resultCode   result code
     * @param  string $message       error message. May be null.
     * @return string                prepared XML response
     */
    public function buildResponse($action, $invoiceId, $resultCode, $message = null)
    {
        $xml = new \DOMDocument("1.0", "utf-8");
        $child = $xml->createElement($action."Response");
        $child->setAttribute('performedDatetime', date("Y-m-d\TH:i:s.000P"));
        $child->setAttribute('code', $resultCode);
        if ($message) {
            $child->setAttribute('message', $message);
        }
        $child->setAttribute('invoiceId', $invoiceId);
        $child->setAttribute('shopId', $this->shopId);
        $xml->appendChild($child);
        return $xml->saveXML();
    }

    /**
     * Checking the MD5 sign.
     * @return bool true if MD5 hash is correct
     */
    public function validateMD5($attribute)
    {
        $order = Order::findOne($this->orderNumber);

        $str = implode(';', [
            $this->action,
            number_format($order->total, 2, '.', ''),
            $this->orderSumCurrencyPaycash,
            $this->orderSumBankPaycash,
            $this->shopId,
            $this->invoiceId,
            $this->customerNumber,
            '3l45jl5kj3l4ryth'
        ]);

        \Yii::info("String to md5: " . $str, 'YandexKassa');
        $md5 = strtoupper(md5($str));

        if ($md5 != strtoupper($this->$attribute)) {
            \Yii::error("Wait for md5: ".$md5.", recieved md5: " .$this->$attribute, 'YandexKassa');
            $this->addError($attribute, 'Security check failed: wrong MD5 hash');

            return false;
        }
    }
}