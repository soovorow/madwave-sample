<?php

namespace common\widgets\product\page;

use common\models\Product;
use common\models\Testimonials;
use common\widgets\product\review\ReviewRate;
use frontend\widgets\Rating;
use Yii;
use yii\base\Widget;

/**
 * Product reviews widget
 * @author Dmitry Suvorov <soovorow@gmail.com>
 *
 */

class Reviews extends Widget
{

    public $testimonialsProvider;
    public $testimonialsForm;
    public $product;
    public $v;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->testimonialsProvider = Testimonials::getDataProvider($this->product);
        $this->testimonialsForm = new Testimonials([
            'product_id' => $this->product->id,
            'user_id' => \Yii::$app->user->isGuest ? '' : \Yii::$app->user->identity->getId()
        ]);
        /**
         * @var $product Product
         */
        $product = $this->product;
        $v = $this->v;

        $rating = $product->getRating();

        $total_size_much_widget = null;

        if (
            count($product->getVariantsBySizes(null, $v->color_id)) > 0
            && !empty($product->getVariantsBySizes(null, $v->color_id)[0]['size_id'])
        ) {

            $total_size_much_widget = ReviewRate::widget([
                'label' => \Yii::t('app', 'Size'),
                'active' =>  $product->getSizeMatchRating(),
                'caption' => [\Yii::t('app', 'Too small'), \Yii::t('app', 'Too big')],
                'options' => ['class' => 'col-md-4']
            ]);
        }

        return $this->render('product_review', [
            'model' => $product,
            'v' => $v,
            'rating' => $rating,
            'raring_widget' => Rating::widget(['rating' => $rating]),
            'review_count_string' => Yii::t('app', 'Based on {n,plural, one{# review} other{# reviews}}', [
                'n' => $product->getRatingVotes()
            ]),
            'total_quality_widget' => ReviewRate::widget([
                'label' => \Yii::t('app', 'Quality'),
                'active' => $product->getQualityRating(),
                'caption' => [\Yii::t('app', 'Low'), \Yii::t('app', 'High')],
                'options' => ['class' => 'col-md-4']
            ]),
            'total_size_much_widget' => $total_size_much_widget,
            'testimonialsProvider' => $this->testimonialsProvider,
            'testimonialsForm' => $this->testimonialsForm,
        ]);
    }
}
