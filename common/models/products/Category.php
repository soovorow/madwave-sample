<?php

namespace common\models\products;

use common\components\multilocal\Local;
use common\models\LocalCategory;
use common\models\Product;
use yii\behaviors\SluggableBehavior;

/**
 * Global Category model
 *
 * @property int $category_id
 * @property int $parent_id
 * @property string $name_ru
 * @property string $name_en
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class Category extends \yii\db\ActiveRecord
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
        return 'category';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => SluggableBehavior::className(),
                'attribute' => 'title',
                'slugAttribute' => 'slug'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id'], 'default', 'value' => null],
            [['parent_id'], 'integer'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'category_id' => 'ID',
            'parent_id' => 'Parent',
            'title' => 'Title',
            'slug' => 'Slug',
        ];
    }

    public function getName() {
        return Local::choose([
            Local::RU => $this->name_ru,
            Local::EU => $this->name_en
        ]);
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->category_id;
    }

    /**
     * Get parent category
     *
     * @return Category
     */
    public function getParent()
    {
        return $this->hasOne(Category::className(), ['category_id' => 'parent_id']);
    }

    /**
     * Get child categories
     *
     * @return Category
     */
    public function getCategories()
    {
        return $this->hasMany(Category::className(), ['parent_id' => 'category_id']);
    }

    /**
     * Get products from category
     *
     * @return Product
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['category_id' => 'category_id']);
    }

    /**
     * Get local category
     *
     * @return array|\yii\db\ActiveRecord
     */
    public function getLocalCategory()
    {
        return LocalCategory::find()->where(['like', 'linked_categories', '"' . $this->category_id . '"'])->one();
    }
    
}
