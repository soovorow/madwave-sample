<?php

namespace common\widgets\product\page;

use yii\base\InvalidParamException;
use yii\base\Widget;

class TopBanner extends Widget
{
    public $model;

    public function run()
    {
        if (empty($this->model)) {
            return null;
        }

        if (!$this->model instanceof \common\models\content\TopBanner) {
            throw new InvalidParamException('Model is not instance of \common\models\content\TopBanner');
        }

        return $this->render('top_banner', [
            'model' => $this->model
        ]);
    }
}
