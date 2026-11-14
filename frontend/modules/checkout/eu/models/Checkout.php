<?php

namespace frontend\modules\checkout\eu\models;

use Codeception\Coverage\Subscriber\Local;
use common\components\postifi\Posti;
use common\components\postifi\services\ParcelExpress;
use common\models\Order;
use common\models\Pricing;
use common\models\ProductVariant;
use frontend\modules\checkout\eu\models\delivery_methods\DeliveryMethod;
use frontend\modules\checkout\eu\models\pricing\ClientCard;
use frontend\modules\checkout\eu\models\pricing\Discounts;
use frontend\modules\checkout\eu\models\pricing\PromoCode;
use yii\base\Model;
use yii\helpers\Json;

/**
 * @property Customer $_customer
 * @property Delivery $_delivery
 * @property Payment $_payment
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class Checkout extends Model
{
    const SESSION_KEY = 'checkout';

    public $_customer;
    public $_delivery;
    public $_payment;

    public $customer_notes;

    public $_order_id;

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->_customer = new Customer();
        $this->_delivery = new Delivery();
        $this->_payment = new Payment();

    }

    public function rules()
    {
        return [
            ['_customer', 'validateCustomer'],
            ['_delivery', 'validateDelivery'],
            ['_payment', 'validatePayment'],
            ['customer_notes', 'default']
        ];
    }

    public function attributeLabels()
    {
        return [
            'customer_notes' => 'Comment'
        ];
    }

    public function validateCustomer() {
        if (!$this->_customer->validate()) {
            $this->addError('_customer');
        }
    }

    public function validateDelivery() {
        if (!$this->_delivery->validate()) {
            $this->addError('_customer');
        }
    }

    public function validatePayment() {
        if (!$this->_payment->validate()) {
            $this->addError('_customer');
        }
    }

    public static function getTotalSum($currency = false, $withDelivery = true)
    {
        $total = \Yii::$app->cart->getCost();

//        $total += $total / 100 * 24;
        //$total -= Discounts::getTotalDiscountSum();

        if ($withDelivery) {
            $deliveryCost = DeliveryMethod::getDeliveryCost();
            $total += $deliveryCost;
            $total += $deliveryCost / 100 * 24;
        }

        return ($total < 0 ? 0 : number_format($total, '2')) . ($currency ?  \Yii::$app->params['currency'] : null);
    }

    public function saveInSession() {
        $second_step = [];
        foreach ($this->attributes() as $a) {
            $second_step[$a] = $this->$a;
        }
        \Yii::$app->session->set(self::SESSION_KEY, $second_step);
    }

    public function composeOrder()
    {

        if (!$this->validate()) return false;


        $order = new Order();

        $customer = $this->_customer;
        $delivery = $this->_delivery;
        $payment = $this->_payment;


        $deliveryMethod = $delivery->method[$delivery->delivery_method];
        $attributes = $_POST[array_reverse(explode('\\', $deliveryMethod::className()))[0]];

        $order->website = \common\components\multilocal\Local::detectLocal();

        $order->country = $this->_customer->country;

        $order->customer_notes = $this->customer_notes;
        $order->customer_phone = $customer->phone;
        $order->customer_email = $customer->email;
        $order->delivery_method = $delivery->getCode();
        $order->city = $customer->city;
        $order->country = $customer->country;
        $order->address = $attributes['address'];
        $order->post_index = $attributes['postalCode'];

        $order->customer_firstname = $customer->firstName;
        $order->customer_lastname = $customer->lastName;
        $order->customer_secondname = $customer->secondName;


        $order->payment_method = $payment->payment_method;

        $order->delivery_price = $delivery->getCost();

        $order->primary_total = Pricing::getPreparedSum();
        $order->total_discount = Discounts::getTotalDiscountSum();
        $order->total = self::getTotalSum(false, true);

        if ($card = ClientCard::fetchFromSession()) {
            $order->client_card = $card;
            $order->client_discount = Discounts::getTotalCardDiscountSum();
        }

        if ($coupon = PromoCode::fetchFromSession()) {
            $order->coupon = $coupon->code;
            $order->coupon_discount = Discounts::getTotalCodeDiscountSum();
        }

        if ($order->create($this)) {
            $this->_order_id = $order->getId();
            return true;
        }
        return false;
    }


    /**
     * @param $step
     */
    public static function measureCheckoutStep($step)
    {
        $params = Json::encode([
            'step' => $step,
        ]);

        $cart =  \Yii::$app->cart->getPositions();
        $addProductArray = [];

        foreach ($cart as $variant ) {
            /** @var $variant ProductVariant */
            $product = $variant->formatGaArray();
            $product['price'] = $variant->price;
            $product['quantity'] = $variant->getQuantity();
            $product = Json::encode($product);
            $addProductArray[] = "ga('ec:addProduct', $product)";
        }
        $js = implode(';', $addProductArray);

        $view = \Yii::$app->view;
        $view->registerJs($js, $view::POS_END);
        $js = "ga('ec:setAction','checkout', $params); ga('send', 'event', 'checkout', 'step')";
        $view = \Yii::$app->view;
        $view->registerJs($js, $view::POS_END);
    }

}
