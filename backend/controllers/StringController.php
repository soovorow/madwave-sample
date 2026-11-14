<?php namespace backend\controllers;

use common\models\User;

class StringController extends CRUDController {

    public $main_model = 'common\models\AppString';
    public $title = 'Strings';

    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        User::checkAccess(User::RIGHT_LEVEL_5);
        return parent::beforeAction($action);
    }
}
