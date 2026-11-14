<?php

namespace common\models;

use common\components\DateTimeFormatter;
use common\components\multilocal\Local;
use Yii;
use yii\base\Exception;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\Migration;
use yii\db\Schema;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;

/**
 * Product Testimonials model
 *
 * @property integer $id
 * @property integer $product_id
 * @property integer $user_id
 * @property integer $rating
 * @property integer $old_rating
 * @property integer $status
 * @property string $text
 * @property int $size_match
 * @property mixed $created_at
 * @property integer $updated_at
 * @property mixed $votes
 * @property mixed $votes_up
 * @property mixed $votes_down
 * @property mixed text_en
 * @property mixed pros
 * @property string pros_en
 * @property mixed cons
 * @property string cons_en
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class Testimonials extends ActiveRecord
{
    const STATUS_NEW = 1;
    const STATUS_POSTED = 2;

    public $reCaptcha;

    public static function getDb()
    {
        return \Yii::$app->madwave_rpoducts;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {


        $table_name = 'product_testimonials';

        if (\Yii::$app->db->schema->getTableSchema($table_name) === null) {
            $migration = new Migration();
            $migration->createTable($table_name, [
                'id' => Schema::TYPE_PK,
                'product_id' => Schema::TYPE_INTEGER,
                'user_id' => Schema::TYPE_INTEGER,
                'rating' => Schema::TYPE_INTEGER,
                'old_rating' => Schema::TYPE_INTEGER,
                'votes' => Schema::TYPE_INTEGER,
                'votes_up' => Schema::TYPE_INTEGER,
                'votes_down' => Schema::TYPE_INTEGER,
                'size_match' => Schema::TYPE_INTEGER,
                'text' => Schema::TYPE_TEXT,
                'text_eu' => Schema::TYPE_TEXT,
                'status' => Schema::TYPE_INTEGER,
                'created_at' => Schema::TYPE_INTEGER,
                'updated_at' => Schema::TYPE_INTEGER,
                'quantity' => Schema::TYPE_INTEGER,
                'pros' => Schema::TYPE_TEXT,
                'pros_eu' => Schema::TYPE_TEXT,
                'cons_eu' => Schema::TYPE_TEXT,
                'name' => Schema::TYPE_STRING,
                'email' => Schema::TYPE_STRING,
                'size' => Schema::TYPE_STRING,
                'reply' => Schema::TYPE_TEXT,
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
            [['reCaptcha'], \himiklab\yii2\recaptcha\ReCaptchaValidator::className()],

            [['rating', 'text', 'email', 'name', 'size'], 'required'],
            ['email', 'email'],
            [['id', 'status', 'size_match', 'quantity', 'name', 'pros', 'cons', 'size', 'reply'], 'default']
        ];
    }

    public function getSizeMatchLabel()
    {
        switch ($this->size_match) {
            case 1: $label = 'Слишком мал'; break;
            case 2: $label = 'Мал'; break;
            case 3: $label = 'Слегка мал'; break;
            case 4: $label = 'Идеально'; break;
            case 5: $label = 'Слега велик'; break;
            case 6: $label = 'Велик'; break;
            case 7: $label = 'Слишком Велик'; break;
            default: $label = 'не указано';
        }
        return $label;
    }

    public function getQualityLabel()
    {
        switch ($this->quantity) {
            case 1: $label = 'Очень низкое'; break;
            case 2: $label = 'Низкое'; break;
            case 3: $label = 'Ниже среднего'; break;
            case 4: $label = 'Среднее'; break;
            case 5: $label = 'Выше среднего'; break;
            case 6: $label = 'Высокое'; break;
            case 7: $label = 'Очень высокое'; break;
            default: $label = 'не указано';
        }
        return $label;
    }

    /**
     * @author Vyacheslav Elchin <lexscite@gmail.com>
     * @param Product $model
     * @return array|null|ActiveRecord
     */
    public static function getBest(Product $model)
    {
        return self::find()
            ->select('*, (`votes_up` + 1) / (`votes_down` + 1) as ordering')
            ->where(['product_id' => $model->getId()])
            ->andWhere(['status' => self::STATUS_POSTED])
            ->one();
    }

    public static function getDataProvider(Product $model) {
        $session = \Yii::$app->session;
        $session_key = 'testimonials';
        $session_access = $session->has($session_key) ? $session->get($session_key) : [];

        $query1 = Testimonials::find()
            ->select('*, (`votes_up` + 1) / (`votes_down` + 1) as ordering')
            ->where(['product_id' => $model->getId()])
            ->andWhere(['status' => Testimonials::STATUS_POSTED])
            ->orderBy('ordering DESC');

        if (count($session_access) > 0) {
            $query2 = Testimonials::find()->select('*, (`votes_up` + 1) / (`votes_down` + 1) as ordering')
                ->where(['product_id' => $model->getId()])
                ->andWhere(['id' => $session_access])
                ->andWhere(['status' => Testimonials::STATUS_NEW])
                ->orderBy('ordering DESC');
            $query1 = $query1->union($query2);
        }

        return new ActiveDataProvider([
            'query' => $query1,
            'pagination' => [
                'pageSize' => 5
            ]
        ]);
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'text' => 'Текст отзыва',
            'rating' => 'Оценка'
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public static function getStatuses() {
        return [
            self::STATUS_NEW => 'New',
            self::STATUS_POSTED => 'Posted'
        ];
    }

    public function vote($bool) {
        if (\Yii::$app->user->isGuest) {
            throw new BadRequestHttpException();
        }
        $user = \Yii::$app->user;
        $isVoteExist = ReviewVote::find()->where(['user_id' => $user->id])->andWhere(['review_id' => $this->id])->one();
        if ($isVoteExist) {
            if (($isVoteExist->value == 1 && $bool == false) || ($isVoteExist->value == 0 && $bool == true)) {
                if ($bool) {
                    $this->votes_up++;
                    $this->votes_down--;
                } else {
                    $this->votes_up--;
                    $this->votes_down++;
                }
            } else {
                return Json::encode([$this->votes_up, $this->votes_down]);
            }
        } else {
            $isVoteExist = new ReviewVote();
            if ($bool) {
                $this->votes_up++;
            } else {
                $this->votes_down++;
            }
        }
        $isVoteExist->review_id = $this->id;
        $isVoteExist->user_id = $user->id;
        $isVoteExist->value = $bool ? 1 : 0;
        if ($isVoteExist->save(false)) {
            $this->save(false);
        }
        return Json::encode([$this->votes_up == null ? 0 : $this->votes_up, $this->votes_down == null ? 0 : $this->votes_down]);
    }

    public function getFormField($form, $attribute, $labels = [])
    {
        $field = $form->field($this, $attribute)->radioList(
            [
                1 => 1,2 => 2,3 => 3,4 => 4,5 => 5,6 => 6,7 => 7
            ],
            [
                'class' => 'review-progress--form',
                'item' => function ($index, $label, $name, $checked, $value) {
                    return
                        '<div class="radio"><label>'
                        . Html::radio($name, $checked, ['value' => $value])
                        . '<span></span></label></div>';
                },
            ])->label(false);

        $caption = '';
        if (!empty($labels)) {
            $elements = '';

            if (is_array($labels)) {
                $count = count($labels);
                foreach ($labels as $i => $c) {
                    $additionalClass = null;
                    $i = $i + 1;
                    if ($i > 1 && $i < $count) {
                        $additionalClass = 'text-center';
                    }
                    if ($i == $count) {
                        $additionalClass = 'text-right';
                    }
                    $elements .= Html::tag('div', $c, ['class' => ('col-xs-6 '.$additionalClass)]);
                }
            }

            $caption = Html::tag('div', $elements, ['class' => 'row']);
        }

        return $field.$caption;
    }

    public function getColumns() {
        return [
            'id',
            [
                'label' => 'Продукт',
                'attribute' => 'product_id',
                'format' => 'raw',
                'value' => function($model) {
                    if ($product = Product::findOne($model->product_id)) {
                        return Html::a($product->name, $product->getUrl());
                    }
                }
            ],
            'email',
            [
                'label' => 'Статус',
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function($model) {
                    return $model::getStatuses()[$model->status];
                }
            ],
            [
                'format' => 'raw',
                'value' => function($model) {
                    return
                          '<b>Плюсы:</b> '.$model->pros . '<br><br>'
                        . '<b>Минусы:</b> '. $model->cons. '<br><br>'
                        . '<b>Комментарий:</b> '. $model->text;
                }
            ],
            'rating',
            [
                'label' => 'Дата',
                'attribute' => 'created_at',
                'format' => 'raw',
                'value' => function($model) {
                    return DateTimeFormatter::dateAsString($model->created_at);
                }
            ],
            [
                'format' => 'raw',
                'value' => function($model) {
                    return
                        Html::a('update', ['update', 'id' => $model->id])
                        .' '. Html::a('delete', ['delete', 'id' => $model->id])
                        ;
                }
            ]
        ];
    }

    public function formAttributes() {
        return [
            'status'            => ['type' => 'dropdown', 'items' => self::getStatuses()],
            'reply'                 => ['type' => 'textarea'],
            'reCaptcha' => ['type' => 'reCaptcha']
        ];
    }

    public function getExceptionsArray()
    {

    }

    public function getText()
    {
        if (Yii::$app->language == Local::RU) {
            return $this->text;
        }

        if (empty($this->text_en) && !empty($this->text)) {
            try {
                $translation = @Yii::$app->translate->translate('ru', 'en', $this->text);
                if (isset($translation['data']['translations'][0]['translatedText'])) {
                    $this->text_en = $translation['data']['translations'][0]['translatedText'];
                    $this->save(false);
                }
            } catch (Exception $e) {
                Yii::error($e->getMessage());
            }
        }
        return $this->text_en;
    }

    public function getPros()
    {
        if (Local::detectLocal() == Local::RU) {
            return $this->pros;
        }

        if (empty($this->pros_en) && !empty($this->pros)) {
            try {
                $translation = @Yii::$app->translate->translate('ru', 'en', $this->pros);
                if (isset($translation['data']['translations'][0]['translatedText'])) {
                    $this->pros_en = $translation['data']['translations'][0]['translatedText'];
                    $this->save(false);
                }
            } catch (Exception $e) {
                Yii::error($e->getMessage());
            }
        }
        return $this->pros_en;
    }

    public function getCons()
    {
        if (Local::detectLocal() == Local::RU) {
            return $this->cons;
        }

        if (empty($this->cons_en) && !empty($this->cons)) {
            try {
                $translation = @Yii::$app->translate->translate('ru', 'en', $this->cons);
                if (isset($translation['data']['translations'][0]['translatedText'])) {
                    $this->cons_en = $translation['data']['translations'][0]['translatedText'];
                    $this->save(false);
                }
            } catch (Exception $e) {
                Yii::error($e->getMessage());
            }
        }
        return $this->cons_en;
    }



}