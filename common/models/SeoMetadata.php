<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Migration;
use yii\helpers\Url;

/**
 * @property mixed title_ru
 * @property mixed title_en
 * @property mixed description_ru
 * @property mixed description_en
 * @property mixed image_url
 * @property mixed twitter_card
 * @property mixed og_type
 * @property mixed twitter_website
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class SeoMetadata extends ActiveRecord
{
    /**
     * @inheritdoc
     * @todo implement migration
     */
    public static function tableName()
    {
        $table_name = 'seo_metadata';
        if (Yii::$app->db->schema->getTableSchema($table_name) === null) {
            $migration = new Migration();
            $migration->createTable($table_name, [
                'id' => $migration->primaryKey(),
                'module' => $migration->string(),
                'controller' => $migration->string(),
                'action' => $migration->string(),
                'url' => $migration->string(),
                'title_ru' => $migration->string(),
                'title_en' => $migration->string(),
                'description_ru' => $migration->string(),
                'description_en' => $migration->string(),
                'image_url' => $migration->string(),
                'twitter_website' => $migration->string(),
                'twitter_card' => $migration->string(),
                'og_type' => $migration->string(),
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
            [
                [
                    'module',
                    'controller',
                    'action',
                    'url',
                    'title',
                    'description',
                    'image_url',
                    'twitter_website',
                    'twitter_creator',
                    'twitter_card',
                    'og_site_name',
                    'og_type',
                ],
                'default'],
        ];
    }

    public function register()
    {
        $view = Yii::$app->controller->view;

        if ($title = $this->getTitle()) {
            $view->title = $title;
            $view->registerMetaTag(['name' => 'twitter:title', 'content'  => $title]);
            $view->registerMetaTag(['property' => 'og:title', 'content'  => $title]);
        };

        if ($description = $this->getDescription()) {
            $view->registerMetaTag(['name' => 'description', 'content'  => $description]);
            $view->registerMetaTag(['name' => 'twitter:description', 'content'  => $description]);
            $view->registerMetaTag(['property' => 'og:description', 'content'  => $description]);
        }

        if ($image_url = $this->image_url) {
            $view->registerMetaTag(['name' => 'twitter:image', 'content'  => $image_url]);
            $view->registerMetaTag(['property' => 'og:image', 'content'  => $image_url]);
        }

        if ($twitter_card = $this->twitter_card) {
            $view->registerMetaTag(['name' => 'twitter:card', 'content'  => $twitter_card]);
        }

        if ($twitter_website = $this->twitter_website) {
            $view->registerMetaTag(['name' => 'twitter:website', 'content'  => $twitter_website]);
        }

        if ($og_type = $this->og_type) {
            $view->registerMetaTag(['property' => 'og:type', 'content'  => $og_type]);
        }

        $view->registerMetaTag(['property' => 'og:url', 'content'  => Url::canonical()]);
    }

    public function getTitle()
    {
        return $this->title_ru;
    }

    public function getDescription()
    {
        return $this->description_ru;
    }
}
