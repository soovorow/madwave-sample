<?php

namespace frontend\controllers;

use common\models\TheLook;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * The Looks Controller
 */
class LookController extends Controller
{
    /**
     * Lists all Looks models.
     * @param bool|string $slug
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionIndex($slug = false)
    {
        $looks = TheLook::find()->where(['status' => 1])->orderBy('weight DESC')->all();

        if ($slug === 'index') {
            return $this->redirect(['index', 'slug' => $looks[0]->slug]);
        }

        if (!$model = TheLook::findOne(['slug' => $slug])) {
            throw new NotFoundHttpException();
        }



        return $this->render('index', [
            'model' => $model,
            'looks' => $looks
        ]);
    }

    /**
     * Displays a single Contact model.
     * @param integer $id
     * @return mixed
     */
    public function actionAddLookToCart($id)
    {
//        return $this->render('view', [
//            'model' => $this->findModel($id),
//        ]);
    }
}
