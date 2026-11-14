<?php
namespace backend\controllers;

use backend\models\ProductSearch;
use common\models\TheLook;
use Nette\Utils\Json;
use Yii;
use yii\base\Exception;
use yii\base\InvalidParamException;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ShopTheLookController extends Controller
{
    public $enableCsrfValidation = false;

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
            'query' => TheLook::find()->orderBy('weight DESC')
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
        $model = new TheLook();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->uploadImage();
            $model->save(false);
            $model->setSku($model->sku_input);
            Yii::$app->session->setFlash('success', 'Collection page was created successful');
            return $this->redirect('index');
        }
        return $this->render('create', [
            'model' => $model
        ]);
    }

    public function actionWeight($first_id, $second_id)
    {
        $looks = TheLook::find()->indexBy('id')->all();

        if (!isset($looks[$first_id]) && !isset($looks[$second_id])) {
            throw new InvalidParamException();
        }

        $primaryModel = $looks[$first_id];
        $swapModel = $looks[$second_id];

        $swapModelWeight = $swapModel->weight;
        $primaryModelWeight = $primaryModel->weight;

        $primaryModel->weight = $swapModelWeight;
        $swapModel->weight = $primaryModelWeight;

        $primaryModel->save(false);
        $swapModel->save(false);

        return $this->redirect('index');
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
            $model->uploadImage();
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
     * @return TheLook
     * @throws NotFoundHttpException
     */
    private function getModel($id)
    {
        if (!$model = TheLook::findOne($id)) {
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