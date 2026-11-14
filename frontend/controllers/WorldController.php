<?php

namespace frontend\controllers;

use common\components\AdmitAd;
use common\components\Criteo;
use common\components\multilocal\Local;
use frontend\models\BlogPostSearch;
use common\models\BlogCategory;
use common\models\BlogPost;
use yii\helpers\Url;
use yii\web\Controller;

class WorldController extends Controller
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            Url::remember();
            return true;
        } else {
            return false;
        }
    }

    public function actionIndex($url_path, $url_path_2 = null)
    {

        $category = null;

        $id = BlogCategory::findOne(['slug' => !empty($url_path_2) ? $url_path_2 : $url_path])->id;

        $categories = BlogCategory::find()->indexBy('id')->all();

        $query = BlogPost::find()->where(['status' => 1])->orderBy('id DESC');

        if ($id !== null && isset($categories[$id])) {
            $category = $categories[$id];
            $query->where(['category_id' => $this->getCategoryIds($categories, $id)]);
        }

        if (Local::detectLocal() == Local::RU) {
            AdmitAd::setIndexPage();
            Criteo::setIndexPage();
        }

        return $this->render('index', [
            'category' => $category,
            'menuItems' => $this->getMenuItems($categories, isset($category->id) ? $category->id : null),
            'dataProvider' => (new BlogPostSearch())->search(\Yii::$app->request->queryParams),
            'pinnedPost' => BlogPost::findOne(11),
            'categories' => BlogCategory::find()->all()
        ]);
    }

    public function actionView($slug)
    {
        $post = BlogPost::findOne(['slug' => $slug]);
        $post->views++;
        $post->save(false);

        $categories = BlogCategory::find()->indexBy('id')->all();
        $category = $post->getCategory();

        if (Local::detectLocal() == Local::RU) {
            AdmitAd::setIndexPage();
            Criteo::setIndexPage();
        }

        return $this->render('view', [
            'model' => $post,
            'menuItems' => $this->getMenuItems($categories, isset($category->id) ? $category->id : null),
            'pinnedPost' => BlogPost::findOne(11),
            'categories' => BlogCategory::find()->all()
        ]);
    }

    private function getMenuItems($categories, $activeId = null, $parent = null)
    {
        $menuItems = [];
        foreach ($categories as $category) {
            if ($category->parent_id === $parent) {
                $menuItems[$category->id] = [
                    'active' => $activeId === $category->id,
                    'label' => $category->title,
                    'url' => [
                        'world/index',
                        'url_path' => !empty($category->parent->slug) ? $category->parent->slug : $category->slug,
                        'url_path_2' => !empty($category->parent->slug) ? $category->slug : ''
                    ],
                    'items' => $this->getMenuItems($categories, $activeId, $category->id),
                ];
            }
        }
        return $menuItems;
    }

    private function getCategoryIds($categories, $categoryId, &$categoryIds = [])
    {
        foreach ($categories as $category) {
            if ($category->id == $categoryId) {
                $categoryIds[] = $category->id;
            } elseif ($category->parent_id == $categoryId) {
                $this->getCategoryIds($categories, $category->id, $categoryIds);
            }
        }
        return $categoryIds;
    }

}
