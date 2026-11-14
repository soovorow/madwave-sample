<?php namespace backend\controllers;

use common\models\content\PinnedItems;
use common\models\User;
use Yii;

class PinnedItemsController extends CRUDController {

    public $main_model = 'common\models\content\PinnedItems';
    public $title = 'Pinned Items';

    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        User::checkAccess(User::RIGHT_LEVEL_5);
        return parent::beforeAction($action);
    }

    public function actionDeleteIcon($id)
    {
        /**
         * @var PinnedItems
         */
        $model = $this->findModel($id);

        $file = Yii::getAlias('@stash').$model->icon;

        if ($model->updateAttributes(['icon' => null])) {
            unset($file);
            Yii::$app->session->addFlash('success', 'Icon was successfully deleted!');
        } else {
            Yii::$app->session->addFlash('error', 'Icon wasn\'t deleted. Something wrong happened');
        }

        return $this->redirect(['update', 'id' => $id]);
    }

    public function actionDeleteBackground($id)
    {
        /**
         * @var PinnedItems
         */
        $model = $this->findModel($id);

        $file = Yii::getAlias('@stash').$model->background;

        if ($model->updateAttributes(['background' => null])) {
            unset($file);
            Yii::$app->session->addFlash('success', 'Background was successfully deleted!');
        } else {
            Yii::$app->session->addFlash('error', 'Background wasn\'t deleted. Something wrong happened');
        }


        return $this->redirect(['update', 'id' => $id]);
    }

    public function actionMoveUp($id) {
        $moving = PinnedItems::findOne($id);
        $moving_pos = $moving->pos;
        /**
         * @var PinnedItems $prev
         */
        $prev = PinnedItems::find()->where(['<', 'pos', $moving_pos])->orderBy('pos desc')->one();
        if (!$prev) return $this->redirect('index');
        $prev_pos = $prev->pos;

        $moving->pos = $prev_pos;
        $prev->pos = $moving_pos;

        $moving->save(false);
        $prev->save(false);

        return $this->redirect('index');
    }

    public function actionMoveDown($id) {
        $moving = PinnedItems::findOne($id);
        $moving_pos = $moving->pos;
        /**
         * @var PinnedItems $next
         */
        $next = PinnedItems::find()->where(['>', 'pos', $moving_pos])->orderBy('pos')->one();
        if (!$next) return $this->redirect('index');
        $next_pos = $next->pos;

        $moving->pos = $next_pos;
        $next->pos = $moving_pos;

        $moving->save(false);
        $next->save(false);

        return $this->redirect('index');
    }
}
