<?php

namespace backend\controllers;

use common\models\User;

class ActionLogController extends CRUDController {

    public $main_model = 'backend\models\UserActionsLog';
    public $title = 'Action Log';

    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        User::checkAccess(User::RIGHT_LEVEL_8);
        return parent::beforeAction($action);
    }
}
