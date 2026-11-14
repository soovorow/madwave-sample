<?php

use yii\db\Migration;

class m171006_092747_create_table_price_watcher_products extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%price_watcher_products}}', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'compare_with' => $this->integer(11),
            'link' => $this->string(255),
            'brand' => $this->string(255),
            'category' => $this->string(255),
            'vendor_code' => $this->string(255),
            'title' => $this->string(255),
            'price' => $this->string(255),
            'discount' => $this->string(255),
            'availability' => $this->string(255),
            'size' => $this->string(255),
            'color' => $this->string(255),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%price_watcher_products}}');
    }
}
