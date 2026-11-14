<?php

namespace frontend\models\user;

use yii\base\Model;

/**
 * Change password form
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 *
 * @property string password
 * @property string new_password
 * @property string new_password_repeat
 */
class ChangePasswordForm extends Model
{
    public $password;
    public $new_password;
    public $new_password_repeat;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [
                [
                    'password',
                    'new_password',
                    'new_password_repeat',
                ],
                'required'
            ],
            ['password', 'validatePassword'],
            ['new_password_repeat', 'validateEquality']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'password' => 'Текущий пароль',
            'new_password' => 'Новый пароль',
            'new_password_repeat' => 'Повторите пароль',
        ];
    }


    public function changePassword() {
        $user = \Yii::$app->user->getIdentity();
        $user->setPassword($this->new_password);
        $user->save();
    }

    public function validatePassword() {
        $user = \Yii::$app->user->getIdentity();

        if (!$user || !$user->validatePassword($this->password)) {
            $this->addError('password', 'Неверный пароль.');
        }
    }

    public function validateEquality() {
        if ($this->new_password !== $this->new_password_repeat) {
            $this->addError('new_password');
            $this->addError('new_password_repeat', 'Пароли не совпадают');
        }
    }


    
}