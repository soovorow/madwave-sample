<?php

use yii\db\Migration;

class m171006_092747_create_table_product_testimonials extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%product_testimonials}}', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'product_id' => $this->integer(11),
            'user_id' => $this->integer(11),
            'rating' => $this->integer(11),
            'old_rating' => $this->integer(11),
            'votes' => $this->integer(11),
            'votes_up' => $this->integer(11),
            'votes_down' => $this->integer(11),
            'size' => $this->string(255),
            'size_match' => $this->integer(11),
            'text' => $this->text(),
            'reply' => $this->text(),
            'status' => $this->integer(11),
            'pros' => $this->text(),
            'cons' => $this->text(),
            'name' => $this->string(255),
            'email' => $this->string(255),
            'quantity' => $this->integer(11),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%product_testimonials}}');
    }
}
