<?php

use yii\db\Migration;

class m171006_092747_create_table_auth_user_data extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%auth_user_data}}', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'auth_id' => $this->integer(11),
            'email' => $this->string(255),
            'first_name' => $this->string(255),
            'last_name' => $this->string(255),
            'sex' => $this->string(255),
            'bdate' => $this->string(255),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%auth_user_data}}');
    }
}
