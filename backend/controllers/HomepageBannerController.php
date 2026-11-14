<?php namespace backend\controllers;

use common\models\User;

class HomepageBannerController extends CRUDController {

    public $main_model = 'common\models\content\HomepageBanner';
    public $title = 'Homepage banner';

    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        User::checkAccess(User::RIGHT_LEVEL_5);
        return parent::beforeAction($action);
    }
}
