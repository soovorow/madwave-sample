<?php

use yii\db\Migration;

class m171006_092747_create_table_a1 extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%a1}}', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'firstname' => $this->string(255),
            'lastname' => $this->string(255),
            'card' => $this->string(255),
            'email' => $this->string(255),
            'phone' => $this->string(255),
            'sport' => $this->string(255),
            'rank' => $this->string(255),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%a1}}');
    }
}
