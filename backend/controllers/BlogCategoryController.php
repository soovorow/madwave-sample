<?php namespace backend\controllers;

use yii\widgets\Menu;

class BlogCategoryController extends CRUDController {

    public $main_model = 'common\models\BlogCategory';
    public $title = 'blog categories';

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

}
