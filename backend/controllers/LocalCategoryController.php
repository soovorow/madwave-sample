<?php

namespace backend\controllers;

use backend\models\LocalCategorySearch;
use common\models\LocalCategory;
use common\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class LocalCategoryController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => [
                            'index',
                            'view',
                            'create',
                            'update',
                            'delete',
                            'increase-weight',
                            'reduce-weight',
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

    public function beforeAction($action)
    {
        User::checkAccess(User::RIGHT_LEVEL_7);
        return parent::beforeAction($action);
    }

    /**
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LocalCategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, true);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionReduceWeight($id, $after = null) {

        if ($after == null) {
            // TODO error message
            return false;
        }


        $model_current = LocalCategory::findOne($id);
        $model_after = LocalCategory::findOne($after);

        $weight_current = $model_current->weight;
        $weight_after = $model_after->weight;

        $model_current->weight = $weight_after;
        $model_after->weight = $weight_current;

        $model_current->save(false);
        $model_after->save(false);

        return $this->redirect('index');
    }

    public function actionIncreaseWeight($id, $before = null) {

        if ($before == null) {
            // TODO error message
            return false;
        }

        $model_current = LocalCategory::findOne($id);
        $model_before = LocalCategory::findOne($before);

        $weight_current = $model_current->weight;
        $weight_before = $model_before->weight;

        $model_current->weight = $weight_before;
        $model_before->weight = $weight_current;

        $model_current->save(false);
        $model_before->save(false);

        return $this->redirect('index');
    }

    /**
     * Creates a new Category model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param int $id id of the parent category
     * @return mixed
     */
    public function actionCreate($id = null)
    {
        $categories = LocalCategory::find()->all();
        $model = new LocalCategory();
        $model->parent_id = $id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->weight = $model->id;
            $model->save(false);
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'categories' => $categories,
            ]);
        }
    }

    /**
     * Updates an existing Category model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $categories = LocalCategory::find()->all();
        /** @var $model LocalCategory */
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->linked_categories = Json::encode($model->linked);


            $model->img = UploadedFile::getInstance($model, 'img');

            if ($model->img) {
                $model->uploadCover();
            }

            $model->save(false);

            Yii::$app->session->setFlash('success', 'Изменения успешно сохранены');
            return $this->redirect(['update', 'id'=>$model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'categories' => $categories,
            ]);
        }
    }

    /**
     * Deletes an existing Category model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LocalCategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LocalCategory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
