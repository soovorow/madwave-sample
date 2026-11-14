<?php

namespace frontend\models\user;

use common\models\User;
use yii\base\Model;
use Yii;

/**
 * Sign up form
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 *
 * @property string email
 * @property string password
 */
class SignupForm extends Model
{
    public $email;
    public $password;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => Yii::t('app', 'This email address is already registered on our site.')],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'password' => Yii::t('app', 'Create password'),
            'email' => 'E-mail',
        ];
    }

    /**
     * Signs user up.
     * @return User|null
     */
    public function signup()
    {
        if ($this->validate()) {

            $user = new User();
            $user->email = $this->email;
            $user->saveEmailInSession();
            $user->setPassword($this->password);
            $user->generateAuthKey();

            if ($user->save()) {
                return $user;
            };
        }
        return null;
    }

    public static function registerUserByEmail($email)
    {
        $user = new User();
        $user->email = $email;
        $user->generateAuthKey();
        $user->save();
        $PasswordResetRequestForm = new PasswordResetRequestForm();
        $PasswordResetRequestForm->email = $user->email;
        $PasswordResetRequestForm->sendEmail();
    }
}
