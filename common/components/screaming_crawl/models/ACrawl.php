<?php namespace common\components\screaming_crawl\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Migration;
use yii\db\Schema;

abstract class ACrawl extends ActiveRecord {

    abstract public function getTableName();

    protected static function createTable($table_name) {
        $migration = new Migration();
        $migration->createTable($table_name, [
            'id'                         => Schema::TYPE_PK,
            'Address'                    => Schema::TYPE_TEXT,
            'Content'                    => Schema::TYPE_TEXT,
            'StatusCode'                 => Schema::TYPE_TEXT,
            'Status'                     => Schema::TYPE_TEXT,
            'Title1'                     => Schema::TYPE_TEXT,
            'Title1Length'               => Schema::TYPE_TEXT,
            'Title1PixelWidth'           => Schema::TYPE_TEXT,
            'Title2'                     => Schema::TYPE_TEXT,
            'Title2Length'               => Schema::TYPE_TEXT,
            'Title2PixelWidth'           => Schema::TYPE_TEXT,
            'MetaDescription1'           => Schema::TYPE_TEXT,
            'MetaDescription1Length'     => Schema::TYPE_TEXT,
            'MetaDescription1PixelWidth' => Schema::TYPE_TEXT,
            'MetaKeyword1'               => Schema::TYPE_TEXT,
            'MetaKeywords1Length'        => Schema::TYPE_TEXT,
            'H11'                        => Schema::TYPE_TEXT,
            'H12'                        => Schema::TYPE_TEXT,
            'H11length'                  => Schema::TYPE_TEXT,
            'H12length'                  => Schema::TYPE_TEXT,
            'H21'                        => Schema::TYPE_TEXT,
            'H22'                        => Schema::TYPE_TEXT,
            'H21length'                  => Schema::TYPE_TEXT,
            'H22length'                  => Schema::TYPE_TEXT,
            'MetaRobots1'                => Schema::TYPE_TEXT,
            'MetaRefresh1'               => Schema::TYPE_TEXT,
            'CanonicalLinkElement1'      => Schema::TYPE_TEXT,
            'Size'                       => Schema::TYPE_TEXT,
            'WordCount'                  => Schema::TYPE_TEXT,
            'TextRatio'                  => Schema::TYPE_TEXT,
            'Level'                      => Schema::TYPE_TEXT,
            'Inlinks'                    => Schema::TYPE_TEXT,
            'Outlinks'                   => Schema::TYPE_TEXT,
            'ExternalOutlinks'           => Schema::TYPE_TEXT,
            'Hash'                       => Schema::TYPE_TEXT,
            'ResponseTime'               => Schema::TYPE_TEXT,
            'LastModified'               => Schema::TYPE_TEXT,
            'RedirectURI'                => Schema::TYPE_TEXT,
        ]);
    }
}