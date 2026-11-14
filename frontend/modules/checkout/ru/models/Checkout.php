<?php
namespace frontend\modules\checkout\ru\models;

use common\components\multilocal\Local;
use common\models\Order;
use common\models\Pricing;
use common\models\ProductVariant;
use common\models\SelfPickUpPoints;
use frontend\modules\checkout\ru\models\delivery_methods\DeliveryMethod;
use frontend\modules\checkout\ru\models\delivery_methods\MethodPickup;
use frontend\modules\checkout\ru\models\pricing\ClientCard;
use frontend\modules\checkout\ru\models\pricing\Discounts;
use frontend\modules\checkout\ru\models\pricing\PromoCode;
use Yii;
use yii\base\Model;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;

/**
 * Cart checkout form
 *
 * @property Customer $_customer
 * @property Delivery $_delivery
 * @property Payment $_payment
 *
 * @property string $customer_notes
 * @property string $_order_id
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

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->_customer = new Customer();
        $this->_delivery = new Delivery();
        $this->_payment = new Payment();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['_customer', 'validateCustomer'],
            ['_delivery', 'validateDelivery'],
            ['_payment', 'validatePayment'],
            ['customer_notes', 'default']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'customer_notes' => 'Комментарий к заказу'
        ];
    }


    public function loadAll($post)
    {
        if (
               $this->load($post)
            && $this->_customer->load($post)
            && $this->_delivery->load($post)
            && $this->_delivery->getMethod()->load($post)
            && $this->_payment->load($post)
        ) {
            return true;
        }
        return false;
    }


    public function validateCustomer() {
        if (!$this->_customer->validate()) {
            $this->addError('_customer');
        }
    }

    public function validateDelivery() {
        if (!$this->_delivery->validate()) {
            $this->addError('_delivery');
        }
    }

    public function validatePayment() {
        if (!$this->_payment->validate()) {
            $this->addError('_payment');
        }
    }



    public static function getTotalSum($currency = false, $withDelivery = true)
    {
        $total = \Yii::$app->cart->getCost();

        $total -= Discounts::getTotalDiscountSum();

        if ($withDelivery) {
            $total += (double) DeliveryMethod::getDeliveryCost();
        }

        return ($total < 0 ? 0 : $total) . ($currency ?  \Yii::$app->params['currency'] : null);
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
        if (!$this->validate()) {
            return false;
        }

        $order = new Order();
        $customer = $this->_customer;
        $delivery = $this->_delivery;
        $payment = $this->_payment;

        if ($delivery->getMethod() instanceof MethodPickup) {
            $point = SelfPickUpPoints::findOne($delivery->getMethod()->picking_up_point);
            $order->pick_up_point = $point->code;
        }

        $order->website = Local::detectLocal();
        $order->customer_notes = $this->customer_notes;
        $order->customer_phone = $customer->phone;
        $order->customer_email = $customer->email;
        $order->delivery_method = $delivery->getCode();
        $order->city = $delivery->getCity();
        $order->address = $delivery->getAddress();
        $order->post_index = $delivery->getPostalCode();
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

        if ($order->create()) {
            $this->_order_id = $order->getId();
            return $order;
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

        foreach ( $cart as $variant ) {
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
