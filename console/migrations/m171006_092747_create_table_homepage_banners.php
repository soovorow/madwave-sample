<?php

use yii\db\Migration;

class m171006_092747_create_table_homepage_banners extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%homepage_banners}}', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'background' => $this->text(),
            'small_title' => $this->text(),
            'small_title_en' => $this->text(),
            'title' => $this->text(),
            'title_en' => $this->text(),
            'text' => $this->text(),
            'text_en' => $this->text(),
            'link' => $this->text(),
            'link_eu' => $this->text()->notNull(),
            'style' => $this->text(),
            'status' => $this->integer(11),
            'enable_ru' => $this->integer(11),
            'enable_eu' => $this->integer(11),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%homepage_banners}}');
    }
}
