<?php namespace backend\controllers;

use common\models\User;

class ReviewsController extends CRUDController {

    public $main_model = 'common\models\Testimonials';
    public $title = 'Reviews';

    public function beforeAction($action)
    {
        User::checkAccess(User::RIGHT_LEVEL_1);
        return parent::beforeAction($action);
    }
}
