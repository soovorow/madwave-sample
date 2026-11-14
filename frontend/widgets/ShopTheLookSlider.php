<?php
namespace frontend\widgets;

use common\components\multilocal\Local;
use common\models\TheLook;
use yii\base\Widget;
use yii\data\ActiveDataProvider;

/**
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */

class ShopTheLookSlider extends Widget
{
    public $looks;
    public $model;

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
        foreach ($this->looks as $i => $look) {
            if ($look->id == $this->model->id) {
                $k = $i;
            }
        }

        if (!isset($k)) {
            $k = 0;
        }

        $looks_count = count($this->looks);

        return $this->render('shop-the-look-slider', [
            'looks' => $this->looks,
            'key' => $k,
            'slideToShow' => $looks_count > 4 ? 4 : $looks_count
        ]);
    }

}
