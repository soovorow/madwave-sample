<?php

namespace frontend\models;

use common\components\multilocal\Local;
use common\models\LocalCategory;
use common\models\products\Balance;
use common\models\ProductVariant;
use soovorow\product_features\models\Feature;
use soovorow\product_features\models\ProductFeatures;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * Product search model
 * @author Dmitry Suvorov <soovorow@gmail.com>
 *
 * @property integer price_lower
 * @property integer price_higher
 * @property mixed size
 * @property mixed color
 * @property mixed available
 * @property mixed feature
 * @property mixed category_like
 * @property mixed name_like
 */

class ProductSearch extends ProductVariant
{
    const IS_STOCK = 'stock';

    public $price_lower;
    public $price_higher;
    public $size;
    public $color;
    public $available;
    public $feature;
    public $category_like;
    public $name_like;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [];
    }

    public function rules()
    {
        return [
            [['price_lower', 'price_higher'], 'integer'],
            [
                [
                    'price_lower',
                    'price_higher',
                    'size',
                    'color',
                    'available',
                    'feature',
                    'category_like',
                    'name_like'
                ],
                'safe'
            ]
        ];
    }

    /**
     * Search method
     *
     * @param mixed $params $_GET params
     * @param int $category_id
     * @param bool $query_only
     * @return ActiveDataProvider|\yii\db\ActiveQuery
     * @internal param bool $queryOnly
     */
    public function search($params = [], $category_id = null, $query_only = false)
    {
        $productsQuery = ProductVariant::find()
            ->where([ProductVariant::tableName().'.status' => 0])
            ->innerJoinWith('product')
            ->innerJoin('category c', 'c.category_id = product.category_id')
            ->innerJoinWith('priceList price')
            ->innerJoinWith('stock vs')
//            ->innerJoin('(
//                SELECT product_id, max(st.quantity) as quantity FROM product_variant
//                INNER JOIN `variant_stock` `st` ON `product_variant`.`variant_id` = `st`.`variant_id`
//                WHERE `st`.`store_id` = '.Local::choose([
//                    Local::RU => '00005',
//                    Local::EU => '00049',
//                ]).'
//                GROUP BY product_id
//            ) q', 'product_variant.product_id = q.product_id AND vs.quantity = q.quantity')

            ->joinWith('measuredData google')
            ->joinWith('rating review')
        ;



        if (Yii::$app->language == 'ru-RU') {
            $price = $category_id == self::IS_STOCK ? 'price_rub_sale' : ProductVariant::getPriceColumn();
        } else {
            $price = ProductVariant::getPriceColumn();
        }

        if ($category_id == self::IS_STOCK) {
            $productsQuery->andWhere(['or', ['>', 'price.'.$price, 0], ['>', 'product.discount_card_percent', 10]]);
        } else {
            $productsQuery->andWhere(['>', 'price.'.$price, 0]);
        }

        if(!($category_id === null) && !($category_id == self::IS_STOCK)) {
            $productsQuery->andWhere(['product.category_id' => LocalCategory::getCategoryIds(
                LocalCategory::find()->indexBy('id')->orderBy('id')->all(), $category_id
            )]);
        }

        $priceColumn = $this->getPriceFilterColumn($category_id, $price);

        if (($this->load(['ProductSearch' => $params]) && $this->validate())) {

            $explodeName = null;

            if (!empty($this->name_like)) {
                $explodeName = explode(' ', $this->name_like);
                if (count($explodeName) > 1) {
                    $this->name_like = $explodeName;
                }
            }

            if (!empty($this->category_like)) {
                $explodeName = explode(' ', $this->category_like);
                if (count($explodeName) > 1) {
                    $this->category_like = $explodeName;
                }
            }

            if (is_array($explodeName)) {
                foreach ($explodeName as $k) {
                    $productsQuery->andFilterWhere([
                        'or',
                        ['like', 'product.name', $k],
                        ['like', 'c.'.Local::choose([Local::RU => 'name_ru',Local::EU => 'name_en',]), $k]
                    ]);
                }
            } else {
                $productsQuery->andFilterWhere([
                    'or',
                    ['like', 'product.name', $this->name_like],
                    ['like', 'c.'.Local::choose([Local::RU => 'name_ru',Local::EU => 'name_en',]), $this->category_like]
                ]);
            }



            $productsQuery->andFilterWhere(['>=', $priceColumn, $this->price_lower]);
            $productsQuery->andFilterWhere(['<=', $priceColumn, $this->price_higher]);
            $productsQuery->andFilterWhere(['product_variant.size_id' => $this->size]);
            $productsQuery->andFilterWhere(['product_variant.color_id' => $this->getInColorArray($this->color)]);

            if (isset($params['available'])) {
                $productsQuery->andFilterWhere(['>', 'vs.quantity', 0]);
            }
            if (isset($params['feature'])) {
                if (count($params['feature']) > 0) {
                    foreach ($params['feature'] as $group_id => $type_id) {

                        $addFeatureSQL = ProductFeatures::find()
                            ->innerJoin(Feature::tableName().' f', 'f.id = '.ProductFeatures::tableName().'.feature_id')
                            ->andWhere(['f.type_id' => $type_id])
                            ->prepare(\Yii::$app->db->queryBuilder)
                            ->createCommand()
                            ->rawSql
                        ;
                        $productsQuery->innerJoin('('.$addFeatureSQL.') pf'.$group_id, 'pf'.$group_id.'.product_id = product_variant.product_id');
                    }
                }
            }
        }

        $productsQuery->groupBy('product_variant.product_id');

        if ($query_only) {
            return $productsQuery;
        }

        $pushNotAvailable = '(case when SUM(vs.quantity) > 0 then 9 else 10 end)';

        $dataProvider = new ActiveDataProvider([
            'query' => $productsQuery,

            /**
             * @todo refactor with data provider sort or with sort model
             */
            'sort'=> [
                'defaultOrder' => [
                    'popularity' => SORT_DESC,
                ],
                'attributes' => [
                    'popularity' => [
                        'asc' => [$pushNotAvailable => SORT_ASC, 'product.pull_in_order' => SORT_DESC, 'google.ctr' => SORT_DESC],
                        'desc' => [$pushNotAvailable => SORT_ASC, 'product.pull_in_order' => SORT_DESC, 'google.ctr' => SORT_DESC],
                        'label' => 'Low to high',
                        'default' => SORT_DESC
                    ],
                    'low_to_high' => [
                        'asc' => [$pushNotAvailable => SORT_ASC, $priceColumn => SORT_ASC],
                        'desc' => [$pushNotAvailable => SORT_ASC, $priceColumn => SORT_ASC],
                        'label' => 'Low to high',
                        'default' => SORT_ASC
                    ],
                    'high_to_low' => [
                        'asc' => [$pushNotAvailable => SORT_ASC, $priceColumn => SORT_DESC],
                        'desc' => [$pushNotAvailable => SORT_ASC, $priceColumn => SORT_DESC],
                        'label' => 'High to low',
                        'default' => SORT_DESC
                    ],
                    'by_discount' => [
                        'asc' => [$pushNotAvailable => SORT_ASC, 'product.discount_card_percent' => SORT_DESC],
                        'desc' => [$pushNotAvailable => SORT_ASC, 'product.discount_card_percent'=> SORT_DESC],
                        'label' => 'By Discount',
                        'default' => SORT_DESC
                    ],
                    'by_rating' => [
                        'asc' => [$pushNotAvailable => SORT_ASC, 'review.rating' => SORT_DESC],
                        'desc' => [$pushNotAvailable => SORT_ASC, 'review.rating'=> SORT_DESC],
                        'label' => 'By Discount',
                        'default' => SORT_DESC
                    ],
                ]
            ],
            'pagination' => [
                'defaultPageSize' => 15
            ],
        ]);


        if (isset($params['pageSize'])) {
            $dataProvider->pagination->pageSize = $params['pageSize'];
        }

        return $dataProvider;
    }

    /**
     * Search by one keyword
     * @param string $keywords search keywords
     * @param bool $queryOnly return ActiveQuery if true, dataProvider if false
     * @return ActiveDataProvider|\yii\db\ActiveQuery
     */
    public function searchByKeyword($keywords, $queryOnly = false)
    {
        $keywords = trim(strtolower($keywords));

        if ($queryOnly) {
            if ($vendor_code = ProductVariant::isVendorCode($keywords)) {
                $productQuery = ProductVariant::findByVendorCode($vendor_code);
            } else {
                $productQuery = $this->search(['category_like' => $keywords,'name_like' => $keywords], null, true);
            }
            return $productQuery;
        }

        if ($vendor_code = ProductVariant::isVendorCode($keywords)) {
            $dataProvider = new ActiveDataProvider([
                'query' => ProductVariant::findByVendorCode($vendor_code),
                'pagination' => [
                    'pageSize' => 12,
                ],
            ]);
        } else {
            $dataProvider = $this->search([
                'category_like' => $keywords,
                'name_like' => $keywords
            ]);
        }
        return $dataProvider;
    }

    /**
     * Helper method to compose color IN [array]
     *
     * @param array $colors params[color] from GET request
     * @return array
     */
    private function getInColorArray($colors)
    {
        $in_color = [];

        if (!empty($colors)) {
            foreach ($colors as $id => $c) {

                $in_color[] = $id;

                if ($id == '0004') {
                    $in_color[] = '0012';
                }

                if ($id == '0012') {
                    $in_color[] = '0004';
                }

                if ($id == '0025') {
                    $in_color[] = '0023';
                }

                if ($id == '0023') {
                    $in_color[] = '0025';
                }

            }
        }

        return $in_color;
    }

    /**
     * Get column for price filtering
     * @param $category_id
     * @param $price
     * @return string
     */
    private function getPriceFilterColumn($category_id, $price)
    {
        if ($category_id == self::IS_STOCK) {
            $priceColumn = '
            (case 
                when price.price_rub_sale > 0 
                then (case when price.price_rub_sale > price.price_rub then price.price_rub else price.price_rub_sale end)
                else 
                (price.price_rub * ((100 - case when product.discount_card_percent > 10 then product.discount_card_percent else 10 end) / 100))
            end)';
        } else {
            $priceColumn = '(
                price.'.$price.' * ((100 - case when product.discount_card_percent > 10 then product.discount_card_percent else 10 end) / 100)
            )';
        }

        return $priceColumn;
    }
}