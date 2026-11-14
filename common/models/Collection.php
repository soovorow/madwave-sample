<?php

namespace common\models;

use common\components\multilocal\Local;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Migration;
use yii\helpers\Json;
use yii\imagine\Image;
use yii\web\UploadedFile;

/**
 * Collection model
 *
 * @property integer $id
 * @property mixed meta_title
 * @property mixed meta_description
 * @property mixed subtitle
 * @property mixed title
 * @property mixed description
 * @property mixed h1
 * @property mixed variants
 * @property string image
 * @property mixed title_eu
 * @property mixed description_eu
 * @property mixed subtitle_eu
 * @property mixed meta_description_eu
 * @property mixed meta_title_eu
 * @property mixed h1_eu
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class Collection extends ActiveRecord
{
    public $sku_input;
    public $img;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {

        $table_name = 'collection';

        if (\Yii::$app->db->schema->getTableSchema($table_name) === null) {
            $migration = new Migration();
            $migration->createTable($table_name, [
                'id' => $migration->primaryKey(),

                'slug' => $migration->string(),
                'slug_eu' => $migration->string(),
                'image' => $migration->string(),
                'subtitle' => $migration->string(),
                'subtitle_eu' => $migration->string(),
                'title' => $migration->string(),
                'title_eu' => $migration->string(),
                'description' => $migration->string(),
                'description_eu' => $migration->string(),
                'meta_title' => $migration->string(),
                'meta_title_eu' => $migration->string(),
                'meta_description' => $migration->string(),
                'meta_description_eu' => $migration->string(),
                'h1' => $migration->string(),
                'h1_eu' => $migration->string(),

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
            ['slug','required'],
            ['slug_eu','required'],
            ['title','required'],
            ['title_eu','required'],
            ['subtitle','required'],
            ['subtitle_eu','required'],
            ['description','required'],
            ['description_eu','required'],
            ['meta_title','required'],
            ['meta_title_eu','required'],
            ['meta_description','required'],
            ['meta_description_eu','required'],
            ['h1','required'],
            ['h1_eu','required'],
            ['sku_input','default'],
            [['img'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
        ];
    }

    public function setSku($input)
    {
        CollectionItem::deleteAll(['collection_id' => $this->id]);
        if (!empty($input)) {
            foreach ($input as $variant_id => $bool) {
                if ($bool) {
                    $link = new CollectionItem();
                    $link->collection_id = $this->id;
                    $link->sku = $variant_id;
                    $link->save(false);
                }
            }
        }
        return $this;
    }

    public function getSku()
    {
        return $this->hasMany(CollectionItem::className(), ['collection_id' => 'id']);
    }

    public function getVariants()
    {
        return $this->hasMany(ProductVariant::className(), ['variant_id' => 'sku'])
            ->via('sku');
    }

    public function uploadCover()
    {
        $this->img = UploadedFile::getInstance($this, 'img');
        if ($this->img) {
            $file = 'uploads/' . md5($this->img->baseName.time()) . '.' . $this->img->extension;
            $rout = \Yii::getAlias('@stash') . '/' . $file;
            $this->img->saveAs($rout);
            $this->image = Json::encode($file);
            return $file;
        }
    }

    public function getTitle() {
        return Local::choose([
            Local::RU => $this->title,
            Local::EU => $this->title_eu,
        ]);
    }

    public function getDescription() {
        return Local::choose([
            Local::RU => $this->description,
            Local::EU => $this->description_eu,
        ]);
    }

    public function getSubTitle() {
        return Local::choose([
            Local::RU => $this->subtitle,
            Local::EU => $this->subtitle_eu,
        ]);
    }

    public function getMetaTitle() {
        return Local::choose([
            Local::RU => $this->meta_title,
            Local::EU => $this->meta_title_eu,
        ]);
    }

    public function getMetaDescription() {
        return Local::choose([
            Local::RU => $this->meta_description,
            Local::EU => $this->meta_description_eu,
        ]);
    }

    public function getH1() {
        return Local::choose([
            Local::RU => $this->h1,
            Local::EU => $this->h1_eu,
        ]);
    }

    /**
     * Get cover
     *
     * @param bool $url_only
     * @param bool $lg
     * @return string <img>
     * @internal param bool $sm
     */
    public function getCover($url_only = false, $lg = false)
    {
        if (!empty($this->image)) {
            $root = \Yii::getAlias('@frontend') . '/web/';
            $domain =  Yii::$app->params['baseUrl'] . '/';
            $filePath = Json::decode($this->image);
            $filePath = 'images/'.$filePath;
            $filePathArray = array_reverse(explode('/', $filePath));
            $fileNameArray = explode('.', $filePathArray[0]);
            $fileName = $fileNameArray[0];
            $fileExt = $fileNameArray[1];
            unset($filePathArray[0]);
            $filePath = implode('/', array_reverse($filePathArray));

            if (!$lg) {
                if (!file_exists($root.$filePath.'/'. $fileName.'__sm'.'.'.$fileExt)) {
                    $originalFile = $root.$filePath.'/'. $fileName.'.'.$fileExt;
                    if (file_exists($originalFile)) {
                        $imagineObj = Image::getImagine();
                        $imageObj = $imagineObj->open($originalFile);
                        $imageObj->resize($imageObj->getSize()->widen(858))->save($root.$filePath.'/'. $fileName.'__sm.'.$fileExt);
                    }
                }
                $fileName = $fileName.'__sm';
            };

            $url = $domain.$filePath.'/'.$fileName.'.'.$fileExt;

            if ($url_only) {
                return $url;
            }
            return '<img src="' . $url . '" width="100%" class="local-category-cover">';
        }
        return false;
    }
}