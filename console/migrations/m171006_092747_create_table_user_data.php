<?php

use yii\db\Migration;

class m171006_092747_create_table_user_data extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user_data}}', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'user_id' => $this->string(255)->notNull(),
            'first_name' => $this->string(255),
            'last_name' => $this->string(255),
            'second_name' => $this->string(255),
            'phone' => $this->string(255),
            'city' => $this->text(),
            'client_card' => $this->text(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%user_data}}');
    }
}
