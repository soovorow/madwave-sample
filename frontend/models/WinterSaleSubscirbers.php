<?php

namespace frontend\models;

use common\components\MailChimp;
use yii\base\Model;
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
class WinterSaleSubscirbers extends Model
{
    public $email;

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
        $mc->addInterest($this->email, 'NEW YEAR\'S SALE', true);
    }

}
