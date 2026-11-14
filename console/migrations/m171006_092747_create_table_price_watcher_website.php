<?php

use yii\db\Migration;

class m171006_092747_create_table_price_watcher_website extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%price_watcher_website}}', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'domain' => $this->string(255),
            'price_selector' => $this->string(255),
            'discount_selector' => $this->string(255),
            'availability_selector' => $this->string(255),
            'favicon' => $this->text(),
            'status' => $this->integer(11),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'color_selector' => $this->string(255),
            'size_selector' => $this->string(255),
            'title_selector' => $this->string(255),
            'brand_selector' => $this->string(255),
            'category_selector' => $this->string(255),
            'vendor_code_selector' => $this->string(255),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%price_watcher_website}}');
    }
}
