<?php namespace common\components;

use common\models\Auth;
use common\models\AuthUserData;
use common\models\Log;
use common\models\User;
use Yii;
use yii\authclient\ClientInterface;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * AuthHandler handles successful authentication via Yii auth component
 */
class AuthHandler
{
    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function handle()
    {
        $attributes = $this->client->getUserAttributes();
        $email = self::fetchEmail($attributes);
        $id = ArrayHelper::getValue($attributes, 'id');
        $nickname = ArrayHelper::getValue($attributes, $id);

        /**
         * @var $auth Auth
         */

        $auth = Auth::find()->where(['source' => $this->client->getId(),'source_id' => $id])->one();

        if (Yii::$app->user->isGuest)
        {
            if ($auth) // login
            {
                $user = $auth->getUser();
                $this->updateUserInfo($user);
                Yii::$app->user->login($user, Yii::$app->params['user.rememberMeDuration']);
            }
            else // signup
            {
                $user = User::find()->where(['email' => $email])->one();

                if ($email !== null && $user) {
                    Yii::$app->user->login($user, Yii::$app->params['user.rememberMeDuration']);
                } elseif($id !== null && User::find()->where(['LIKE', 'username', '%twitter_' . $id])->exists()) {
                    $user = User::find()->where(['LIKE', 'username', '%twitter_' . $id])->one();
                    Yii::$app->user->login($user, Yii::$app->params['user.rememberMeDuration']);
                } else {
                    $password = Yii::$app->security->generateRandomString(6);
                    $user = new User([
                        'username' => empty($email) ? $id : $nickname,
                        'email' => $email,
                        'password' => $password,
                    ]);
                    $user->generateAuthKey();
                    $user->generatePasswordResetToken();

                    $transaction = User::getDb()->beginTransaction();

                    if ($user->save()) {
                        $auth = new Auth([
                            'user_id' => $user->id,
                            'source' => $this->client->getId(),
                            'source_id' => (string)$id,
                        ]);
                        if ($auth->save()) {
                            $transaction->commit();
                            Yii::$app->user->login($user, Yii::$app->params['user.rememberMeDuration']);
                        } else {
                            Yii::$app->getSession()->setFlash('error', [
                                Yii::t('app', 'Unable to save {client} account: {errors}', [
                                    'client' => $this->client->getTitle(),
                                    'errors' => json_encode($auth->getErrors()),
                                ]),
                            ]);
                        }
                    } else {
                        Yii::$app->getSession()->setFlash('error', [
                            Yii::t('app', 'Unable to save user: {errors}', [
                                'client' => $this->client->getTitle(),
                                'errors' => json_encode($user->getErrors()),
                            ]),
                        ]);
                    }
                }
            }
        } else { // user already logged in
            if (!$auth) { // add auth provider
                $auth = new Auth([
                    'user_id' => Yii::$app->user->id,
                    'source' => $this->client->getId(),
                    'source_id' => (string)$attributes['id'],
                ]);
                if ($auth->save()) {
                    /** @var User $user */
                    $user = $auth->user;
                    $this->updateUserInfo($user);
                    Yii::$app->getSession()->setFlash('success', [
                        Yii::t('app', 'Linked {client} account.', [
                            'client' => $this->client->getTitle()
                        ]),
                    ]);
                } else {
                    Yii::$app->getSession()->setFlash('error', [
                        Yii::t('app', 'Unable to link {client} account: {errors}', [
                            'client' => $this->client->getTitle(),
                            'errors' => json_encode($auth->getErrors()),
                        ]),
                    ]);
                }
            } else { // there's existing auth
                Yii::$app->getSession()->setFlash('error', [
                    Yii::t('app',
                        'Unable to link {client} account. There is another user using it.',
                        ['client' => $this->client->getTitle()]),
                ]);
            }
        }
    }

    private function fetchEmail($attributes) {
        $emails = ArrayHelper::getValue($attributes, 'emails');
        if ($emails) {
            $email = $emails[0]['value'];
        } else {
            $email = ArrayHelper::getValue($attributes, 'email');
        }
        return $email;
    }

    /**
     * @param User $user
     */
    private function updateUserInfo(User $user)
    {
        $attributes = $this->client->getUserAttributes();

        $auth =  Auth::findOne(['user_id' => $user->id]);

        $auth_data = AuthUserData::findOne(['auth_id' => $auth->id]);

        if (!$auth_data) {
            $auth_data = new AuthUserData();
            $auth_data->auth_id = $auth->id;
        }

        $auth_data->email = ArrayHelper::getValue($attributes, 'email');
        $auth_data->first_name = ArrayHelper::getValue($attributes, 'first_name');
        $auth_data->last_name = ArrayHelper::getValue($attributes, 'last_name');
        $auth_data->last_name = ArrayHelper::getValue($attributes, 'last_name');
        $auth_data->sex = ArrayHelper::getValue($attributes, 'sex');
        $auth_data->bdate = ArrayHelper::getValue($attributes, 'bdate');
        $auth_data->save(false);
    }
}