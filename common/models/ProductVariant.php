<?php

namespace common\models;

use common\components\multilocal\Local;
use common\models\products\Balance;
use common\models\products\Color;
use common\models\products\Price;
use common\models\products\Size;
use Yii;
use yii\behaviors\SluggableBehavior;
use yii\bootstrap\Html;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Json;
use yii\httpclient\Client;
use yz\shoppingcart\CartPositionInterface;
use yz\shoppingcart\CartPositionTrait;

/**
 * Product Variant model
 *
 * @property int $id
 * @property int $variant_id
 * @property int $product_id
 * @property string $size_id
 * @property string $color_id
 * @property string $name
 * @property string $vendore_code
 * @property string $barcode
 * @property double $weight
 * @property double $volume
 * @property int $preorder
 * @property Balance|ActiveQuery $stock
 * @property string $gender
 * @property int $status
 * @property Product product
 * @property Price priceList
 * @property Color $color
 * @property Size $size
 * @property mixed url
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class ProductVariant extends ActiveRecord implements CartPositionInterface
{

    use CartPositionTrait;

    const IMG_ORIGINAL = 'original';
    const IMG_SMALL = 'small';
    const IMG_MEDIUM = 'medium';
    const IMG_LARGE = 'large';

    /**
     * @inheritdoc
     */
    public static function getDb()
    {
        return \Yii::$app->madwave_rpoducts;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product_variant';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => SluggableBehavior::className(),
                'attribute' => 'name',
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description'], 'string'],
            [['category_id',], 'integer'],
            [['price'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'slug' => 'Slug',
            'description' => 'Description',
            'category_id' => 'Category ID',
            'price' => 'Price',
        ];
    }

    /**
     * Get variant id === sku
     *
     * @return int
     */
    public function getId()
    {
        return $this->variant_id;
    }

    /**
     * Is new product
     *
     * @return bool
     */
    public function isNew()
    {
        $newProducts = [
            1497,
            1498,
            501,
            1493,
            1385,
            1272,
            1433,
            992,
            1277
        ];
        return in_array($this->product_id, $newProducts);
    }

    /**
     * Weight getter
     * Return item weight in kg
     * @author Dmitry Suvorov <soovorow@gmail.com>
     * @since 2.1.2
     */
    public function getWeight()
    {
        return $this->weight;
    }

    public static function getPriceColumn()
    {
        return Price::getPriceColumn();
    }

    public static function isVendorCode($string)
    {
        $string = strtolower(str_replace(' ', '', $string));
        if (preg_match('/m[0-9]{4}.*/', $string)) {
            return $string;
        }
        return false;
    }

    /**
     * @param $vendor_code
     * @return ActiveQuery
     */
    public static function findByVendorCode($vendor_code) {
        return ProductVariant::find()
            ->where(['status' => 0])
            ->innerJoin('price', '`price`.`variant_id` = `product_variant`.`variant_id`')
            ->andWhere(['>', 'price.'.Price::getPriceColumn(), 0])
            ->andWhere(['like', "REPLACE(vendore_code,' ','')", $vendor_code]);
    }

    /**
     * Get parent product
     *
     * @return ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['product_id' => 'product_id'])
                ->with('targetVariant')
            ;
//            ->where(['product.status' => 0]);
    }

    /**
     * Get stock info
     * @return Balance|ActiveQuery
     */
    public function getStock()
    {
        $store_id = Local::choose([
            Local::RU => '00005',
            Local::EU => '00049',
        ]);
        return $this->hasOne(Balance::className(), ['variant_id' => 'variant_id'])
            ->andWhere(['store_id' => $store_id])
        ;
    }

    /**
     * Get rating
     * @return ActiveQuery
     */
    public function getRating()
    {
        return $this->hasOne(Testimonials::className(), ['product_id' => 'product_id'])
            ->select('(SUM( rating ) / COUNT( rating )) AS rating')
        ;
    }

    public function getMeasuredData()
    {
        return $this->hasOne(OrderMetrics::className(), ['product_id' => 'product_id']);
    }



    /**
     * Get related prices
     */
    public function getPriceList()
    {
        return $this->hasOne(Price::className(), ['variant_id' => 'variant_id']);
    }

    public function getPurchasingPrice($in_rub = false)
    {
        return $this->priceList->getPurchasingPrice($in_rub);
    }

    /**
     * Get variant url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->product->url . '?sku=' . $this->variant_id;
    }

    /**
     * Get variant image
     *
     * @param int $n N-image
     * @param string $size image size original|small|medium|large
     * @param bool $url_only
     * @param bool $force if true && image !exist return placeholder
     * @return string $url_only === true ? url : <img>
     */
    public function getImage($n, $size, $url_only = false, $force = false)
    {
        if (YII_ENV_TEST) {
            return null;
        }
        $original = self::IMG_ORIGINAL;
        $filename = $this->id;
        $skuLength = strlen($this->id);

        $original_img = 'http://stash.madwave.ru/images/' . $original . '/pic_' . $n . '/' . $filename . '.jpg';
        $is_original_exist = @getimagesize($original_img);

        if (!$is_original_exist && $skuLength != 8) {
            $zero_count = 8 - $skuLength;
            for ($i = 0; $i < $zero_count; $i++) {
                $arr[] = 0;
            }
            $filename = implode('', $arr) . $this->id;
        }

        $url = 'http://stash.madwave.ru/images/' . $size . '/pic_' . $n . '/' . $filename . '.jpg';
        $is_image = @getimagesize($url);

        if ($size !== $original) {
            if (!empty($is_original_exist)) {

                $originalTime = @filectime('/var/www/admin/www/stash.madwave.ru/images/' . $original . '/pic_' . $n . '/' . $filename . '.jpg');

                if (!empty($is_image)) {
                    $thumbnaletime = @filectime('/var/www/admin/www/stash.madwave.ru/images/' . $size . '/pic_' . $n . '/' . $filename . '.jpg');
                }

                if (empty($is_image) || $originalTime > $thumbnaletime) {

                    $client = new Client();
                    $response = $client->createRequest()
                        ->setMethod('post')
                        ->setUrl('http://stash.madwave.ru/corp.php')
                        ->setData([
                            'variant_id' => $filename,
                            'n' => $n
                        ])
                        ->send();

                    if ($response->isOk && !$response->data['success']) {
                        $url = 'http://placehold.it/181x232';
                    }

                }
            }
        }

        if (!$is_image) {
            if ($force && $n < 5) {
                $n++;
                return $this->getImage($n, $size, $url_only);
            }
            return false;
        }

        $url = explode('/', explode('//', $url)[1]);
        unset($url[0]);
        $url = Yii::$app->params['baseUrl'] . '/' . implode('/', $url);

        $thumbnaletime = @filectime('/var/www/admin/www/stash.madwave.ru/images/' . $size . '/pic_' . $n . '/' . $filename . '.jpg');

        if ($url_only) {
            return $url.'?'.$thumbnaletime;
        }

        if ($url) {
            return '<img width="100%" src="' . $url.'?'.$thumbnaletime . '" alt="' . $this->product->globalCategory->name_ru . ' ' . $this->name . '">';
        }

    }

    public function getColor() {
        return $this->hasOne(Color::className(), ['color_id' => 'color_id']);
    }

    public function getSize() {
        return $this->hasOne(Size::className(), ['size_id' => 'size_id']);
    }

    public function getPrices() {
        $v = $this;

        $p = $v->product;

        $hasSalePrice = $v->getSalePrice();
        $getPrice = $v->getPrice();

        if ($hasSalePrice) {
            if (\Yii::$app->language == 'ru-RU') {
                $oldPrice = $v->getOldPrice();
            } else {
                $oldPrice = null;
            }
        } elseif ($p->discount_card !== 1) {
            $oldPrice = $getPrice;
        } else {
            $oldPrice = null;
        }

        if ($hasSalePrice || $p->discount_card == 1) {
            $price = $getPrice;
        } else {
            $price = $v->getCardPrice();
        }

        return [
            'old' => $oldPrice,
            'new' => $price
        ];
    }

    /**
     * Get product price
     *
     * @param bool $currency_sign
     * @return float
     */
    public function getPrice($currency_sign = false)
    {
        $priceList = $this->priceList;

        $local = Local::detectLocal();
        if ($local == Local::RU) {
            $price = $priceList->price_rub;
            if ($salePrice = $priceList->price_rub_sale) {
                $price = $price > $salePrice ? $salePrice : $price;
            }
        } else {
            $price = $priceList->price_eur_rrp;
        }

        return $currency_sign ? ($price . Yii::$app->params['currency']) : Local::choose([
            Local::EU => (double) $price,
            Local::RU => (integer) $price,
        ]);
    }

    /**
     * Get product old price
     *
     * @param bool $currency_sign
     * @return float
     */
    public function getOldPrice($currency_sign = false)
    {
        $price_column = Local::choose([
            Local::RU => 'price_rub',
            Local::EU => 'price_eur_rrp',
        ]);
        $price = $this->hasOne(Price::className(), ['variant_id' => 'variant_id'])->one()->$price_column;
        $salePrice = $this->getSalePrice();
        if ($salePrice) {
            if ($price > $salePrice) {
                $old = $price;
                $new = $salePrice;
            } else {
                $old = $salePrice;
                $new = $price;
            }
            $price = $old;
        }
        return $currency_sign ? ($price . Yii::$app->params['currency']) : Local::choose([
            Local::EU => (double) $price,
            Local::RU => (integer) $price,
        ]);
    }

    /**
     * Get product price with card discount
     *
     * @param bool $currency_sign
     * @return float
     */
    public function getCardPrice($currency_sign = false)
    {

        $product = $this->product;
        $unique_percent = $product->discount_card_percent;

        if ($unique_percent > 0 ) {
            $card_discount = $unique_percent;
        } else {
            $card_discount = $product->getClientCardDiscountPercent();
        }

        $d = $this->price * ((100 - $card_discount) / 100);

        $price = number_format($d, 0, '', '');
        return $currency_sign ? ($price . Yii::$app->params['currency']) : ((integer) $price);

    }

    /**
     * Get product sale price
     *
     * @return bool
     */
    public function getSalePrice()
    {
        $data = $this->hasOne(Price::className(), ['variant_id' => 'variant_id'])->one()->price_rub_sale;
        return $data > 0 ? $data : false;
    }

    /**
     * That variant has in stock?
     *
     * @return bool
     */
    public function hasInStock()
    {
        $data = Balance::find()
            ->where(['variant_id' => $this->id])
            ->andWhere(['>', 'quantity', 0])
            ->andWhere(['store_id' => Local::choose([
                Local::RU => '00005',
                Local::EU => '00049',
            ])])
            ->count()
        ;
        return $data > 0 ? true : false;
    }

    /**
     * Product has any variant in stock?
     *
     * @todo move method into prdoct model
     * @return bool
     */
    public function hasAnyInStock()
    {
        $data = ProductVariant::find()
            ->where(['product_id' => $this->product_id])
            ->innerJoin('variant_stock', '`variant_stock`.`variant_id` = `product_variant`.`variant_id`')
            ->andWhere(['>', 'variant_stock.quantity', 0])
            ->andWhere(['variant_stock.store_id' => Local::choose([
                Local::RU => '00005',
                Local::EU => '00049',
            ])])
            ->count();
        return $data > 0 ? true : false;
    }

    /**
     * Get variant parent product name
     *
     * @todo TODO rewrite implementation with ->product->name
     * @return string
     */
    public function getName()
    {
        return $this->product->name;
    }

    /**
     * Render modal action button
     *
     * @todo move into another class
     * @return string
     */
    public function renderCartButton($only_icon = false)
    {
        return $this->hasAnyInStock() ? Html::tag(
            'span',
            ($only_icon ? '<i class="fa fa-cart-plus"></i>' : 'В корзину'),
            [
                'class' => 'btn btn-lg btn-danger btn-add_to_cart add_to_cart-modal',
                'data-sku' => $this->id,
            ]
        )
            : Html::tag(
                'span',
                \Yii::t('app', ($only_icon ? '<i class="fa fa-bell"></i>' : 'Сообщить о наличии')),
                [
                    'class' => 'btn btn-warning btn-add_to_cart add_to_cart-modal',
                    'data-sku' => $this->id,
                ]
            );
    }

    public function getAvailableItem()
    {
        $av = $this->hasOne(ProductVariant::className(), ['product_id' => 'product_id'])
            ->innerJoin('variant_stock', '`variant_stock`.`variant_id` = `product_variant`.`variant_id`')
            ->andWhere(['>', 'variant_stock.quantity', 0])
            ->andWhere(['variant_stock.store_id' => Local::choose([
                Local::RU => '00005',
                Local::EU => '00049',
            ])])
            ->one();
        return $av ? $av : $this;
    }

    /**
     * Render add to cart ajax action button
     *
     * @todo move into another class
     * @param bool $hidden_input
     * @param bool|string $btn_value
     * @param bool|string $btn_class
     * @return string
     */
    public function renderAddToCartButton($hidden_input = false, $btn_value = false, $btn_class = false)
    {
        $isAvailable = ProductVariant::find()
            ->where(['status' => 0])
            ->innerJoin('price', '`price`.`variant_id` = `product_variant`.`variant_id`')
            ->andWhere(['>', 'price.' . Price::getPriceColumn(), 0])
            ->innerJoin('variant_stock', '`variant_stock`.`variant_id` = `product_variant`.`variant_id`')
            ->andWhere(['variant_stock.store_id' => Local::choose([
                Local::RU => '00005',
                Local::EU => '00049',
            ])])
            ->andWhere(['>', 'variant_stock.quantity', 0])
            ->andWhere(['product_variant.variant_id' => $this->id])
            ->all();

        $data_product = $this->formatGaArray();
        $data_product['price'] = $this->price;

        $quantity = [];
        for ($o = 1; $o <= 20; $o++) {
            $quantity[$o] = $o;
        }

        if ($isAvailable) {
            $input = Html::dropDownList('quantity', 1, $quantity, [
                'id' => 'add_to_cart-quantity',
                'class' => 'form-control',
                'style' => 'width: 100%;',
            ]);
            $field = Html::tag('b', \Yii::t('app','Quantity'), ['class'=>'titled']) . Html::tag('div', $input, ['class' => 'mb30' . ($hidden_input ? ' hidden' : '')]);
            $btn = Html::tag(
                'span',
                ($btn_value ? $btn_value : (\Yii::t('app','Add to cart').' <i class="fa fa-cart-plus"></i>')),
                [
                    'class' => 'add_to_cart-ajax btn-ga-cart-action ' . ($btn_class ? $btn_class : 'btn btn-danger btn-add_to_cart')
                        .' '.Local::choose([Local::RU => 'ru', Local::EU => 'eu']),
                    'data-sku' => $this->id,
                    'data-action' => 'add',
                    'style' => 'width: 100%',
                    'data-product' => Json::encode($data_product)
                ]
            )
            .(round($this->product->todayViewsCount / 2) > 20 ? Html::tag('div',
                '<i style="display: inline-block;
                    width: 20px;
                    height: 20px;
                    border: 2px solid #fecb39;
                    border-bottom: 7px solid #fecb39;
                    position: relative;
                    top: 4px;
                    
                    margin-right: 10px;"></i>'.Yii::t('app', 'Less than 10 left in stock'), [
                'class' => 'text-center',
                'style' => 'padding-top: 10px;'
            ]) : '')
            ;
        } else {
            $input = Html::input('email', 'subscriber', null, [
                'id' => 'add_to_cart-quantity',
                'class' => 'form-control mb30',
                'style' => 'width: 100%;',
            ]);
            $field = Html::tag('b', \Yii::t('app','Your e-mail').' <br><br>', ['class'=>'titled']) . Html::tag('div', $input, ['class' => 'mb30' . ($hidden_input ? ' hidden' : '')]);
            $btn = Html::tag(
                'span',
                \Yii::t('app', 'Report Availability'),
                [
                    'class' => 'btn btn-warning btn-add_to_cart add_to_cart-ajax',
                    'data-sku' => $this->id,
                    'style' => 'width: 100%',
                    'data-product' => Json::encode($data_product)
                ]
            );

            if (Local::detectLocal() == Local::RU) {
                $btn .= Html::tag('div', 'Вы можете оставить свой e-mail, для получения оповещения, когда товар вновь поступит в продажу', ['style' => 'padding: 10px 0']);
            }
        };

        $complete = Html::tag('div', Yii::t('app', 'PRODUCT HAS BEEN ADDED TO CART'), [
            'class' => 'add-to-cart--complete'
        ]);

        return $field . $btn . $complete;

    }

    /**
     * Renders new add to cart ajax action button
     *
     * @param bool $hidden_input
     * @param bool|string $btn_value
     * @param bool|string $btn_class
     * @return string
     */
    public function renderAddToCartButtonNew($hidden_input = false, $btn_value = false, $btn_class = false)
    {
        $isAvailable = ProductVariant::find()
            ->where(['status' => 0])
            ->innerJoin('price', '`price`.`variant_id` = `product_variant`.`variant_id`')
            ->andWhere(['>', 'price.' . Price::getPriceColumn(), 0])
            ->innerJoin('variant_stock', '`variant_stock`.`variant_id` = `product_variant`.`variant_id`')
            ->andWhere(['variant_stock.store_id' => Local::choose([
                Local::RU => '00005',
                Local::EU => '00049',
            ])])
            ->andWhere(['>', 'variant_stock.quantity', 0])
            ->andWhere(['product_variant.variant_id' => $this->id])
            ->all();

        $data_product = $this->formatGaArray();
        $data_product['price'] = $this->price;

        $quantity = [];
        for ($o = 1; $o <= 200; $o++) {
            $quantity[$o] = $o;
        }

        if ($isAvailable) {
            $input = Html::dropDownList('quantity', 1, $quantity, [
                'id' => 'add_to_cart-quantity',
                'class' => 'form-control',
            ]);
            $field = Html::tag(
                'div',
                Html::tag(
                    'form',
                    Html::tag(
                        'span',
                        $input.'<span class="fa fa-chevron-down"></span>',
                        [
                            'class' => 'mb30' . ($hidden_input ? ' hidden' : ''),
                        ]
                    )
                )
                .Html::tag(
                    'div',
                    \Yii::t('app','Quantity'),
                    [
                        'class' => 'title',
                    ]
                ),
                [
                    'class' => 'quantity',
                ]
            );

            $btn = Html::tag(
                'span',
                ($btn_value ? $btn_value : '<i class="fa fa-shopping-bag"></i>'.\Yii::t('app','Add to cart')),
                [
                    'class' => 'add_to_cart-ajax btn-ga-cart-action ' . ($btn_class ? $btn_class : 'btn btn-danger btn-add_to_cart'),
                    'data-sku' => $this->id,
                    'data-action' => 'add',
                    'style' => 'width: 100%',
                    'data-product' => Json::encode($data_product)
                ]
            ).(round($this->product->todayViewsCount / 2) > 20 ? Html::tag('div',
                    '<i style="display: inline-block;
                    width: 20px;
                    height: 20px;
                    border: 2px solid #fecb39;
                    border-bottom: 7px solid #fecb39;
                    position: relative;
                    top: 4px;
                    
                    margin-right: 10px;"></i>'.Yii::t('app', 'Less than 10 left in stock'), [
                        'class' => 'text-center',
                        'style' => 'padding-top: 10px;'
                    ]) : '');
        } else {
            $input = Html::input('email', 'subscriber', null, [
                'id' => 'add_to_cart-quantity',
                'class' => 'form-control mb30',
                'style' => 'width: 100%;',
            ]);
            $field = Html::tag('b', \Yii::t('app','Your e-mail').' <br><br>', ['class'=>'titled']) . Html::tag('div', $input, ['class' => 'mb30' . ($hidden_input ? ' hidden' : '')]);
            $btn = Html::tag(
                'span',
                \Yii::t('app', 'Report Availability'),
                [
                    'class' => 'btn btn-warning btn-add_to_cart add_to_cart-ajax',
                    'data-sku' => $this->id,
                    'style' => 'width: 100%',
                    'data-product' => Json::encode($data_product)
                ]
            );
            if (Local::detectLocal() == Local::RU) {
                $btn .= Html::tag('div', 'Вы можете оставить свой e-mail, для получения оповещения, когда товар вновь поступит в продажу', ['style' => 'padding: 10px 0']);
            }
        };

        return $field . $btn;
    }

    /**
     * Render product variant preview
     *
     * @todo move into another class?
     * @todo rewrite for avoid "click on available - get not available" event
     * @param string $list list name for google analytics
     * @param int $n N-position in list for google analytics
     * @param int $size jedi force key for modal grid size of preview block
     * @param bool $withoutWrap
     * @return string
     */
    public function preview($list, $n = null, $size = null, $withoutWrap = false)
    {

        $v = $this;

        if (isset($v->product->target_color) && $t = $v->product->getTargetVariant()->one()) {
            $v = $t;
        }

        $v->measureImpression($list, $n);
        return \Yii::$app->view->render('@frontend/views/catalog/_new_product', [
            'v' => $v,
            'list' => $list,
            'size' => $size,
            'n' => $n,
            'withoutWrap' => $withoutWrap,
        ]);

    }

