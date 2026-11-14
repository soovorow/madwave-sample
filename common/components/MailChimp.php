<?php

namespace common\components;

use common\components\multilocal\Local;
use common\models\Order;
use common\models\Product;
use common\models\ProductVariant;
use common\models\User;
use DrewM\MailChimp\Batch;
use Nette\Utils\Json;

/**
 * MailChimp component
 *
 * The component for interaction app with MailChimp
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */

class MailChimp extends \DrewM\MailChimp\MailChimp
{

    const SESSION_EMAIL_KEY = 'user_email_for_mailchimp';

    /**
     * @var string $api_key Mail Chimp API key
     */
    private $api_key = 'REDACTED_BEFORE_PUBLICATION';

    /**
     * @var string $store_id Mail Chimp API store id
     */
    public $store_id = 'MWPROD';

    /**
     * @var string $list_id Mail Chimp API list id
     */
    private $list_id = 'a894803046';

    public function __construct()
    {
        $this->store_id = Local::choose([
            Local::RU => 'MWPROD',
            Local::EU => 'MADWAVEEU'
        ]);

        $this->list_id = Local::choose([
            Local::RU => 'a894803046',
            Local::EU => 'ff7000884b'
        ]);

        parent::__construct($this->api_key);
    }

    public static function setUserEmail($email)
    {
        $session = \Yii::$app->session;
        $session->set(self::SESSION_EMAIL_KEY, $email);
    }

    public static function getUserEmail()
    {
        $session = \Yii::$app->session;
        if (!\Yii::$app->user->isGuest) {
            return \Yii::$app->user->identity->email;
        }
        if ($session->has(self::SESSION_EMAIL_KEY)) {
            return $session->get(self::SESSION_EMAIL_KEY);
        }
    }

    /**
     * Create new Mail Chimp list member
     *
     * @param string $email user email
     * @param bool $opt_in subscribe user or not subscribe
     * @return string
     */
    public function addListMember($email, $opt_in = true)
    {
        $result = $this->post("ecommerce/stores/$this->store_id/customers", [
            'id' => $email,
            'email_address' => $email,
            'opt_in_status' => $opt_in,
        ]);
        return $result;
    }

    /**
     * Get Mail Chimp list member
     *
     * @param string $email user email
     * @return string
     */
    public function getListMember($email)
    {
        $subscriber_hash = $this->subscriberHash($email);
        $response = $this->get("lists/$this->list_id/members/$subscriber_hash");
        return $response;
    }



    /**
     * Add interest for Mail Chimp user
     *
     * @param string $email user email
     * @param string $interest interest label
     * @param bool $value interest value
     * @return array
     */
    public function addInterest($email, $interest, $value)
    {
        $subscriber_hash = $this->subscriberHash($email);
        $interest_hash = $this->getInterestHash($interest);
        $response = $this->patch("lists/$this->list_id/members/$subscriber_hash", [
            'interests' => [
                $interest_hash => $value
            ]
        ]);
        return $response;
    }

    /**
     * Get interest hash
     *
     * @param string $key interest label
     * @return string
     */
    public function getInterestHash($key)
    {
        if ($key == 'a1') {
            return '9739080bba';
        }

        if ($key == 'NEW YEAR\'S SALE') {
            return 'b3ff409a3f';
        }

        if ($key == 'footer') {
            return 'e0ba8c680e';
        }

        if ($key == 'order') {
            return '18f3aade04';
        }

        if ($key == 'reg') {
            return '5225cbba41';
        }

        if ($key == 'client-card') {
            return '00e4ef87e3';
        }
    }



    /**
     * Create Mail Chimp order
     *
     * @param Order|object $order Order model instance
     * @return array
     */
    public function createOrder(Order $order)
    {

        $response = $this->post("ecommerce/stores/$this->store_id/orders", [
            'id' => (string) $order->getId(),
            'customer' => [
                'id' => $order->getCustomerEmail(),
                'email_address' => $order->getCustomerEmail(),
                'opt_in_status' => true
            ],
            'currency_code' => 'RUB',
            'processed_at_foreign' => date('Y-m-d h:i:s'),
            'order_total' => $order->total,
            'lines' => $this->formatOrderItems($order)
        ]);

        $this->removeCart($order->getCustomerEmail());

        return $response;
    }

    /**
     * Check product, and create if exist
     *
     * @param ProductVariant|object $productVariant
     * @param Product|object $product
     */
    public function checkProduct(ProductVariant $productVariant, Product $product = null) {

        if ($product == null) {
            $product = $productVariant->product;
        }

        $product_id = (string) substr($productVariant->vendore_code, 0, -6);
        $variant_id = $productVariant->vendore_code;
        $mcVariant = [
            'id' => (string) $variant_id,
            'title' => $product->name,
            'url' => $productVariant->getUrl(),
            'sku' => $productVariant->id,
            'price' => $productVariant->price,
            'image_url' => $productVariant->getImage(1, ProductVariant::IMG_MEDIUM, true),
        ];

        $product_response = $this->get("ecommerce/stores/$this->store_id/products/$product_id");

        if (!isset($product_response['id'])) {
//            echo 'PRODUCT DOES NOT '.$product->name.' EXIST'."\n";

            $type = $product->globalCategory ? $product->globalCategory->getName() : null;
            $r = $this->createProduct([
                'id' => (string) $product_id,
                'title' => $product->name,
                'url' => $product->getUrl(),
                'description' => $product->desc_ru,
                'type' => $type,
                'image_url' => $productVariant->getImage(1, ProductVariant::IMG_MEDIUM, true),
                'variants' => [
                    $mcVariant
                ]
            ]);

//            if (isset($r['id'])) {
//                echo 'PRODUCT '.$product->name.' CREATED'."\n";
//            }
        } else {
//            echo 'PRODUCT '.$product->name.' EXIST'."\n";

            $r = $variant_response = $this->get("ecommerce/stores/$this->store_id/products/$product_id/variants/$variant_id");

            if (!isset($variant_response['id'])) {
//                echo 'VARIANT DOES NOT EXIST'."\n";

                $r = $this->post("ecommerce/stores/$this->store_id/products/$product_id/variants", $mcVariant);
                if (isset($r['id'])) {
//                    echo 'VARIANT CREATED'."\n";
                }

            } else {
                $r = $this->patch("ecommerce/stores/$this->store_id/products/$product_id/variants/$variant_id", [
                    'price' => $productVariant->price
                ]);
//                echo 'FOURTH POINT'.print_r($r);

            }
        }
    }

