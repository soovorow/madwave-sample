<?php
namespace backend\controllers;

use backend\models\ProductSearch;
use common\models\Collection;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class CollectionsController extends Controller
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
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * List page
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Collection::find()
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Create page
     */
    public function actionCreate()
    {
        $model = new Collection();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->uploadCover();
            $model->save(false);
            $model->setSku($model->sku_input);
            Yii::$app->session->setFlash('success', 'Collection page was created successful');
            return $this->redirect('index');
        }
        return $this->render('create', [
            'model' => $model
        ]);
    }

    /**
     * Update page
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionUpdate($id)
    {
        $model = $this->getModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->uploadCover();
            $model->save(false);
            $model->setSku($model->sku_input);
            Yii::$app->session->setFlash('success', 'Collection page was updated successful');
            return $this->redirect('index');
        }
        return $this->render('update', [
            'model' => $model
        ]);
    }

    /**
     * Delete action
     * @param $id
     * @return \yii\web\Response
     */
    public function actionDelete($id)
    {
        $model = $this->getModel($id);
        if ($model->delete()) {
            Yii::$app->session->setFlash('success', 'Collection page was deleted successful');
        };
        return $this->redirect('index');
    }

    /**
     * Get model
     * @param $id
     * @return Collection
     * @throws NotFoundHttpException
     */
    private function getModel($id)
    {
        if (!$model = Collection::findOne($id)) {
            throw new NotFoundHttpException();
        }

        return $model;
    }

    public function actionSearch()
    {
        $key = Yii::$app->request->post('key');

        if (empty($key)) {
            return $this->renderPartial('search-empty-state');
        }

        return $this->renderPartial('search', [
            'productQuery' => (new ProductSearch())
                ->search(['ProductSearch' => ['key' => $key]], true)
                ->all()
            ,
        ]);
    }
}