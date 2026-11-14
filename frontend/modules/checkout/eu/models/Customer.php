<?php

namespace frontend\modules\checkout\eu\models;

use common\models\User;
use common\models\UserData;
use frontend\modules\citydetector\CityDetector;
use yii\base\Model;

/**
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class Customer extends Model
{
    public $city;
    public $country;
    public $firstName;
    public $lastName;
    public $secondName;
    public $phone;
    public $email;

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $session = \Yii::$app->session;

        if ($session->has(Checkout::SESSION_KEY)) {
            $checkout = $session->get(Checkout::SESSION_KEY);
            if (isset($checkout['_customer'])) {
                /** @var $s Customer */
                $s = $checkout['_customer'];
                foreach ($s->attributes as $a => $v) {
                    if ($a !== 'city') {
                        $this->$a = $v;
                    }
                }
            }
        }

        if (!\Yii::$app->user->isGuest && $user = User::findOne(\Yii::$app->user->identity->getId())) {
            if ($userData = UserData::findOne(['user_id' => $user->id])) {
                $this->firstName = $userData->first_name;
                $this->phone = $userData->phone;
                $this->email = $user->email;
            }
        }
    }

    public function rules()
    {
        return [
            [
                [
                    'country',
                    'city',
                    'firstName',
                    'lastName',
                    'phone',
                    'email'
                ], 'required'],
                ['email', 'email'],
                [['email', 'lastName', 'firstName'], 'trim'],
                [['email', 'lastName', 'firstName'], 'filter', 'filter' => 'trim'
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'country' => 'Country *',
            'city' => 'City *',
            'firstName' => 'Name *',
            'lastName' => 'Last name *',
            'phone' => 'Phone *',
            'email' => 'E-mail *',
        ];
    }

    public function getCountry() {
        return $this->country;
    }

    public static function widget($model, $form)
    {
        return \Yii::$app->controller->view->render('checkout___customer', [
            'form' => $form,
            'model' => $model
        ]);
    }

}