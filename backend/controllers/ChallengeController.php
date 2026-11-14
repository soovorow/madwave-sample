<?php

namespace backend\controllers;

use common\models\User;
use yii\widgets\Menu;

/**
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class ChallengeController extends CRUDController {

    public $main_model = 'common\models\content\ChallengeSteps';
    public $title = 'Challenge steps';

    public function beforeAction($action)
    {
        User::checkAccess(User::RIGHT_LEVEL_5);
        return parent::beforeAction($action);
    }
}
