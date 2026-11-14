<?php

namespace backend\controllers;

use backend\models\FeatureSearch;
use common\models\Feature;
use common\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * Class EasyFeaturesController is controller for EasyFeature admin controls part
 *
 * @package backend\controllers
 *
 * @author Vyacheslav Elchin <lexscite@gmail.com>
 */
class EasyFeaturesController extends Controller
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
                            'view',
                            'create',
                            'update',
                            'delete',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
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
     * Displays list of all features
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new FeatureSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays new feature creation page
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Feature();

        if ($model->load(Yii::$app->request->post()) && $model->save(true)) {
            return $this->redirect(['view', 'id' => $model->primaryKey]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays existing feature details page
     *
     * @param $id
     * @return string
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => Feature::findOne($id),
        ]);
    }

    /**
     * Displays existing feature editing page
     *
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionUpdate($id)
    {
        $model = Feature::findOne($id);

        if ($model->load(Yii::$app->request->post()) && $model->save(true)) {
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Removes existing feature
     *
     * @param $id
     * @return \yii\web\Response
     */
    public function actionDelete($id)
    {
        Feature::findOne($id)->delete();

        return $this->redirect(['index']);
    }
}