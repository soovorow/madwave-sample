<?php

use yii\db\Migration;

class m171006_092747_create_table_file_watcher_product extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%file_watcher_product}}', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'brand' => $this->string(255),
            'title' => $this->string(255),
            'link' => $this->string(255),
            'dom_element' => $this->string(255),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'website' => $this->string(255),
            'compare_with' => $this->integer(11),
            'category' => $this->string(255),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%file_watcher_product}}');
    }
}
