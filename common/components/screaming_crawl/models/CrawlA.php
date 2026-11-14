<?php namespace common\components\screaming_crawl\models;

use Yii;

class CrawlA extends ACrawl {

    public static function tableName() {
        $table_name = 'crawl_a';
        if (Yii::$app->db->schema->getTableSchema($table_name) === null) {
            parent::createTable($table_name);
        }
        return $table_name;
    }
    
    public function getTableName() {
        return 'crawl_a';
    }

}