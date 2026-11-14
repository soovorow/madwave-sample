<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\bootstrap\Html;
use yii\db\ActiveRecord;
use yii\db\Migration;
use yii\db\Schema;
use yii\helpers\ArrayHelper;
use yii\imagine\Image;

/**
 * BlogPost model
 *
 * @property integer $id
 * @property integer $category_id
 * @property string $title
 * @property string $content
 * @property string $cover
 * @property integer $status
 * @property integer $views
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class BlogPost extends ActiveRecord
{

    const STATUS_HIDDEN = 0;
    const STATUS_POSTED = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {

        $table_name = 'blog_post';

        if (\Yii::$app->db->schema->getTableSchema($table_name) === null) {
            $migration = new Migration();
            $migration->createTable($table_name, [
                'id' => Schema::TYPE_PK,
                'category_id' => Schema::TYPE_INTEGER,
                'title' => Schema::TYPE_STRING,
                'slug' => Schema::TYPE_STRING,
                'unique_url_path' => Schema::TYPE_STRING,
                'content' => Schema::TYPE_TEXT,
                'cover' => Schema::TYPE_TEXT,
                'preview' => Schema::TYPE_TEXT,
                'views' => Schema::TYPE_INTEGER,
                'status' => Schema::TYPE_INTEGER,
                'meta_description' => Schema::TYPE_TEXT,
                'created_at' => Schema::TYPE_INTEGER,
                'updated_at' => Schema::TYPE_INTEGER,
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
            [
                [
                    'category_id',
                    'title',
                    'content',
                    'status',
                ],
                'required'
            ],
            [
                [
                    'preview',
                    'cover',
                    'slug',
                    'meta_description',
                    'unique_url_path',
                ],
                'default'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($this->isNewRecord && empty($this->slug)) {
            $this->slug = str_replace(' ', '-', self::transliterate($this->title));
        }
        return true;
    }

    /**
     * Get attributes for form
     *
     * Get attributes for form in admin area implementing custom not completed crud inheritance
     *
     * @return array
     */
    public function formAttributes()
    {
        return [
            'status' => [
                'type' => 'dropdown',
                'items' => [
                    self::STATUS_HIDDEN => 'Черновик',
                    self::STATUS_POSTED => 'Опубликовано'
                ],
            ],
            'title' => ['type' => 'textInput'],
            'meta_description' => ['type' => 'textInput'],
            'cover' => ['type' => 'textarea'],
            'preview' => ['type' => 'textarea'],
            'content' => ['type' => 'textarea'],
            'category_id' => ['type' => 'dropdown', 'items' => ArrayHelper::map(BlogCategory::find()->all(), 'id', 'title')],
            'slug' => ['type' => 'textInput'],
            'unique_url_path' => ['type' => 'textInput'],
        ];
    }

    /**
     * Get columns for index
     *
     * Get attributes for index page in admin area implementing custom not completed crud inheritance
     *
     * @return array
     */
    public function getColumns()
    {
        return [
            'id',
            [
                'attribute' => 'category_id',
                'format' => 'raw',
                'value' => function ($model) {
                    return BlogCategory::findOne(['id' => $model->category_id])->title;
                }
            ],
            'title',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->status == self::STATUS_POSTED ? 'Опубликовано' : 'Черновик';
                }
            ],
            'created_at:datetime',
            [
                'format' => 'raw',
                'value' => function ($model) {
                    return implode(' ', [
                        Html::a('update', ['update', 'id' => $model->id]),
                        Html::a('delete', ['delete', 'id' => $model->id], [
                            'data-confirm' => 'Вы уверены что хотите удалить '.$model->title.'? Действие нельзя будет отменить!'
                        ]),
                    ]);
                }
            ]
        ];
    }

    /**
     * Get post category
     *
     * @return BlogCategory
     */
    public function getCategory()
    {
        return BlogCategory::findOne(['id' => $this->category_id]);
    }

    /**
     * Get URL
     *
     * @return string
     */
    public function getUrl()
    {
        return Yii::$app->params['baseUrl'] . '/world' . (!empty($this->unique_url_path) ? $this->unique_url_path : '/') . $this->slug;
    }

    public function getCoverUrl($thumb = false)
    {
        preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', !empty($this->cover) ? $this->cover : $this->content, $matches);

        if (!empty($matches[1]) && $thumb) {
            $filePath = $matches[1];
            $filePathArray = array_reverse(explode('/', $filePath));
            $fileNameArray = explode('.', $filePathArray[0]);
            $fileName = $fileNameArray[0];
            $fileExt = $fileNameArray[1];
            $root = \Yii::getAlias('@stash');

            if (!file_exists($root.'/uploads/'. $fileName.'__thumb'.'.'.$fileExt)) {
                $originalFile = $root.'/uploads/'. $fileName.'.'.$fileExt;
                if (!file_exists($originalFile)) {
                    return 'http://placehold.it/64x64';
                }
                $imagineObj = Image::getImagine();
                $imageObj = $imagineObj->open($originalFile);
                $imageObj->resize($imageObj->getSize()->widen(120))->save($root.'/uploads/'. $fileName.'__thumb'.'.'.$fileExt);
            }
            return Yii::$app->params['baseUrl'].'/images/uploads/'.$fileName.'__thumb'.'.'.$fileExt;
        }

        return empty($matches[1]) ? 'http://placehold.it/64x64' : $matches[1];
    }

    public function getFirstImgUrl()
    {
        preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $this->content, $matches);
//        print_r($matches);
        return empty($matches[1]) ? '#' : $matches[1];
    }

    /**
     * Transliterate string
     * @author Dmitry Suvorov <soovorow@gmail.com>
     * @param null $textcyr
     * @param null $textlat
     * @return mixed|null
     */
    private static function transliterate($textcyr = null, $textlat = null)
    {
        $cyr = array(
            'ж', 'ы',  'ч',  'щ',   'ш',  'ю',  'а', 'б', 'в', 'г', 'д', 'е', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ъ', 'ь', 'я',
            'Ж', 'Ы',  'Ч',  'Щ',   'Ш',  'Ю',  'А', 'Б', 'В', 'Г', 'Д', 'Е', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ъ', 'Ь', 'Я');
        $lat = array(
            'zh', 'y', 'ch', 'sht', 'sh', 'yu', 'a', 'b', 'v', 'g', 'd', 'e', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'y', '', 'ya',
            'Zh', 'Y', 'Ch', 'Sht', 'Sh', 'Yu', 'A', 'B', 'V', 'G', 'D', 'E', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'c', 'Y', '', 'Ya');
        if($textcyr) return str_replace($cyr, $lat, $textcyr);
        else if($textlat) return str_replace($lat, $cyr, $textlat);
        else return null;
    }

}