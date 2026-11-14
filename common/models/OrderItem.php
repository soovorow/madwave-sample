<?php

namespace common\models;

/**
 * Order Items model
 *
 * @property int $id
 * @property int $order_id
 * @property string $name
 * @property int $quantity
 * @property string $vendore_code
 * @property string $size
 * @property string $color
 * @property double $clearPrice
 * @property double $clearPriceWDiscount
 * @property double $sum
 * @property double $discount
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class OrderItem extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     * @todo implement migration
     */
    public static function tableName()
    {
        return 'order_items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//            [['quantity'], 'number'],
        ];
    }

    public function getDiscountPercent()
    {
        $onePercentCost = $this->clearPrice / 100;
        $discountPercent = ($this->clearPrice - ($this->sum / $this->quantity)) / $onePercentCost;
        return $discountPercent;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
//            'id' => 'ID',
//            'order_id' => 'Order ID',
//            'title' => 'Title',
//            'price' => 'Price',
//            'product_id' => 'Product ID',
//            'quantity' => 'Quantity',
        ];
    }

    /**
     * Get product variant
     *
     * @return ProductVariant
     */
    public function getProductVariant()
    {
        return ProductVariant::findOne(['vendore_code' => $this->vendore_code]);
    }

    /**
     * Get order
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }
}
