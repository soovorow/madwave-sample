<?php

namespace backend\controllers;

use common\models\content\ProductLabel;
use common\models\User;
use Yii;

/**
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class ProductLabelController extends CRUDController {

    public $main_model = 'common\models\content\ProductLabel';
    public $title = 'Product labels';

    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        User::checkAccess(User::RIGHT_LEVEL_5);
        return parent::beforeAction($action);
    }

    public function actionUpdate($product_id)
    {
        if (!$model = ProductLabel::findOne(['product_id' => $product_id])) {
            $model = new ProductLabel(['product_id' => $product_id]);
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
