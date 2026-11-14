<?php namespace common\components\screaming_crawl\models;

use common\components\screaming_crawl\ScreamingCrawl;
use common\models\LocalCategory;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Migration;
use yii\db\Schema;
use yii\helpers\Url;

class CrawlDiffs extends ActiveRecord {

    public static function tableName() {
        $table_name = 'diffs_title';
        if (Yii::$app->db->schema->getTableSchema($table_name) === null) {
            $migration = new Migration();
            $migration->createTable($table_name, [
                'id'                            => Schema::TYPE_PK,
                'CanonicalLinkElement1'         => Schema::TYPE_TEXT,
                'Title1'                        => Schema::TYPE_TEXT,
                'MetaDescription1'              => Schema::TYPE_TEXT,
                'MetaKeyword1'                  => Schema::TYPE_TEXT,
                'H11'                           => Schema::TYPE_TEXT,
            ]);
        }
        return $table_name;
    }

    /**
     * @param $category LocalCategory
     * @return bool
     */
    public static function checkUpdate($url, $category)
    {
        $diff = CrawlDiffs::find()->where(['CanonicalLinkElement1' => ScreamingCrawl::cutUrl($url)])->one();

        if (!$diff) {
            return false;
        }

        if (!empty($diff->H11)) {
            $category->h1 = $diff->H11;
        }

        if (!empty($diff->MetaDescription1)) {
            $category->meta_description = $diff->MetaDescription1;
        }

        if (!empty($diff->Title1)) {
            $category->meta_title = $diff->Title1;
        }

        if ($category->save(false)) {
            $diff->delete();
        }
    }

}