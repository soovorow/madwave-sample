<?php

namespace frontend\models\user;

use yii\base\Model;
use common\models\User;

/**
 * Password reset request form
 * @author Dmitry Suvorov <soovorow@gmail.com>
 *
 * @property string email
 */
class PasswordResetRequestForm extends Model
{
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\common\models\User',
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => 'Пользователь с таким e-mail адресом не зарегистрирован.'
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne([
            'email' => $this->email,
        ]);

        if (!$user) {
            return false;
        }

        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
            if (!$user->save(false)) {
                return false;
            }
        }

        $message = \Yii::$app->mailer
            ->compose('passwordResetToken-html', ['user' => $user])
            ->setTo($this->email)
            ->setFrom(\Yii::$app->params['adminEmail'])
            ->setSubject('Восстановление пароля от сайта Mad Wave')
            ->send();

        return $message;
    }
}