<?php

use yii\db\Migration;

class m171006_092747_create_table_blog_post extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%blog_post}}', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'category_id' => $this->integer(11),
            'title' => $this->string(255),
            'slug' => $this->string(255),
            'unique_url_path' => $this->string(255),
            'content' => $this->text(),
            'cover' => $this->text(),
            'preview' => $this->text()->notNull(),
            'status' => $this->integer(11),
            'views' => $this->integer(11)->notNull(),
            'meta_description' => $this->text(),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%blog_post}}');
    }
}
