<?php

namespace backend\controllers;

use backend\models\ImageSearch;
use common\models\content\Image;

use common\models\User;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\UploadedFile;

class ImageController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'list',
                            'create',
                            'update',
                            'delete',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        User::checkAccess(User::RIGHT_LEVEL_5);
        return parent::beforeAction($action);
    }

    public function actionList()
    {
        $imageSearch = new ImageSearch();
        $dataProvider = $imageSearch->search(\Yii::$app->request->queryParams);
        return $this->render('list', [
            'searchModel' => $imageSearch,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $image = new Image();
        $this->view->title = 'Создать новую картинку';
        if ($image->load(\Yii::$app->request->post())) {
            $image->imageFile = UploadedFile::getInstance($image, 'imageFile');
            if ($image->validate()) {
                $image->upload();
                $image->save(false);
                return $this->redirect(['list']);
            }
        }
        return $this->render('detail', [
            'image' => $image,
        ]);
    }

    public function actionUpdate($id)
    {
        $image = Image::findOne($id);
        $this->view->title = 'Картинка №'.$image->primaryKey;
        if ($image->load(\Yii::$app->request->post())) {
            $image->imageFile = UploadedFile::getInstance($image, 'imageFile');
            if ($image->validate()) {
                if ($image->imageFile != null)
                    $image->deleteFile();
                $image->upload();
                $image->save(false);
                return $this->redirect(['list']);
            }
        }
        return $this->render('detail', [
            'image' => $image,
        ]);
    }

    public function actionDelete($id)
    {
        Image::findOne($id)->delete();
        return $this->redirect(['list']);
    }
}