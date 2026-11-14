<?php

use yii\db\Migration;

class m171006_092747_create_table_pinned_items extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%pinned_items}}', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'background' => $this->text(),
            'icon' => $this->text(),
            'small_title' => $this->text(),
            'small_title_en' => $this->text()->notNull(),
            'title' => $this->text(),
            'title_en' => $this->text()->notNull(),
            'text' => $this->text(),
            'text_en' => $this->text()->notNull(),
            'link' => $this->text(),
            'status' => $this->integer(11),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%pinned_items}}');
    }
}
