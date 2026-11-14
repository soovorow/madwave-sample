<?php

use yii\db\Migration;

class m171006_092747_create_table_collection extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%collection}}', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'slug' => $this->string(255),
            'slug_eu' => $this->string(255),
            'image' => $this->string(255),
            'subtitle' => $this->string(255),
            'subtitle_eu' => $this->string(255),
            'title' => $this->string(255),
            'title_eu' => $this->string(255),
            'description' => $this->string(255),
            'description_eu' => $this->string(255),
            'meta_title' => $this->string(255),
            'meta_title_eu' => $this->string(255),
            'meta_description' => $this->string(255),
            'meta_description_eu' => $this->string(255),
            'h1' => $this->string(255),
            'h1_eu' => $this->string(255),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%collection}}');
    }
}
