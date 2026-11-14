<?php

namespace common\models\products;

/**
 * Product Attribute model
 *
 * Model store many-to-many relation between Product and Product Attributes
 *
 * @todo write @property
 * @todo write migration
 * @todo getName() looks very wired that return Feature but not the Attribute
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class ProductAttribute extends \yii\db\ActiveRecord
{

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
        return 'product_attribute';
    }

    /**
     * Get name
     * @return Features name
     */
    public function getName()
    {
        return Features::find()->where(['attribute_id' => $this->attribute_id])->one()->name;
    }

}
