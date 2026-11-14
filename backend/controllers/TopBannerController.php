<?php
namespace backend\controllers;

use common\models\content\TopBanner;
use common\models\User;
use backend\models\ProductSearch;
use Yii;
use yii\web\NotFoundHttpException;

class TopBannerController extends CRUDController {

    public $main_model = 'common\models\content\TopBanner';
    public $title = 'Product Top Banner';

    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        User::checkAccess(User::RIGHT_LEVEL_5);
        return parent::beforeAction($action);
    }

    /**
     * Create page
     */
    public function actionCreate()
    {
        $model = new TopBanner();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->uploadCover();
            $model->save(false);
            $model->setSku($model->product_id_input);
            Yii::$app->session->setFlash('success', 'Collection page was created successful');
            return $this->redirect('index');
        }
        return $this->render('@backend/views/top-banner/create', [
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
            $model->setSku($model->product_id_input);
            Yii::$app->session->setFlash('success', 'Collection page was updated successful');
            return $this->redirect('index');
        }
        return $this->render('@backend/views/top-banner/update', [
            'model' => $model
        ]);
    }

    /**
     * Get model
     * @param $id
     * @return TopBanner
     * @throws NotFoundHttpException
     */
    private function getModel($id)
    {
        if (!$model = TopBanner::findOne($id)) {
            throw new NotFoundHttpException();
        }

        return $model;
    }

    public function actionSearch()
    {
        $key = Yii::$app->request->post('key');

        if (empty($key)) {
            return $this->renderPartial('@backend/views/top-banner/search-empty-state');
        }

        return $this->renderPartial('@backend/views/top-banner/search', [
            'productQuery' => (new ProductSearch())
                ->search(['ProductSearch' => ['key' => $key]], true)
                ->all()
            ,
        ]);
    }
}
