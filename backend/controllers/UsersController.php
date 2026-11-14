<?php
namespace backend\controllers;

use common\models\purchasing\Order;
use common\models\showcase\ShowcaseItem;
use common\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

/**
 * Users controller
 */
class UsersController extends Controller
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
                        'actions'  => [
                            'index',
                            'view',
                            'delete',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        User::checkAccess(User::RIGHT_LEVEL_9);
        return parent::beforeAction($action);
    }

    /**
     * Orders page
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index', [
            'dataProvider' => new ActiveDataProvider([
                'query' => User::find(),
                'pagination' => ['pageSize' => 20]
            ])
        ]);
    }

    /**
     * Order view page
     *
     * @param $id integer Order ID
     * @return string
     */
    public function actionView($id)
    {
        $model = User::findOne($id);

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->session->setFlash('success', 'saved');
        }

        return $this->render('view', [
            'model' => $model
        ]);
    }

    public function actionDelete($id)
    {
        $user = User::findOne($id);
        if ($user->rights === 777 || ShowcaseItem::find()->where(['creator_id' => $user->id])->count() > 0) {
            throw new ForbiddenHttpException();
        }
        $user->delete();
        return $this->redirect(['index']);
    }

}
