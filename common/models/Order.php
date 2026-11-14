<?php
namespace common\models;

use baibaratsky\yii\google\analytics\MeasurementProtocol;
use common\components\MailChimp;
use common\components\multilocal\Local;
use common\components\postifi\Posti;
use common\models\products\Balance;
use frontend\modules\checkout\eu\models\delivery_methods\MethodPickup;
use frontend\modules\checkout\eu\models\payment_methods\MethodOnline;
use frontend\modules\checkout\ru\models\Delivery;
use frontend\modules\checkout\ru\models\delivery_methods\DeliveryMethodInterface;
use frontend\modules\checkout\ru\models\Payment;
use frontend\modules\checkout\ru\models\payment_methods\MethodPsBank;
use frontend\modules\checkout\ru\models\pricing\PromoCode;
use frontend\models\user\PasswordResetRequestForm;
use Nette\Neon\Exception;
use Nette\Utils\Json;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yz\shoppingcart\ShoppingCart;

/**
 * Order Model
 *
 * @implements OrderInterface
 *
 * @property string $customer_lastname
 * @property string $customer_firstname
 * @property string $customer_secondname
 * @property string $customer_phone
 * @property string $customer_email
 * @property string $customer_notes
 * @property string $track_code
 * @property string $city
 * @property string $post_index
 * @property string $address
 * @property string $delivery_method
 * @property string $client_card
 * @property string $coupon
 * @property double $delivery_price
 * @property double $primary_total
 * @property double $total_discount
 * @property string $call_status
 * @property double $total
 * @property double $percent
 * @property double $preparedSum
 * @property double $client_discount
 * @property double $coupon_discount
 * @property string $payment_method
 * @property integer $id
 * @property mixed created_at
 * @property mixed status
 * @property mixed country
 * @property string website
 * @property User user
 * @property string pick_up_point
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class Order extends ActiveRecord implements OrderInterface
{
    const STATUS_NEW = 'New';
    const STATUS_PAID = 'Paid';

    const CALL_STATUS_COMPLETE = 'complete';
    const CALL_STATUS_NOT_AVAILABLE = 'not-available';

    const TEST_STATUS_TRUE = 1;
    const TEST_STATUS_FALSE = null;

    /**
     * ACTIVE RECORD METHODS
     */

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{order}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_phone'], 'required'],
            [['customer_notes'], 'string'],
            [['customer_phone', 'customer_email'], 'string', 'max' => 255],
            [['customer_email'], 'email'],
            [['website'], 'default'],
            [['country'], 'default'],
            [['pick_up_point'], 'default'],
            [['payment_method', 'delivery_method', 'call_status', 'track_code', 'test_status', 'customer_notes'], 'default']
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->status = self::STATUS_NEW;
            }
            if ($this->getOldAttribute('call_status') !== $this->call_status && $this->call_status == self::CALL_STATUS_NOT_AVAILABLE) {
                \Yii::$app->mailer
                    ->compose('not-available-html', ['order' => $this])
                    ->setTo([$this->customer_email, 'servicemad@yandex.ru'])
                    ->setFrom('managerinternet@madwave.ru')
                    ->setSubject(('С вами не удалось связаться по вашему заказу на сайте Mad Wave'))
                    ->send();
            }

            if ($this->getOldAttribute('track_code') !== $this->track_code && !empty($this->track_code)) {
                \Yii::$app->mailer
                    ->compose('order-track-code-html', ['order' => $this])
                    ->setTo([$this->customer_email, 'servicemad@yandex.ru'])
                    ->setFrom('managerinternet@madwave.ru')
                    ->setSubject('Ваш код для отслеживания заказа на сайте Mad Wave')
                    ->send();
                $this->sendTrackCodeTo1c();
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Дата',
            'customer_phone' => 'Телефон',
            'address' => 'Адрес',
            'customer_email' => 'Email',
            'customer_notes' => 'Заметка',
            'customer_lastname' => 'Фамилия',
            'customer_firstname' => 'Имя',
            'customer_secondname' => 'Отчество',
            'status' => 'Оплата',
            'track_code' => 'Трек код',
            'call_status' => 'Статус дозвона',
            'test_status' => 'Тестовый заказ',
            'delivery_method' => 'Способ доставки',
            'delivery_price' => 'Стоимость доставки',
            'client_card' => 'Карта клиента',
            'coupon' => 'Купон',
        ];
    }


    /**
     * GET METHODS
     */

    public static function getPostiCountries()
    {
        return [
            'AL' => 'Albania',
            'AD' => 'Andorra',
            'AT' => 'Austria',
            'BY' => 'Belarus',
            'BE' => 'Belgium',
            'BA' => 'Bosnia And Herzegovina',
            'CY' => 'Cyprus',
            'CZ' => 'Czech Republic',
            'DK' => 'Denmark',
            'EE' => 'Estonia',
            'FI' => 'Finland',
            'FR' => 'France',
            'DE' => 'Germany',
            'GI' => 'Gibraltar',
            'GR' => 'Greece',
            'GL' => 'Greenland',
            'HU' => 'Hungary',
            'IS' => 'Iceland',
            'IE' => 'Ireland',
            'IT' => 'Italy',
            'LV' => 'Latvia',
            'LT' => 'Lithuania',
            'LU' => 'Luxembourg',
            'MK' => 'Macedonia',
            'MT' => 'Malta',
            'MC' => 'Monaco',
            'ME' => 'Montenegro',
            'NL' => 'Netherlands',
            'NO' => 'Norway',
            'PL' => 'Poland',
            'PT' => 'Portugal',
            'RO' => 'Romania',
            'SM' => 'San Marino',
            'RS' => 'Serbia',
            'SK' => 'Slovakia',
            'SI' => 'Slovenia',
            'ES' => 'Spain',
            'SE' => 'Sweden',
            'CH' => 'Switzerland',
            'TR' => 'Turkey',
            'UA' => 'Ukraine',
            'GB' => 'United Kingdom',
        ];
    }

    /**
     * Get order payment status
     */
    public static function getOrderStatuses()
    {
        return [
            self::STATUS_NEW => 'Неоплачен',
            self::STATUS_PAID => 'Оплачен',
        ];
    }

    /**
     * Get order call status
     */
    public static function getCallStatuses() {
        return [
            self::CALL_STATUS_COMPLETE => 'Дозвонились',
            self::CALL_STATUS_NOT_AVAILABLE => 'Недозвон',
        ];
    }

    /**
     * Get order test status
     */
    public static function getTestStatuses() {
        return [
            self::TEST_STATUS_TRUE => 'Это тестовый заказ',
            self::TEST_STATUS_FALSE => 'Это не тестовый заказ',
        ];
    }

    /**
     * Get session key to access complete page
     *
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function getSessionAccessKey()
    {
        return 'complete_access_to_' . $this->id;
    }

    /**
     * Get user
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['email' => 'customer_email']);
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get price without discounts and delivery
     *
     * @return double
     */
    public function getClearPrice()
    {
        $products = $this->getOrderItems();

        $sum = 0;

        foreach ($products as $product) {
            $sum += $product->clearPrice * $product->quantity;
        }

        return $sum;
    }

    /**
     * @inheritdoc
     */
    public function getTotal() {
        return $this->total + $this->delivery_price;
    }

    /**
     * @inheritdoc
     */
    public function getCustomerFullName() {
        $array = [$this->customer_firstname, $this->customer_secondname, $this->customer_lastname];
        return implode(' ', array_filter($array));
    }

    /**
     * @inheritdoc
     */
    public function getCustomerPhone() {
        return $this->customer_phone;
    }

    /**
     * @inheritdoc
     */
    public function getCustomerEmail() {
        return $this->customer_email;
    }

    /**
     * @inheritdoc
     */
    public function getDeliveryAddress()
    {
        $country = '';
        if (isset($this->getPostiCountries()[$this->country])) {
            $country = $this->getPostiCountries()[$this->country];
        }
        $array = [$this->post_index, $this->city, $this->address, $country];
        return implode(', ', array_filter($array));
    }

    /**
     * @inheritdoc
     */
    public function getPaymentMethod($code = false)
    {
        if ($code) {
            return $this->payment_method;
        }

        $payment = Local::choose([
            Local::EU => new \frontend\modules\checkout\eu\models\Payment(),
            Local::RU => new Payment()
        ]);

        foreach ($payment->_payment_methods as $m) {
            if ($this->payment_method == $m->getCode()) {
                return $m->getLabel();
            }
        }
        return 'unknown';
    }

    /**
     * @inheritdoc
     */
    public function getDeliveryMethod($code = false)
    {
        if ($code) {
            return $this->delivery_method;
        }
        if ($this->delivery_method == '"ТГ504642"') {
            return 'Самовывоз';
        }

        $delivery = Local::choose([
            Local::RU => new Delivery(),
            Local::EU => new \frontend\modules\checkout\eu\models\Delivery()
        ]);

        $methods = $delivery->method;
        foreach ($methods as $m) {
            /** @var $m DeliveryMethodInterface */
            if ($this->delivery_method == $m->getCode($this)) {
                return $m->getLabel();
            }
        }
        return 'unknown';
    }

    /**
     * @inheritdoc
     */
    public function getOrderItems()
    {
        return OrderItem::findAll(['order_id' => $this->id]);
    }

    /**
     * @inheritdoc
     */
    public function getWeight()
    {
        /**
         * @var $item OrderItem
         */
        $items = $this->getOrderItems();
        $weight = 0;

        foreach ($items as $item) {
            $weight += $item->getProductVariant()->weight;
        }

        return $weight;
    }

    /**
     * Get order details url for user account
     *
     * @return string
     */
    public function getUrl()
    {
        return Url::to(['account/order-details', 'id' => $this->getId()]);
    }


    /**
     * EXTERNAL INTEGRATIONS METHODS
     */

    /**
     * Send order notification email
     *
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function sendEmail()
    {
        $email_view = 'order-html';
        $view_params = ['order' => $this];
        $subject = Local::choose([
            Local::RU => 'Заказ на сайте Mad Wave'.' #'. $this->getId(),
            Local::EU => 'Order Mad Wave #'. $this->getId()
        ]);

        $to = [$this->customer_email, Yii::$app->params['orderNotificationBcc']];

        if (Local::detectLocal() == Local::EU) {
            $to[] = 'maxmadwave@icloud.com';
        }

        return Yii::$app->mailer->compose($email_view, $view_params)
            ->setTo($to)
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setSubject($subject)
            ->send();
    }

    /**
     * Send admit ad purchase
     */
    public function sendAdmitAd()
    {
        $items = $this->getOrderItems();
        $positionId = 1;

        if(empty($_COOKIE['admitad_uid'])) {
            return false;
        }

        $campaign_code = Yii::$app->params['admitad']['campaign_code'];
        $postback_key = Yii::$app->params['admitad']['postback_key'];

        foreach ($items as $item){

            $variant = $item->productVariant;
            $url = 'https://ad.admitad.com/r?campaign_code='.$campaign_code
                .'&postback=1&postback_key='.$postback_key
                .'&action_code=1&uid='.$_COOKIE['admitad_uid']
                .'&order_id='.$this->getId()
                .'&tariff_code=1&price='.$item->clearPriceWDiscount
                .'&quantity='.$item->quantity
                .'&position_id='.$positionId
                .'&position_count='.count($items)
                .'&product_id='.$variant->variant_id
                .'&payment_type=sale';

            @file_get_contents($url);

            $positionId++;
        }

        return true;
    }

    /**
     * Send order data to google analytics
     *
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function sendGaTransaction()
    {
        if (in_array(strtolower($this->customer_secondname), [
            'тест',
            'test',
        ])) {
            return false;
        }

        if (!isset($_COOKIE['_ga'])) {
            return false;
        }

        $gaCookie = array_reverse(explode('.', $_COOKIE['_ga']));
        $clientId = $gaCookie[1].'.'.$gaCookie[0];
        /**
         * @var $ga MeasurementProtocol
         */
        $ga = \Yii::$app->ga;
        $request = $ga->request();
        $request
            ->setClientId($clientId)
            ->setUserId($clientId)
            ->setTransactionId($this->getId())
