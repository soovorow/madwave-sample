<?php

namespace frontend\models\user;

use common\models\User;
use Yii;
use yii\base\Model;

/**
 * Change user email form
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 *
 * @property string email
 */
class ChangeEmailForm extends Model
{
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Извините, такой e-mail уже занят'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => 'Новый e-mail',
        ];
    }

    /**
     * Send confirmation email
     * @author Dmitry Suvorov <soovorow@gmail.com>
     * @return bool
     */
    public function sendConfirmation()
    {
        if (!$user = User::findOne(\Yii::$app->user->getIdentity()->getId())) {
            return false;
        }

        if (!User::isEmailChangeTokenValid($user->email_change_token)) {
            $user->generateEmailChangeToken();
            if (!$user->save()) {
                return false;
            }
        }

        $user->new_email = $this->email;
        $user->save(false);

        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'changeEmailToken-html'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['adminEmail']])
            ->setTo($user->email)
            ->setSubject('Изменение e-mail адреса на сайте Mad Wave')
            ->send();
    }
}