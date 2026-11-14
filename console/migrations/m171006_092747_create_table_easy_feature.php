<?php

use yii\db\Migration;

class m171006_092747_create_table_easy_feature extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%easy_feature}}', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'product_id' => $this->integer(11),
            'title' => $this->string(255),
            'description' => $this->string(255),
            'xpos' => $this->integer(11),
            'ypos' => $this->integer(11),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%easy_feature}}');
    }
}
