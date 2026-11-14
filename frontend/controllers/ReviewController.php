<?php

namespace frontend\controllers;

use common\models\Product;
use common\models\Testimonials;
use yii\web\Controller;

/**
 * The Reviews Controller
 */
class ReviewController extends Controller
{
    public function actionCreate($product_id)
    {
        $model = new Testimonials([
            'product_id' => $product_id,
            'user_id' => \Yii::$app->user->isGuest ? '' : \Yii::$app->user->identity->getId()
        ]);

        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            if (!$model->isNewRecord) {
                $testimonialsForm = Testimonials::findOne($model->id);
                $testimonialsForm->old_rating = $testimonialsForm->rating;
                if (!$testimonialsForm->load(\Yii::$app->request->post()) && !$testimonialsForm->validate()) {
                    $testimonialsForm->save(false);
                }
            } else {
                $model->save(false);
                $model->status = $model::STATUS_NEW;
                $session = \Yii::$app->session;
                $session_key = 'testimonials';
                $reviews = $session->has($session_key) ? $session->get($session_key) : [];
                $reviews[] = $model->id;
                $session->set($session_key, $reviews);

                $product = Product::findOne($model->product_id);

                \Yii::$app->mailer
                    ->compose('review-html', ['review' => $model, 'product' => $product])
                    ->setFrom(\Yii::$app->params['adminEmail'])
                    ->setTo('servicemad@yandex.ru')
                    ->setSubject('Новый отзыв: оценка '.$model->rating.'; '.$product->name.'; '.$model->text)
                    ->send();
            }
        }

        return $this->goBack();
    }
}
