<?php

namespace common\widgets\product\review;

use yii\base\Widget;

class Form extends Widget
{
    public $testimonialsForm;
    public $model;
    public $v;
    public $isEmpty;

    public function run()
    {
        return $this->render('form', [
            'testimonialsForm' => $this->testimonialsForm,
            'model' => $this->model,
            'v' => $this->v,
            'isEmpty' => $this->isEmpty
        ]);
    }

}
