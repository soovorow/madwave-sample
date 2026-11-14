<?php

use yii\db\Migration;

class m171006_092747_create_table_letter_ape_messages extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%letter_ape_messages}}', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'email' => $this->string(255),
            'title' => $this->text(),
            'body' => $this->text(),
            'status' => $this->integer(11),
            'opens' => $this->integer(11),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'campaign_id' => $this->integer(11),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%letter_ape_messages}}');
    }
}
