<?php
namespace frontend\modules\checkout\ru\models;

use common\models\User;
use common\models\UserData;
use frontend\modules\citydetector\CityDetector;
use yii\base\Model;

/**
 * Customer data model
 *
 * @property string city
 * @property string firstName
 * @property string lastName
 * @property string secondName
 * @property string phone
 * @property string email
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class Customer extends Model
{
    public $city;
    public $firstName;
    public $lastName;
    public $secondName;
    public $phone;
    public $email;

    /**
     * @inheritdoc
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->city = CityDetector::getCity();

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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['firstName', 'string', 'max' => 35],
            ['firstName', 'trim'],
            ['firstName', 'required'],

            ['lastName', 'string', 'max' => 35],
            ['lastName', 'trim'],
            ['lastName', 'required'],

            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'trim'],
            ['email', 'required'],

            ['phone', 'string'],
            ['phone', 'required'],

            [
                [
                    'city',
                    'lastName',
                    'email'
                ], 'required'
            ],
            ['secondName', 'default'],

            [['lastName', 'secondName'], 'trim'],
            [['lastName', 'secondName'], 'filter', 'filter' => 'trim'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'city' => 'Город',
            'firstName' => 'Имя *',
            'lastName' => 'Фамилия *',
            'secondName' => 'Отчество *',
            'phone' => 'Контактный телефон *',
            'email' => 'E-mail *',
        ];
    }

}