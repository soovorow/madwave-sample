<?php

namespace backend\controllers;

use common\models\ProductVariant;
use common\models\User;
use Yii;
use common\models\Product;
use backend\models\ProductSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['error', 'imperavi-image-upload'],
                        'allow' => true,
                    ],
                    [
                        'actions' => [
                            'index',
                            'view',
                            'create',
                            'update',
                            'delete',
                            'view-params',
                            'discounts',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        User::checkAccess(User::RIGHT_LEVEL_7);
        return parent::beforeAction($action);
    }

    public function actions()
    {
        return [
            'imperavi-image-upload' => [
                'class' => 'vova07\imperavi\actions\UploadAction',
                'url' => Yii::$app->homeUrl.'/images/uploads/', // Directory URL address, where files are stored.
                'path' => '@stash/uploads', // Or absolute path to directory where files are stored.
                'uploadOnlyImage' => false, // For not image-only uploading.
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save(false)) {
            return $this->redirect(['update', 'id' => $model->product_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionDiscounts($id) {
        $model = $this->findModel($id);
        if ($_POST) {
            $model->discount_card = $_POST['discount_card'];
            $model->discount_code = $_POST['discount_code'];
            $model->discount_card_percent = $_POST['discount_card_percent'];
            $model->save(false);
        }
        return $this->render('discounts', ['model' => $model]);
    }

    public function actionViewParams($id) {
        $model = Product::findOne($id);

        if ($_POST['product_id'] > 0) {
            $product = Product::findOne($_POST['product_id']);
            $product->status = $_POST['product_status'] == 1 ? 0 : 1;
            $product->save(false);
        }
        if (count($_POST['sku']) > 0) {
            foreach ($_POST['sku'] as $sku => $status) {
                $v = ProductVariant::find()->where(['variant_id' => $sku])->one();
                $v->status = $status == 1 ? 0 : 1;
                $v->save(false);
            }
        }

        if ($_POST) {
            return $this->redirect(['view-params', 'id' => $id]);
        }
        return $this->render('view-params', [
            'model' => $model,
            'variantsByColor' => $data = ProductVariant::find()
                ->where(['product_id' => $model->product_id])
                ->groupBy('color_id')
                ->limit(null)
                ->all()
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
