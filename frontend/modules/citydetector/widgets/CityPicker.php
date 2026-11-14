<?php

namespace frontend\modules\citydetector\widgets;

use frontend\modules\citydetector\CityDetector;
use yii\base\Widget;

/**
 * CityPicker Widget
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class CityPicker extends Widget
{
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
        return $this->render('city-picker', [
            'user_city' => CityDetector::getCity(),
        ]);
    }

}
