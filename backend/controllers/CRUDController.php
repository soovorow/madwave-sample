<?php namespace backend\controllers;

use backend\models\CRUDSearch;
use common\models\content\PinnedItems;
use vova07\imperavi\actions\GetAction;
use Yii;
use yii\db\ActiveRecord;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class CRUDController extends Controller
{

    /** @var $main_model ActiveRecord */
    public $main_model;

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

    public function getViewPath()
    {
        return Yii::getAlias('@backend/views/c-r-u-d');
    }

    public function hasMenu() {
        if ($this->getMenu()) {
            return true;
        }
        return false;
    }

    public function getMenu() {
        return null;
    }

    public function actions()
    {
        return [
            'images-get' => [
                'class' => 'vova07\imperavi\actions\GetAction',
                'url' => Yii::$app->homeUrl.'/images/uploads/',
                'path' => '@stash/uploads',
                'type' => GetAction::TYPE_IMAGES,
            ],
            'image-upload' => [
                'class' => 'vova07\imperavi\actions\UploadAction',
                'url' => Yii::$app->homeUrl.'/images/uploads/', // Directory URL address, where files are stored.
                'path' => '@stash/uploads', // Or absolute path to directory where files are stored.
            ]
        ];
    }

    public function actionIndex()
    {
        $searchModel = new CRUDSearch($this->main_model, []);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new $this->main_model;

        if ($model->load(Yii::$app->request->post())) {
            if ($model instanceof PinnedItems) {
                /**
                 * @var PinnedItems $last_posed_pinned_item
                 */
                $last_posed_pinned_item = PinnedItems::find()->orderBy('pos desc')->one();
                if ($last_posed_pinned_item) {
                    $model->pos = $last_posed_pinned_item->pos + 1;
                } else {
                    $model->pos = 1;
                }
            }
            if ($model->save()) {
                return $this->redirect(['update', 'id' => $model->id]);
            } else {
                return $this->render('create', ['model' => $model]);
            }
        } else {
            return $this->render('create', ['model' => $model]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (!$model) {
            $model = new $this->main_model;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Изменения успешно сохранены.');
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('update', ['model' => $model]);
        }
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        $model = $this->main_model;
        if (($model = $model::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
