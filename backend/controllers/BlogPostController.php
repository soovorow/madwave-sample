<?php

namespace backend\controllers;

use common\models\User;
use yii\widgets\Menu;

/**
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class BlogPostController extends CRUDController {

    public $main_model = 'common\models\BlogPost';
    public $title = 'posts';

    public function getMenu() {
        return Menu::widget([
            'options' => [
                'class' => 'list-group'
            ],
            'itemOptions' => ['class' => 'list-group-item'],
            'items' => [
                ['label' => 'Posts', 'url' => ['blog-post/index']],
                ['label' => 'Categories', 'url' => ['blog-category/index']],
            ],
        ]);
    }

    public function beforeAction($action)
    {
        User::checkAccess(User::RIGHT_LEVEL_5);
        return parent::beforeAction($action);
    }
}
