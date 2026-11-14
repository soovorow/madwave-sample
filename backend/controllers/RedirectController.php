<?php namespace backend\controllers;

use common\models\User;

class RedirectController extends CRUDController {

    public $main_model = 'common\components\screaming_crawl\models\Redirect';
    public $title = 'Redirects';

    public function beforeAction($action)
    {
        User::checkAccess(User::RIGHT_LEVEL_9);
        return parent::beforeAction($action);
    }
}
