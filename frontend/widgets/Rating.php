<?php namespace frontend\widgets;

use common\models\Product;
use common\models\ProductVariant;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;

class Rating extends Widget
{

    public $rating = 0;

    public $votesCount = 0;

    public $enableDescription = false;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $rating = $this->rating;

        $html = '<div class="rating-stars">';

        if ($this->rating == 0 && $this->votesCount == 0) {
            $html .= '<small>'.\Yii::t('app', 'not yet rated').'</small>';
            $html .= '</div>';
            return $html;
        }

        for ($r = 1; $r < 6; $r++) {
            if ($rating >= $r) {
                $star = 'fa-star';
            } elseif ($rating + 1 > $r) {
                $star = 'fa-star-half-o';
            } else {
                $star = 'fa-star-o';
            }

            $html.= '<i class="fa '.$star.'" style="margin-right: 4px"></i>';
        }

        $html .= '</div>';

        if ($this->enableDescription) {
            $html .= Html::tag('small',  Yii::t('app', 'Average rating'). ' ' .$this->rating, [
                'class' => 'average-rate'
            ]);
//            $message = \Yii::t('app',
//                '{n,plural, one{# review} few{# reviews} many{# reviews} other{# reviews}}', [
//                    'n' => $this->votesCount
//                ]);
//            $html .= '&nbsp;<small>'.$message.'</small>';
        }

        echo $html;
    }
}
