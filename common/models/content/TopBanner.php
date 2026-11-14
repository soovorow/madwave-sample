<?php
namespace common\models\content;

use common\components\multilocal\Local;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Migration;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\UploadedFile;

/**
 * Product Top Banner model
 *
 * @property string background
 * @property string video
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class TopBanner extends ActiveRecord
{
    const STATUS_POSTED = 1;

    public $background_file;
    public $product_id_input;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        $table_name = 'product_top_banner';

        if (\Yii::$app->db->schema->getTableSchema($table_name) === null) {
            $migration = new Migration();
            $migration->createTable($table_name, [
                'id' => $migration->primaryKey(),
                'background' => $migration->text(),
                'title_ru' => $migration->string(),
                'title_en' => $migration->string(),
                'text_ru' => $migration->text(),
                'text_en' => $migration->text(),
                'video' => $migration->text(),
                'video_eu' => $migration->text(),
                'status' => $migration->integer(),
                'created_at' => $migration->integer(),
                'updated_at' => $migration->integer(),
            ]);
        }

        return $table_name;
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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['title_ru', 'string'],
            ['title_ru', 'required'],

            ['title_en', 'string'],
            ['title_en', 'required'],

            ['text_ru', 'string'],
            ['text_ru', 'required'],

            ['text_en', 'string'],
            ['text_en', 'required'],

            ['video', 'string'],
            ['status', 'integer'],

            ['product_id_input', 'default'],
            ['video_eu', 'default'],


            ['background_file', 'file' ],
        ];
    }

    public function uploadCover()
    {
        $this->background_file = UploadedFile::getInstance($this, 'background_file');
        if ($this->background_file) {
            $file = '/uploads/' . md5($this->background_file->baseName.time()) . '.' . $this->background_file->extension;
            $rout = \Yii::getAlias('@stash') . $file;
            $this->background_file->saveAs($rout);
            $this->background = '/images'.$file;
            return $file;
        }
    }

    public function getProductIds()
    {
        return $this->hasMany(TopBannerProducts::className(), ['banner_id' => 'id']);
    }

    public function getColumns() {
        return [
            'id',
            'title_ru',
            'text_ru',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function($model) {
                    return $model->status == 0 ? 'Черновик' : 'Опубликовано';
                }
            ],
            [
                'format' => 'raw',
                'value' => function($model) {
                    return
                        Html::a('update', ['update', 'id' => $model->id])
                        .' '. Html::a('delete', ['delete', 'id' => $model->id]);
                }
            ]
        ];
    }

    public function setSku($input)
    {
        TopBannerProducts::deleteAll(['banner_id' => $this->id]);
        if (!empty($input)) {
            foreach ($input as $product_id => $bool) {
                if ($bool) {
                    $link = new TopBannerProducts();
                    $link->banner_id = $this->id;
                    $link->product_id = $product_id;
                    $link->save(false);
                }
            }
        }
        return $this;
    }

    public function formAttributes() {
        return [
            'status' => [
                'type' => 'dropdown',
                'items' => [
                    0 => 'Черновик',
                    1 => 'Опубликовано'
                ],
            ],
            'background_file' => ['type' => 'fileInput'],
            'title_ru' => ['type' => 'textInput'],
            'title_en' => ['type' => 'textInput'],
            'text_ru' => ['type' => 'textInput'],
            'text_en' => ['type' => 'textInput'],
            'video' => ['type' => 'textInput'],
            'video_eu' => ['type' => 'textInput'],
        ];
    }

    public static function widget()
    {
        $banners = HomepageBanner::find()
            ->where(['status' => self::STATUS_POSTED])
            ->andWhere([Local::choose([
                Local::RU => 'enable_ru',
                Local::EU => 'enable_eu'
            ]) => 1])
            ->orderBy('id DESC')
            ->indexBy('id')
            ->all();

        if (count($banners) > 0) {
            $items = [];

            foreach ($banners as $i => $b) {
                /** @var $b HomepageBanner */

                $content = [
                    Html::tag('h3', $b->getSmallTitle() ),
                    Html::tag('h2', $b->getTitle(), ['class' => 'h1']),
                    Html::tag('p', $b->getText()),
                    Html::a(Yii::t('app', 'More').' <i class="fa fa-chevron-right"></i>', $b->getLink(), [
                        'class' => 'btn btn-lg btn-danger'
                    ])
                ];

                $container = Html::tag('div', implode($content), ['style' => $b->style, 'class' => 'container']);

                $items[] = Html::tag('div', $container, [
                    'style' => 'background: url(\''.$b->background.'\') center center/cover no-repeat',
                    'class' => 'banner'
                ]);
            }

            return Html::tag('section', implode($items), ['id' => 'main-banner']);
        }

        return null;
    }

    public function getTitle() {
        return Local::choose([
            Local::RU => $this->title_ru,
            Local::EU => $this->title_en,
        ]);
    }
    public function getText() {
        return Local::choose([
            Local::RU => $this->text_ru,
            Local::EU => $this->text_en,
        ]);
    }
    public function getVideo() {
        $v = Local::choose([
            Local::RU => $this->video,
            Local::EU => $this->video_eu,
        ]);
        return $v ? $v : null;
    }
}

