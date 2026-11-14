<?php

namespace backend\controllers;

use common\models\Page;
use common\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Class PageController
 *
 * @package backend\controllers
 *
 * @author Vyacheslav Elchin <lexscite@gmail.com>
 */
class PageController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'index',
                            'edit',
                            'create',
                            'delete',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        User::checkAccess(User::RIGHT_LEVEL_5);
        return parent::beforeAction($action);
    }

    /**
     * Displays index page with list of all Pages
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
           'query' => Page::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays detail page with new Page to create and saves it
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $page = new Page();

        if ($page->load(Yii::$app->request->post()) && $page->save()) {
            return $this->redirect(['/page/index']);
        } else {
            return $this->render('detail', [
                'page' => $page,
            ]);
        }
    }

    /**
     * Displays detail page of existing Page to edit
     *
     * @param $id integer Page id
     * @return string
     */
    public function actionEdit($id)
    {
        $page = Page::findOne($id);

        if ($page->load(Yii::$app->request->post()) && $page->save()) {
            return $this->redirect(['/page/index']);
        } else {
            return $this->render('detail', [
                'page' => $page,
            ]);
        }
    }

    /**
     * Removes specified Page from database
     *
     * @param $id integer Page id
     * @return \yii\web\Response
     */
    public function actionDelete($id)
    {
        Page::findOne($id)->delete();

        return $this->redirect(['/page/index']);
    }
}