    public function updateProductQuantity(ProductVariant $productVariant, $quantity)
    {
        $product_id = $productVariant->product_id;
        $variant_id = $productVariant->variant_id;

        $this->patch("ecommerce/stores/$this->store_id/products/$product_id/variants/$variant_id", [
            'inventory_quantity' => $quantity
        ]);
    }

    public function updateProductPrice(ProductVariant $productVariant, $price)
    {
        $product_id = $productVariant->product_id;
        $variant_id = $productVariant->variant_id;

        $this->patch("ecommerce/stores/$this->store_id/products/$product_id/variants/$variant_id", [
            'price' => $price
        ]);
    }

    /**
     * Format order data
     *
     * @param Order|object $order Order model instance
     *
     * @return array
     */
    private function formatOrderItems(Order $order)
    {
        $lines = [];
        foreach ($order->getOrderItems() as $orderItem) {

            /* @var $productVariant ProductVariant*/
            $productVariant = $orderItem->productVariant;

            /* @var $product Product */
            $product = $productVariant->product;

            $this->checkProduct($productVariant, $product);

            $lines[] = [
                'id' => (string) $orderItem->productVariant->id,
                'product_id' =>  substr($orderItem->vendore_code, 0, -6),
                'type' => $product->globalCategory->getName(),
                'product_variant_id' => $orderItem->vendore_code,
                'quantity' => $orderItem->quantity,
                'price' => $orderItem->sum
            ];
        }

        return $lines;
    }

    /**
     * Create Mail Chimp product
     *
     * @param array $product formatted product validated for MC api
     *
     * @return array
     */
    private function createProduct($product)
    {
        $response = $this->post("ecommerce/stores/$this->store_id/products", $product);
        return $response;
    }



    /**
     * Create Mail Chimp cart
     *
     * @param string $user_email user email
     *
     * @return array;
     */
    public function createCart($user_email = null)
    {
        if ($user_email == null) {
            $user_email = self::getUserEmail();
        }

        if ($user_email == null) {
            return null;
        }

        $this->removeCart($user_email);
        $response = $this->post("ecommerce/stores/$this->store_id/carts", [
            'id' => $user_email,
            'customer' => [
                'id' => $user_email,
            ],
            'checkout_url' => 'madwave.ru/load-mc-cart?uh='.base64_encode($user_email),
            'currency_code' => 'RUB',
            'order_total' => \Yii::$app->cart->getCost(),
            'lines' => $this->formatCartItems()
        ]);
        if (isset($response['status']) && $response['status'] == 400) {
            $response = $this->post("ecommerce/stores/$this->store_id/carts", [
                'id' => $user_email,
                'customer' => [
                    'id' => $user_email,
                    'email_address' => $user_email,
                    'opt_in_status' => true
                ],
                'checkout_url' => 'madwave.ru/load-mc-cart?uh='.base64_encode($user_email),
                'currency_code' => 'RUB',
                'order_total' => \Yii::$app->cart->getCost(),
                'lines' => $this->formatCartItems()
            ]);
        }
        return $response;
    }

    /**
     * Get Mail Chimp cart
     *
     * @param string $email user email
     *
     * @return array;
     */
    public function getCart($email)
    {
        $response = $this->get("ecommerce/stores/$this->store_id/carts/$email");
        return $response;
    }

    public static function fillCart($uh) {
        $cart = (new MailChimp)->getCart(base64_decode($uh));

        if (isset($cart['lines']) && count($cart['lines']) < 1) {
            return false;
        }
        $app_cart = \Yii::$app->cart;
        $app_cart->removeAll();

        foreach ($cart['lines'] as $l) {
            $p = ProductVariant::findOne($l['id']);
            $balance = $p->getStock()->one();
            if ($balance->quantity > 0) {
                $app_cart->put($p);
            }
        }
        return true;
    }

    /**
     * Remove Mail Chimp cart
     *
     * @param string $cart_id Mail Chimp cart id
     *
     * @return array;
     */
    public function removeCart($cart_id)
    {
        $response = $this->delete("ecommerce/stores/$this->store_id/carts/$cart_id");
        return $response;
    }

    /**
     * Format cart items array from session
     * @return array;
     */
    private function formatCartItems() {
        $positions = \Yii::$app->cart->getPositions();
        $lines = [];
        foreach ($positions as $variant) {
            $lines[] = [
                'id' => (string) $variant->id,
                'product_id' => substr($variant->vendore_code, 0, -6),
                'type' => $variant->product->globalCategory->name_ru,
                'product_variant_id' => $variant->vendore_code,
                'quantity' => (integer) $variant->getQuantity(),
                'price' => $variant->price
            ];
        }
        return $lines;
    }

}