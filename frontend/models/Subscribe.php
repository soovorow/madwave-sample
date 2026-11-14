<?php

namespace frontend\models;

use common\components\MailChimp;
use yii\db\ActiveRecord;
use yii\db\Migration;
use yii\db\Schema;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * Footer subscription form
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 *
 * @property integer $id
 * @property string $email
 */
class Subscribe extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        $table_name = 'subscribe';

        if (\Yii::$app->db->schema->getTableSchema($table_name) === null) {
            $migration = new Migration();
            $migration->createTable($table_name, [
                'id'            => Schema::TYPE_PK,
                'email'    => Schema::TYPE_STRING,
            ]);
        }

        return $table_name;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            [['email'], 'email'],
        ];
    }

    public function submit()
    {
        $mc = new MailChimp();
        $mc::setUserEmail($this->email);
        $mc->addListMember($this->email);
        $mc->addInterest($this->email, 'footer', true);
        $this->save(false);
    }

    public static function widget() {
        $subscribe_model = new Subscribe();

        echo '<section id="subscribe">';
        echo '<div class="container p0">';
        echo \Yii::t('app', 'Subscribe to newsletter');
        $form = ActiveForm::begin(['action' => Url::to(['/site/mail-scribe'])]);
        echo $form->field($subscribe_model, 'email')->textInput(['placeholder' => \Yii::t('app', 'Your e-mail'), 'class' => ''])->label(false);
        echo Html::submitInput(\Yii::t('app', 'Subscribe'));
        $form::end();
        echo '</div>';
        echo '</section>';
    }

}
