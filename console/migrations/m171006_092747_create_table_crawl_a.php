<?php

use yii\db\Migration;

class m171006_092747_create_table_crawl_a extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%crawl_a}}', [
            'sstatus' => $this->integer(11),
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'Address' => $this->text(),
            'Content' => $this->text(),
            'StatusCode' => $this->text(),
            'Status' => $this->text(),
            'Title1' => $this->text(),
            'Title1Length' => $this->text(),
            'Title1PixelWidth' => $this->text(),
            'Title2' => $this->text(),
            'Title2Length' => $this->text(),
            'Title2PixelWidth' => $this->text(),
            'MetaDescription1' => $this->text(),
            'MetaDescription1Length' => $this->text(),
            'MetaDescription1PixelWidth' => $this->text(),
            'MetaKeyword1' => $this->text(),
            'MetaKeywords1Length' => $this->text(),
            'H11' => $this->text(),
            'H12' => $this->text(),
            'H11length' => $this->text(),
            'H12length' => $this->text(),
            'H21' => $this->text(),
            'H22' => $this->text(),
            'H21length' => $this->text(),
            'H22length' => $this->text(),
            'MetaRobots1' => $this->text(),
            'MetaRefresh1' => $this->text(),
            'CanonicalLinkElement1' => $this->text(),
            'Size' => $this->text(),
            'WordCount' => $this->text(),
            'TextRatio' => $this->text(),
            'Level' => $this->text(),
            'Inlinks' => $this->text(),
            'Outlinks' => $this->text(),
            'ExternalOutlinks' => $this->text(),
            'Hash' => $this->text(),
            'ResponseTime' => $this->text(),
            'LastModified' => $this->text(),
            'RedirectURI' => $this->text(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%crawl_a}}');
    }
}
