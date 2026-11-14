<?php

namespace frontend\controllers;

use Yii;
use common\models\Order;
use common\models\UserData;
use frontend\models\user\ChangeEmailForm;
use frontend\models\user\ChangePasswordForm;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\Controller;

/**
 * Controller for User Dashboard
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class AccountController extends Controller
{

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (\Yii::$app->user->isGuest) {
            $this->redirect(Url::to(['site/login']));
        }
        if (parent::beforeAction($action)) {
            Url::remember();
            return true;
        }
        return false;
    }

    /**
     * Displays profile settings page
     */
    public function actionProfile()
    {
        $user_data = UserData::find()->where(['user_id' => \Yii::$app->user->getIdentity()->id])->one();

        if (!$user_data) {
            $user_data = new UserData();
        }

        if ($user_data->load(Yii::$app->request->post()) && $user_data->validate()) {
            $user_data->user_id = \Yii::$app->user->getIdentity()->id;
            $user_data->save();
            return $this->refresh();
        }


        $change_password_form = new ChangePasswordForm();
        if ($change_password_form->load(Yii::$app->request->post()) && $change_password_form->validate()) {
            $change_password_form->changePassword();
            Yii::$app->session->setFlash('success', 'Пароль успешно изменен');
            return $this->refresh();
        }


        $change_email_form = new ChangeEmailForm();
        if ($change_email_form->load(Yii::$app->request->post()) && $change_email_form->validate()) {
            $change_email_form->sendConfirmation();
            Yii::$app->session->setFlash('success', 'На ваш текущий e-mail было отправлено сообщение для подверждения изменения вашего e-mail адреса.');
            return $this->refresh();
        }

        return $this->render('profile', [
            'user_data' => $user_data,
            'change_password_form' => $change_password_form,
            'change_email_form' => $change_email_form,
        ]);
    }

    /**
     * Displays all user orders
     *
     * @return string
     */
    public function actionOrders()
    {
        $user = \Yii::$app->user->getIdentity();

        return $this->render('orders', [
            'user' => $user,
            'ordersDataProvider' => new ActiveDataProvider([
                'query' => Order::find()->where(['customer_email' => $user->email])->orderBy('id DESC'),
                'pagination' => [
                    'pageSize' => 12,
                ],
            ])
        ]);
    }

    /**
     * Displays one order view
     *
     * @param $id integer Order Id
     * @return string
     */
    public function actionOrderDetails($id)
    {
        return $this->render('order-details', [
            'model' => Order::findOne($id)
        ]);
    }

    /**
     * Displays discounts & coupons page
     *
     * @return string
     */
    public function actionCoupons()
    {
        return $this->render('coupons', [
            'couponHistoryDataProvider' => new ActiveDataProvider([
                'query' => Order::find()
                    ->where(['customer_email' => \Yii::$app->user->identity->email])
                    ->andWhere(['<>', 'coupon', ''])
            ])
        ]);
    }

}
