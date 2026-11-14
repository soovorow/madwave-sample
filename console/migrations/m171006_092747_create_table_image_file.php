<?php

use yii\db\Migration;

class m171006_092747_create_table_image_file extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%image_file}}', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'note' => $this->string(255),
            'file_path' => $this->string(255)->notNull(),
            'url_path' => $this->string(255)->notNull(),
            'created_at' => $this->time()->notNull(),
            'updated_at' => $this->time()->notNull(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%image_file}}');
    }
}
