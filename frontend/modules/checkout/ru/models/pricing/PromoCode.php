<?php

namespace frontend\modules\checkout\ru\models\pricing;

use common\models\LocalCategory;
use common\models\ProductVariant;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Migration;
use yii\db\Schema;
use yii\helpers\Html;

/**
 * Promo Codes model
 *
 * @property int $id
 * @property string $title
 * @property string $code
 * @property int $n_usage
 * @property int $was_used
 * @property int $type
 * @property double $value
 * @property string $group
 * @property int $excluding_type
 * @property int $allow_with_card
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class PromoCode extends ActiveRecord implements KeyDiscountInterface
{
    const SESSION_KEY = 'coupon';

    const TYPE_DISCOUNT = 1;
    const TYPE_VOUCHER = 2;
    const TYPE_GIFT = 3;

    public $how_many;

    public static function fetchFromSession()
    {
        $session = \Yii::$app->session;

        if (!$session->has(self::SESSION_KEY)) {
            return false;
        }

        if ($code = PromoCode::find()->where(['code' => $session->get(self::SESSION_KEY)])->one()) {
            return $code;
        }

        $session->remove(self::SESSION_KEY);
        return false;
    }

    public static function apply($promo_code)
    {
        if (empty($promo_code)) {
            return 'Поле "Промокод" не может быть пустым значением.';
        }

        /** @var $code PromoCode */
        $code = PromoCode::find()->where(['code' => $promo_code])->one();

        if (!$code) {
            return 'Такого купона не существует';
        }

        if (CardOnly::fetchFromSession() && $code->type == $code::TYPE_VOUCHER) {
            return 'Этот промокод не позволено использовать вместе со скидкой при оплате онлайн';
        }

        if (!$code->isAllowedWithCard()) {
            return 'Данный промокод не позволено использовать вместе со скидкой по карте клиента';
        }

        if ($code->isOutUse()) {
            return 'Купон уже использован максимальное количество раз';
        }

        if (!empty($code->group) || $code->type == self::TYPE_GIFT) {

            $hasItemForGift = false;
            $cart_has_items_from_this_group = false;
            $isGiftOnWholeCategory = $code->type == self::TYPE_GIFT && $code->value === '0';
            $isGiftOnCertainProduct = $code->type == self::TYPE_GIFT && $code->value !== '0';

            $cat = LocalCategory::findOne($code->group);

            $variants = \Yii::$app->cart->getPositions();
            foreach ($variants as $v) {
                $v_category = $v->product->category;
                $isInSettedCategory = $v_category->id == $code->group || $v_category->parent->id == $code->group;

                if ($isInSettedCategory) {
                    $cart_has_items_from_this_group = true;
                    if ($isGiftOnWholeCategory) $hasItemForGift = $v;
                }

                if ($isGiftOnCertainProduct) {
                    $vendore_code = substr(str_replace(' ', '', $v->vendore_code), 0, 7);
                    $coupon_vendore_code = substr(str_replace(' ', '', $code->value), 0, 7);
                    if ($vendore_code == $coupon_vendore_code) $hasItemForGift = $v;
                }
            }
            if (!empty($code->group) && !$cart_has_items_from_this_group) {
                return 'Купон действует только на товары из категории ' . Html::a($cat->title, $cat->url)
                    . ' но таких нет в вашей корзине';
            }
            if ($isGiftOnCertainProduct && !$hasItemForGift) {
                $v = ProductVariant::find()->where(['like', 'vendore_code', $code->value])->one();
                return 'Купон действует только на товар ' . Html::a($v->name, $v->url)
                    . ' но его нет в вашей корзине';
            }
        }

        \Yii::$app->session->set(self::SESSION_KEY, $code->code);
    }

    public static function discard()
    {
        \Yii::$app->session->remove(self::SESSION_KEY);
    }

    public static function getDiscountSum() {
        /** @var $coupon PromoCode */
        $coupon = self::fetchFromSession();
        $coupon_discount_sum = false;
        if ($coupon) {
            $coupon_discount_sum = $coupon->getSum();
        }
        return $coupon_discount_sum;
    }

    public static function types()
    {
        return [
            self::TYPE_DISCOUNT => 'Скидка (%)',
            self::TYPE_VOUCHER => 'Ваучер (SUM)',
            self::TYPE_GIFT => 'Подарок (PRODUCT)',
        ];
    }

    public function generateCodes()
    {
        for ($i = 0; $i < $this->how_many; $i++) {
            $model = new PromoCode();
            $model->title = $this->title;
            $model->n_usage = $this->n_usage;
            $model->type = $this->type;
            $model->value = $this->value;
            $model->group = $this->group;
            $model->excluding_type = $this->excluding_type;
            $model->was_used = 0;
            $model->code = self::createUniqueString();
            $model->save(false);

        }
        return true;
    }

    public function getSum()
    {
        $discount = 0;

        $limit = $this->type === 2 ? $this->value : false;
        $is_gift_available = $this->type == 3;
        $cart_items = Yii::$app->cart->getPositions();

        foreach ($cart_items as $v) {
            $_discount = $this->getDiscount($v, $limit, $is_gift_available);
            $discount += $_discount['discount'];
            $limit = $_discount['limit'];
            $is_gift_available = $_discount['is_gift_available'];
        }

        return $discount;
    }

    public function getDiscount(ProductVariant $v, $limit = false, $is_gift_available = false)
    {

        $discount = 0;

        $isValidForDiscount = true;

        if ($this->excluding_type == 1 && $v->salePrice) {
            $isValidForDiscount = false;
        }

        if (!empty($this->group)) {
            if (!($v->product->category->id == $this->group || $v->product->category->parent->id == $this->group)) {
                $isValidForDiscount = false;
            }
        }

        if ($v->product->discount_code == 1) {
            $isValidForDiscount = false;
        }

        if ($isValidForDiscount) {

            $cost = $v->price;

            if ($this->type === 1) {
                $discount += $cost * ($this->value / 100);
            }

            if ($this->type === 2 && $limit > 0) {

                if (($limit - $cost) <= 0) {
                    $discount += $limit;
                    $limit = 0;
                } else {
                    $discount += $cost;
                    $limit = $limit - $cost;
                };

            }

            if ($this->type == 3 && $is_gift_available) {

                if ($this->value === '0') {
                    if ($v->product->category->id == $this->group || $v->product->category->parent->id == $this->group) {
                        $discount += $cost;
                        $is_gift_available = false;
                    }

                } else {

                    $vendore_code = substr(str_replace(' ', '', $v->vendore_code), 0, 7);
                    $coupon_vendore_code = substr(str_replace(' ', '', $this->value), 0, 7);

                    if ($vendore_code == $coupon_vendore_code) {
                        $discount += $cost;
                        $is_gift_available = false;
                    }

                }

            }

        }

        if ($this->type !== 2 && $this->type !== 3) {
            $discount = $discount * $v->getQuantity();
        }

        return ['discount' => $discount, 'is_gift_available' => $is_gift_available, 'limit' => $limit];
    }

    private function createUniqueString()
    {
        $code = strtoupper(Yii::$app->security->generateRandomString(10));
        $is_exist = PromoCode::find()->where(['code' => $code])->one();
        if (!$is_exist) {
            return $code;
        }
        return self::getUniqueCode();
    }

    private function isOutUse() {
        return $this->n_usage > 0 && $this->was_used === $this->n_usage;
    }

    private function isAllowedWithCard() {
        return !($this->allow_with_card == 0 && \Yii::$app->session->has('card'));
    }



    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            [
                [
                    'n_usage',
                    'type',
                    'title',
                    'value',
                ],
                'required'
            ],
            ['how_many', 'integer', 'min' => 1],
            ['n_usage', 'integer', 'min' => 0],
            [
                [
                    'excluding_type',
                    'group',
                ],
                'default'
            ],

            ['was_used', 'default', 'value' => 0],
            ['how_many', 'default', 'value' => 1],
            ['allow_with_card', 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'title' => 'Название',
            'how_many' => 'Количество',
            'n_usage' => 'Сколько раз использовать',
            'was_used' => 'Был использвоан',
            'type' => 'Тип',
            'value' => 'Значение',
            'excluding_type' => 'Исключить',
            'group' => 'Только для категории',
            'allow_with_card' => 'Разрешить использовать вместе с картой',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {

        $table_name = 'promo_codes';

        if (Yii::$app->db->schema->getTableSchema($table_name) === null) {
            $migration = new Migration();
            $migration->createTable($table_name, [
                'id' => Schema::TYPE_PK,
                'title' => Schema::TYPE_TEXT,
                'code' => Schema::TYPE_TEXT . ' NOT NULL',
                'n_usage' => Schema::TYPE_INTEGER,
                'was_used' => Schema::TYPE_INTEGER,
                'type' => Schema::TYPE_INTEGER,
                'value' => Schema::TYPE_TEXT,
                'group' => Schema::TYPE_TEXT,
                'excluding_type' => Schema::TYPE_INTEGER,
                'allow_with_card' => Schema::TYPE_INTEGER,
            ]);
        }

        return $table_name;
    }
}
