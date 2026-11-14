<?php

use yii\db\Migration;

class m171006_092747_create_table_category extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%category}}', [
            'weight' => $this->integer(11),
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'parent_id' => $this->integer(11),
            'linked_categories' => $this->text(),
            'title' => $this->string(255),
            'yandex_market_title' => $this->string(255),
            'slug' => $this->string(255),
            'url_path' => $this->text(),
            'url_path_2' => $this->text(),
            'url_path_3' => $this->text(),
            'url_path_4' => $this->text(),
            'cover' => $this->text(),
            'meta_title' => $this->text(),
            'meta_description' => $this->text(),
            'meta_canonical' => $this->text(),
            'h1' => $this->text(),
            'desc' => $this->text(),
            'hidden_in_menu' => $this->integer(11)->notNull(),
            'title_en' => $this->string(255),
            'meta_title_en' => $this->string(255),
            'h1_en' => $this->string(255),
            'desc_en' => $this->text(),
            'meta_desc_en' => $this->text(),
            'slug_en' => $this->string(255),
        ], $tableOptions);

        $this->addForeignKey('fk-category-parent_id-category-id', '{{%category}}', 'parent_id', '{{%category}}', 'id');
    }

    public function safeDown()
    {
        $this->dropTable('{{%category}}');
    }
}