//                ->setAffiliation('THE ICONIC')
            ->setRevenue(($this->total))
//                ->setTax(25.0)
            ->setShipping($this->delivery_price)
        ;

        $products = $this->getOrderItems();

        $i = 1;
        foreach ($products as $orderItem) {
            /**
             * @var $orderItem OrderItem
             */
            $v = ProductVariant::findOne(['vendore_code' => $orderItem->vendore_code]);
            $p = $v->product;
            $productData = [
                'sku' => substr($v->vendore_code, 0, -6),
                'name' => $p->name,
                'brand' => 'Mad Wave',
                'category' => $p->getLocalCategory()->title,
                'variant' => $v->color->name_ru,
                'price' => $orderItem->clearPriceWDiscount,
                'quantity' => $orderItem->quantity,
                'position' => $i
            ];
            $request->addProduct($productData);
            $i++;
        }

        $request
            ->setProductActionToPurchase()
            ->setEventCategory('Checkout')
            ->setEventAction('purchase')
            ->sendEvent();
    }

    /**
     * Send order data to 1C
     *
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function sendOrderTo1c()
    {
        $content = $this->composeTxt();
        $export_dir = Yii::$app->params['1c.order_export_dir'];
        $filename = '/order' . $this->getId() . '.txt';
        $fp = fopen($export_dir.$filename, 'wb');
        fwrite($fp, $content);
        fclose($fp);
    }

    /**
     * Send order track code to 1C
     *
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function sendTrackCodeTo1c()
    {
        $track = '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<Файл Track="'.$this->track_code.'" customer="'.$this->getCustomerFullName().'" email="'.$this->customer_email.'" date="'.date('d.m.Y', $this->created_at).'" order="'.$this->getId().'"/>';

        $filename = 'track' . $this->getId() . '.txt';
        $export_dir = Yii::$app->params['1c.track_code_export_dir'];
        $fp = fopen($export_dir.$filename, 'wb');
        fwrite($fp, $track);
        fclose($fp);
    }

    /**
     * Send order data to Mail Chimp
     *
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function sendOrderToMailChimp()
    {
        try {
            $mail_chimp = new MailChimp();
            $mail_chimp->addInterest($this->customer_email, 'order', true);
            $mail_chimp->createOrder($this);
        }
        catch (\yii\base\Exception $e) {
            Yii::warning($e->getMessage());
        }
    }

    /**
     * Send Mandrill Review Request Scheduling
     *
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function sendMandrillReviewRequestScheduling()
    {
        $order = $this;

        $to_email = $this->customer_email;
        $subject = 'Ваше мнение может быть решающим!';
        $send_at = date('Y-m-d H:i:s', $order->created_at + (60 * 60 * 24 * 21));

        $firstname = 'Уважаемый покупатель';
        if ($user = User::findByEmail($order->customer_email)) {
            if ($udata = UserData::findOne($user->id)) {
                if (!empty($udata->first_name)) {
                    $firstname = $udata->first_name;
                }
            }
        }

        $send_to = [['email' => $to_email,'type' => 'to']];
        $template_name = 'template';
        $template_content = [['name' => 'example name','content' => 'example content']];

        try {
            $mandrill = new \Mandrill('REDACTED_BEFORE_PUBLICATION');
            $message = array(
                'html' => '<p>Example HTML content</p>',
                'text' => 'Example text content',
                'subject' => $subject,
                'from_email' => \Yii::$app->params['adminEmail'],
                'from_name' => 'Mad Wave',
                'to' => $send_to,
                'track_opens' => true,
                'track_clicks' => true,
                'merge_vars' => [
                    [
                        'rcpt' => $to_email,
                        'vars' => [
                            [
                                'name' => 'firstname',
                                'content' => $firstname
                            ],
                            [
                                'name' => 'merge_product',
                                'content' => \Yii::$app->controller->view->render('@common/mail/mandrill/testimonials_products', [
                                    'order' => $order
                                ])
                            ]
                        ]
                    ]
                ],
                'tags' => ['review-request'],
            );
            $async = false;
            $ip_pool = 'Main Pool';
            $result = $mandrill->messages->sendTemplate($template_name, $template_content, $message, $async, $ip_pool, $send_at);
        } catch(Exception $e) {

        }
        return true;
    }


    /**
     * CREATION METHODS
     */

    /**
     * @inheritdoc
     */
    public function create($checkout = null)
    {
        /**
         * @var $cart ShoppingCart
         */
        $cart = Yii::$app->cart;

        $transaction = $this->getDb()->beginTransaction();

        $this->save(false);

        /**
         * @var $coupon \frontend\modules\checkout\ru\models\pricing\PromoCode;
         */
        if ($coupon = PromoCode::fetchFromSession()) {
            $is_gift_available = $coupon->type === PromoCode::TYPE_GIFT;
            $limit = $coupon->type === PromoCode::TYPE_VOUCHER ? $coupon->value : false;
            $coupon->was_used++;
            $coupon->save(false);
        }

        $products = $cart->getPositions();

        foreach ($products as $product) {
            /**
             * @var $product \common\models\ProductVariant
             */

            $orderItem = new OrderItem();

            $orderItem->order_id            = $this->id;
            $orderItem->vendore_code        = $product->vendore_code;
            $orderItem->name                = $product->name;

            $orderItem->size                = $product->size->name;
            $orderItem->color               = $product->color->name;

            $orderItem->clearPrice          = $product->getPrice();
            $orderItem->clearPriceWDiscount = Pricing::getItemSumWithDiscount($product);
            $orderItem->quantity            = $product->getQuantity();
            $orderItem->sum                 = Pricing::getItemSumWithDiscount($product) * $product->getQuantity();

            if ($coupon) {
                $_discount = $coupon->getDiscount($product, $limit, $is_gift_available);
                $limit = $_discount['limit'];
                $is_gift_available = $_discount['is_gift_available'];
                if ($_discount['discount'] > 0) {
                    $orderItem->discount = $_discount['discount'];
                }
            }

            /**
             * @var $balance Balance
             */
            $balance = $product->getStock()->one();

            if ($balance && $balance->quantity >= $product->getQuantity()) {
                $balance->reduce($product->getQuantity());
            } else {
                Yii::$app->session->addFlash($product->name.' - больше нет в наличии');
                $cart->remove($product);
                $transaction->rollBack();
                return false;
            }


            if (!$orderItem->save(false)) {
                $transaction->rollBack();
                return false;
            }
        }

        $this->initDelivery($checkout);

        $transaction->commit();

        (new MailChimp())->removeCart($this->getCustomerEmail());

        $this->insertUserData();

        $cart->removeAll();

        $this->setSessionAccess();

        return true;
    }


    /**
     * Init delivery dependencies
     * @param $checkout
     * @return bool
     */
    public function initDelivery($checkout = null)
    {
        $order = $this;
        if ($order->delivery_method == '"ТГ507168"') {

            $posti = new Posti();
            $posti->setCombinedId('REDACTED_BEFORE_PUBLICATION');

            $data = [
                'pdfConfig' => $posti->getPdfConfig(),
                'shipment' => [
                    'orderNo' => $order->getId(),
                    'sender' => [
                        'quickId' => '1',
                    ],
                    "receiver" => [
                        "name" => $order->customer_firstname . $order->customer_lastname . $order->customer_secondname,
                        "address1" => $order->address,
                        "zipcode" => $order->post_index,
                        "city" => $order->city,
                        "country" => $order->country,
                        "phone" => $order->customer_phone,
                        "email" => $order->customer_email,
                    ],
                    'service' => [
                        'id' => 'PO5001'
                    ],
                    'parcels' => [
                        [
                            'copies' => '1',
                            'weight' => 2,
                        ]
                    ],
                ]
            ];

            try {
                $posti->request($posti::METHOD_POST, '/shipments', $data);
            }
            catch (\Exception $e) {
                echo $e;
                if ($checkout !== null) {
                    $delivery = $checkout->_delivery;
                    $delivery->method[$delivery->delivery_method]->addError('postalCode', 'Not valid value');
                }
                return false;
            } ;
        }
    }

    /**
     * Update user data
     *
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    private function insertUserData()
    {
        if (!$user = $this->user) {
            $user = new User();
            $user->email = $this->customer_email;
            $user->username = '';
            $user->password_hash = $user->generateDrupalPasswordHash(Yii::$app->security->generateRandomString(8));
            $user->access = 0;
            $user->generateAuthKey();
            $user->save();
            $PasswordResetRequestForm = new PasswordResetRequestForm();
            $PasswordResetRequestForm->email = $user->email;
            $PasswordResetRequestForm->sendEmail();
        }

        if (!$user_data = UserData::findOne($user->id)) {
            $user_data = new UserData();
            $user_data->user_id = $user->id;
        }

        $user_data->last_name = $this->customer_lastname;
        $user_data->first_name = $this->customer_firstname;
        $user_data->second_name = $this->customer_secondname;
        $user_data->phone = $this->customer_phone;

        $user_data->save(false);
    }

    /**
     * Set session key to access complete page
     */
    public function setSessionAccess()
    {
        Yii::$app->session->set($this->getSessionAccessKey(), true);
    }


    /**
     * PRIVATE & TO REFACTORING METHODS
     */

    /**
     * Compose txt file for 1c export
     *
     * @param bool $itemsFromSession if true - txt was composed from items from session
     * @return string
     */
    public function composeTxt()
    {
        $products = $this->getOrderItems();
        $positions = '';
        $primTotal = 0;
        $percent = null;
        $orderCost = 0;

        $hasTextile = false;
        $textileItems = [];
        $hasAccessories = false;
        $accessoriesItems = [];

        // Get highly discount percent
        foreach ($products as $product)
        {
            /** @var $product OrderItem */
            $discountPercent = $product->getDiscountPercent();
            if ($discountPercent > $percent) {
                $percent = $discountPercent;
            }
        }

        $pos = 1;
        foreach ($products as $product)
        {
            /** @var $product OrderItem */

            $orderCost += $product->sum;

            $cost = ($product->sum / $product->quantity) / (100 - $percent) * 100;

            $primTotal += $cost;
            $arr = [
                $pos,
                '"' . $product->vendore_code . '"',
                '"' . $product->name . ' ' . $product->vendore_code . '"',
                '"' . $product->color . '"',
                '"' . $product->size . '"',
                $cost,
                $product->quantity
            ];

            $positions .= implode(',', $arr);
            $positions .= "\n";
            $pos++;
        }


        if ($this->website == Local::EU) {

            $positions .= implode(',', [
                    $pos,
                    '"M VAT 24 del"',
                    '"DELIVERY VAT 24%"',
                    '""',
                    '""',
                    $this->delivery_price / 100 * 24,
                    1
                ]) . "\n";

//            $this->delivery_price = $this->delivery_price + ($this->delivery_price / 100 * 24);

            $positions .= implode(',', [
                    $pos,
                    '"M VAT 24"',
                    '"ODD VAT 24%"',
                    '""',
                    '""',
                    ($this->total - ($this->delivery_price + ($this->delivery_price / 100 * 24))) / 124 * 24,
                    1
                ]) . "\n";
        }

        $total_discount_sum = $this->preparedSum + $this->delivery_price - $this->total;

        if ($pc = PromoCode::findOne(['code' => $this->coupon])) {
            $this->customer_notes.= ' Объем купона:'.$pc->value;
        }



        $data = [
            '"ТГ500932"',
            '"' . $this->customer_lastname . '"',                   // Фамилия
            '"' . $this->customer_firstname . '"',                  // Имя
            '"' . $this->customer_secondname . '"',                 // Отчество
            '',                                                     // Орган
            '"' . $this->customer_email . '"',                      // E-mail
            '',                                                     // Тел. Код
            '"' . $this->customer_phone . '"',                      // Телефон
            '"' . $this->city . '"',                                // Город
            '"' . $this->post_index . '"',                          // Почтовый индекс
            '"' . str_replace('"', '', $this->address) . '"',       // Адрес
            '"' . $this->client_card . '"',                         // Карта
            '"' . $this->coupon . '"',                              // Купон
            '"' . str_replace(["\r\n", "\r", "\n"], ' ', str_replace('"', '', $this->customer_notes)) . '"',// ДопИнф
            $primTotal,                                             // Сумма тотал без скидки первичная
            $total_discount_sum,                                    // abs($this->total_discount),// Сумма скидки с тотала
            $this->delivery_method,                                 // Код доставки
            $this->delivery_price,                                  // Цена доставки
            $this->total,                                           // Тотал по ордеру
            $percent,                                               // Процент скидки
            $this->website == Local::EU ? '00049' : '',
            $this->website == Local::EU ? '978' : '',
            '',
            '',
            $this->payment_method,
            $this->isOnlinePayment(),
            '"'.$this->pick_up_point.'"'
        ];

        if ($this->website == Local::EU) {
            $data[] = '00029';
        }
        
        return implode(',', $data) . "\n"
            . $positions;
    }

    public function isOnlinePayment() {
        $isOnlinePayment = 0;

        if ($this->payment_method == (new MethodPsBank())->getCode()) {
            $isOnlinePayment = 1;
        }

        if ($this->payment_method == (new MethodOnline())->getCode()) {
            $isOnlinePayment = 1;
        }

        return $isOnlinePayment;
    }

    /**
     * Get prepared sum of order
     *
     * @return double
     */
    public function getPreparedSum()
    {
        $products = $this->getOrderItems();

        $sum = 0;

        foreach ($products as $product) {
            $sum += $product->clearPrice * $product->quantity;
        }

        return $sum;
    }
}