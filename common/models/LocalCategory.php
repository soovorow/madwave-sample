<?php

namespace common\models;

use common\components\multilocal\Local;
use common\components\screaming_crawl\models\CrawlDiffs;
use common\models\products\Category;
use Yii;
use yii\behaviors\SluggableBehavior;
use yii\bootstrap\Html;
use yii\db\ActiveRecord;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\imagine\Image;
use yii\web\View;


/**
 * Local Category model
 *
 * @property int $weight
 * @property int $id
 * @property int $parent_id
 * @property int google_taxanomy_key
 * @property string $linked_categories
 * @property string $title
 * @property string $slug
 * @property string $url_path
 * @property string $url_path_2
 * @property string $url_path_3
 * @property string $url_path_4
 * @property string $cover
 * @property string $meta_title
 * @property string $meta_description
 * @property string $h1
 * @property string $desc
 *
 * @property string title_en
 * @property string meta_title_en
 * @property string h1_en
 * @property string desc_en
 * @property string meta_desc_en
 *
 * @property array $linked
 * @property object $img
 * @property mixed slug_en
 * @property mixed hidden_in_menu
 * @property mixed parentCategory
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class LocalCategory extends ActiveRecord
{

    public $linked;
    public $img;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => SluggableBehavior::className(),
                'value' => function ($event) {
                    return strtolower(str_replace(' ', '-', self::transliterate($this->title)));
                }
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id'], 'default', 'value' => null],
            [['parent_id'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['linked', 'linked_categories'], 'default'],
            [['img'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
            [
                [

                    'meta_title',
                    'meta_description',
                    'meta_canonical',
                    'h1',
                    'desc',
                    'weight',
                    'url_path',
                    'url_path_2',
                    'url_path_3',
                    'url_path_4',
                    'hidden_in_menu',
                    'yandex_market_title',

                    'title_en',
                    'meta_title_en',
                    'h1_en',
                    'desc_en',
                    'meta_desc_en',
                    'slug_en',
                    'google_taxanomy_key',

                ], 'default'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'category_id' => 'ID',
            'parent_id' => 'Parent',
            'title' => 'Title',
            'slug' => 'Slug',
        ];
    }

    /**
     * Method for uploading cover image from form
     *
     * @return string
     */
    public function uploadCover()
    {
        $file = 'uploads/' . md5($this->img->baseName.time()) . '.' . $this->img->extension;
        $rout = \Yii::getAlias('@stash') . '/' . $file;
        $this->img->saveAs($rout);
        $this->cover = Json::encode($file);
        $this->save(false);
        return $file;
    }

    public function isStock() {
        return $this->id == 96;

    }

    /**
     * @return ActiveRecord|LocalCategory
     */
    public static function findBySlug($slug)
    {
        $slug_field = Local::choose([
            Local::RU => 'slug',
            Local::EU => 'slug_en',
        ]);

        return LocalCategory::find()->where([$slug_field => $slug])->one();
    }

    public function getSlug() {
        return Local::choose([
            Local::RU => $this->slug,
            Local::EU => $this->slug_en,
        ]);
    }

    /**
     * Get category Url
     *
     * @param bool $local
     * @return string
     */
    public function getUrl($local = false)
    {
        if (Yii::$app->language == Local::EU) {
            $urlPath = '/catalog/'.$this->getSlug();
            if ($local) {
                return $urlPath;
            }
            return Yii::$app->params['baseUrl'].$urlPath;
        }

        $url_path = [
            $this->url_path,
            $this->url_path_2,
            $this->url_path_3,
            $this->url_path_4,
        ];

        foreach ($url_path as $key => $part) {
            if (empty($part)) {
                unset($url_path[$key]);
            }
        }

        $path = implode('/', $url_path);

        $urlPath = '/catalog/' . $path;

        if ($this->id == 96) {
            $urlPath = '/stock';
        }

        if ($local) {
            return $urlPath;
        }

        return Yii::$app->params['baseUrl'] . $urlPath;
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
        if (!empty($this->cover)) {
            $root = \Yii::getAlias('@frontend') . '/web/';
            $domain =  Yii::$app->params['baseUrl'] . '/';
            $filePath = Json::decode($this->cover);
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

    public function getHeader() {
        if ($this->getCover(true)) {
            return '<img src="'.$this->getCover(true).'" alt="'.$this->title.'">';
        }
        return null;
    }

    /**
     * Get parent category
     *
     * @return LocalCategory
     */
    public function getParent()
    {
        return LocalCategory::findOne(['id' => $this->parent_id]);
    }

    public function getParentCategory()
    {
        return $this->hasOne(LocalCategory::className(), ['id' => 'parent_id']);
    }

        public function getGlobalCategories()
    {
        $linked_arr = Json::decode($this->linked_categories);
        return Category::findAll(['category_id' => $linked_arr]);
    }

    /**
     * Get child categories
     *
     * @return LocalCategory|\yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(LocalCategory::className(), ['parent_id' => 'id'])->orderBy('weight');
    }

    /**
     * Get category Ids
     *
     * @todo write descriptions for this method
     * @param $categories
     * @param $categoryId
     * @param $categoryIds
     * @return array
     */
    public static function getCategoryIds($categories, $categoryId, &$categoryIds = [])
    {
        foreach ($categories as $category) {
            if ($category->id == $categoryId) {
                $localCategories = Json::decode($category->linked_categories);
                if (!empty($localCategories)) {
                    foreach ($localCategories as $lc) {
                        $categoryIds[] = $lc;
                    }
                }
            } elseif ($category->parent_id == $categoryId) {
                self::getCategoryIds($categories, $category->id, $categoryIds);
            }
        }
        return $categoryIds;
    }


    /**
     * Render category catalog tree
     *
     * @return string html
     */
    public static function renderTree()
    {
        $categories = self::find()
            ->where(1)
            ->andWhere(['<', 'hidden_in_menu', 1])
            ->orderBy('weight')
            ->indexBy('id')
            ->all()
        ;

        if ($categories) {
            return self::buildTree($categories);
        }
        return null;
    }

    public function getTitle() {
        return Local::choose([
            Local::RU => $this->title,
            Local::EU => $this->title_en
        ]);
    }

    public function getMetaTitle() {
        $meta = Local::choose([
            Local::RU => $this->meta_title,
            Local::EU => $this->meta_title_en
        ]);
        return empty($meta) ? $this->getTitle() : $meta;
    }

    public function getH1()
    {
        $h1 = Local::choose([
            Local::RU => $this->h1,
            Local::EU => $this->h1_en
        ]);
        return empty($h1) ? $this->getMetaTitle() : $h1;
    }

    public function getMetaDesc()
    {
        return Local::choose([
            Local::RU => $this->meta_description,
            Local::EU => $this->meta_desc_en
        ]);
    }

    public function getDesc()
    {
        return Local::choose([
            Local::RU => $this->desc,
            Local::EU => $this->desc_en
        ]);
    }

    /**
     * @todo remove when table will be empty CrawlDiffs::checkUpdate()
     * @param $view View
     */
    public function registerMeta($view)
    {
        CrawlDiffs::checkUpdate(Url::canonical(), $this);

        $title = $this->getMetaTitle();

        $view->registerMetaTag(['name' => 'description', 'content'  => $this->getMetaDesc()]);
        $view->title = $title . ' | Mad Wave';

        if ($parentCategory = $this->getParent()) {
            $view->params['breadcrumbs'][] = ['label' => $parentCategory->getTitle(), 'url' => $parentCategory->getUrl()];
        }

        $view->params['breadcrumbs'][] = $this->getTitle();
    }

    public function getSortingSelect($productsDataProvider)
    {
        $sorts = [
            'popularity'  => \Yii::t('app', 'Popularity'),
            'by_rating'   => \Yii::t('app', 'Rating'),
            'by_discount' => \Yii::t('app', 'Discount'),
            'low_to_high' => \Yii::t('app', 'Lowest price first'),
            'high_to_low' => \Yii::t('app', 'Highest price first'),
        ];
        $request = \Yii::$app->request;
        $sort = Html::encode($request->get('sort'));
        $options = [];
        foreach ($sorts as $v => $label) {
            $selected = $sort == $v || $sort == '-' . $v;
            if ($v == 'popularity') {
                $selected = $selected || !$request->get('sort');
            }
            $options[] = Html::tag('option', $label, [
                'value' => $productsDataProvider->sort->createUrl($v),
                'selected' => $selected
            ]);
        }

        return Html::tag('select', implode($options), [
            'id' => 'sort-select',
            'class' => 'form-control rad0'
        ]);
    }

    private static function buildTree(array &$elements, $parentId = null)
    {
        foreach ($elements as $element)
        {
            /** @var $element LocalCategory */

            if ($element->parent_id == $parentId)
            {
                $element_id = $element->id;
                $has_childs = false;
                foreach ($elements as $e) {
                    if ($e->parent_id == $element->id) {
                        $has_childs = true;
                        break;
                    }
                }

                $class = 'item ';
                $prepand = '';

                if ($has_childs) {
                    $class .= 'open-childs ';
                }

                if ($element_id == 96) {
                    $class .= 'hidden-xs hidden-sm color-red ';
                    $prepand = '<i class="fa fa-tag"></i>';
                }

                if ($element_id == 1) {
                    $class .= 'hideBorderTop ';
                }

                echo '<li>';
                echo '<a href="' . $element->getUrl() . '" class="'.$class.'">' .$prepand. $element->getTitle() . '</a>';

                if ($has_childs) {
                    echo '<ul class="childs slide-content">';

                    $children = self::buildTree($elements, $element_id);
                    echo '</ul>';

                    echo '<div class="item-slide"><span class="caret"></span></div>';

                }
                echo '</li>';
                unset($elements[$element->id]);
            }
        }
    }

    /**
     * Render global categories tree
     *
     * Global categories tree for linking with local categories in admin area
     *
     * @return string html
     */
    public function renderLinkedCategoryTree($parent_id = null)
    {
        $categories = Category::find()->where(['parent_id' => $parent_id])->all();
        $linked_arr = \yii\helpers\Json::decode($this->linked_categories);

        if (empty($linked_arr)) {
            $linked_arr = [0];
        }

        if ($categories) {
            echo '<ul data-parent_id="' . $parent_id . '" style="list-style: none">';
            foreach ($categories as $c) {

                $lc = LocalCategory::find()->where(['like', 'linked_categories', '"' . $c->category_id . '"'])->one();

                $render_checkbox = false;
                if (count($c->categories) == 0) {
                    $render_checkbox = true;
                }

                $checked = '';
                if (in_array($c->category_id, $linked_arr)) {
                    $checked = 'checked';
                }

                echo '<li><label for="stash_category_' . $c->category_id . '" style="' . ($lc ? 'color:#ccc' : '') . '">';
                if ($render_checkbox) {
                    echo '<input ' . $checked . ' type="checkbox"  class="stash_category" name="LocalCategory[linked][]" value="' . $c->category_id . '" id="stash_category_' . $c->category_id . '">';
                }
                echo '<b> ' . $c->name_ru . '</b></label></li>';
                self::renderLinkedCategoryTree($c->category_id);
            };
            echo '</ul>';
        }
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