//    public static function formatGaArray(ProductVariant $variant)
//    {
//        return [
//            'id'        => substr($variant->vendore_code, 0, -6),
//            'name'      => $variant->getName(),
//            'category'  => $variant->product->globalCategory->getName(),
//            'brand'     => $variant->product->brand,
//            'variant'   => $variant->vendore_code,
//        ];
//    }
//
    /**
     * Format product array for GA measurement
     * @author Dmitry Suvorov <soovorow@gmail.com>
     * @return array
     */
    public function formatGaArray()
    {
        return [
            'id'        => substr($this->vendore_code, 0, -6),
            'name'      => $this->getName(),
            'category'  => $this->product->globalCategory->getName(),
            'brand'     => $this->product->brand,
            'variant'   => $this->vendore_code,
        ];
    }

    /**
     * Measure Google Analytic Product Detail View
     * @author Dmitry Suvorov <soovorow@gmail.com>
     * @return void
     */
    public function measureDetailView()
    {
        $js = "ga('ec:addProduct', ".Json::encode($this->formatGaArray())."); ga('ec:setAction', 'detail');";
        $view = \Yii::$app->view;
        $view->registerJs($js, $view::POS_END);
    }

    /**
     * Measure Google Analytic Product Impression
     * @author Dmitry Suvorov <soovorow@gmail.com>
     * @param string $list
     * @param null|integer $position counter
     * @return void
     */
    private function measureImpression($list, $position = null)
    {
        $product = $this->formatGaArray();

        $product['list'] = $list;

        if ($position) {
            $product['position'] = $position;
        }
        $js = "ga('ec:addImpression', ".Json::encode($product).")";
        $view = \Yii::$app->view;
        $view->registerJs($js, $view::POS_END);
    }

}

