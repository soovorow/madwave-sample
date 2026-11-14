<?php

use yii\db\Migration;

class m171006_092747_create_table_product extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%product}}', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'title' => $this->string(255),
            'slug' => $this->string(255),
            'description' => $this->text(),
            'category_id' => $this->integer(11),
            'price' => $this->decimal(19,4),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%product}}');
    }
}
