<?php

namespace common\widgets\product\review;

use common\models\Product;
use common\models\ProductVariant;
use yii\base\Widget;
use yii\helpers\Html;

class ReviewRate extends Widget
{

    public $active;
    public $caption;
    public $label;
    public $options;
    public $model;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $items = '';
        for ($i = 1; $i <= 7; $i ++) {
            $items .= Html::tag('div', null, ['class' => number_format($this->active) == $i ? 'active' : null]);
        }

        $scale = Html::tag('div', $items, ['class' => 'review-progress']);

        $caption = null;
        if (!empty($this->caption)) {
            $elements = '';

            if (is_array($this->caption)) {
                $count = count($this->caption);
                foreach ($this->caption as $i => $c) {
                    $additionalClass = null;
                    $i = $i + 1;
                    if ($i > 1 && $i < $count) {
                        $additionalClass = 'text-center';
                    }
                    if ($i == $count) {
                        $additionalClass = 'text-right';
                    }
                    $elements .= Html::tag('div', $c, ['class' => ('col-xs-6 '.$additionalClass)]);
                }
            }

            $caption = Html::tag('div', $elements, ['class' => 'row']);
        }
        return Html::tag('div', ('<h5>'.$this->label.'</h5>'.$scale.$caption), $this->options);
    }

}
