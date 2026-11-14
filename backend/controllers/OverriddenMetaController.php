<?php namespace backend\controllers;

use common\models\content\OverriddenMeta;
use common\models\User;
use Yii;

class OverriddenMetaController extends CRUDController {

    public $main_model = 'common\models\content\OverriddenMeta';
    public $title = 'Overridden Meta';

    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        User::checkAccess(User::RIGHT_LEVEL_5);
        return parent::beforeAction($action);
    }

    public function actionUpdate($product_id)
    {
        if (!$model = OverriddenMeta::findOne(['product_id' => $product_id])) {
            $model = new OverriddenMeta(['product_id' => $product_id]);
        };

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Изменения успешно сохранены.');
            return $this->refresh();
        }

        return $this->render('update', [
            'model' => $model
        ]);
    }
}
