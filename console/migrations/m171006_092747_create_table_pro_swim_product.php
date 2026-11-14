<?php

use yii\db\Migration;

class m171006_092747_create_table_pro_swim_product extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%pro_swim_product}}', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'title' => $this->string(255),
            'url' => $this->string(255),
            'price' => $this->string(255),
            'old_price' => $this->string(255),
            'rating' => $this->string(255),
            'votes' => $this->string(255),
            'category' => $this->string(255),
            'category_url' => $this->string(255),
            'parent_category' => $this->string(255),
            'parent_url' => $this->string(255),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%pro_swim_product}}');
    }
}
