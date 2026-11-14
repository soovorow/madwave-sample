<?php 

namespace frontend\modules\citydetector\controllers;

use frontend\modules\citydetector\CityDetector;
use yii\web\BadRequestHttpException;

/**
 * Ajax controller
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class AjaxController extends \yii\web\Controller
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $this->layout = 'empty';
            return true;
        }
        return false;
    }

    public function actionLoadModal()
    {
        return $this->render('../../widgets/views/modal');
    }

    public function actionSetLocation() {
        if (!isset($_POST['input'])) {
            throw new BadRequestHttpException;
        }
        $input = $_POST['input'];
        $array = explode(',', $input);
        $region = null;
        if (!empty($array[1])) {
            $region = $array[1];
            $city = $array[0];
        } else {
            $city = $input;
        }
        CityDetector::setLocation($city, $region);
        return $city;
    }

}

