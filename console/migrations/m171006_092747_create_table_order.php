<?php

use yii\db\Migration;

class m171006_092747_create_table_order extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%order}}', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'website' => $this->string(5)->notNull(),
            'track_code' => $this->string(255),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'customer_lastname' => $this->string(255),
            'customer_firstname' => $this->string(255),
            'customer_secondname' => $this->string(255),
            'customer_phone' => $this->string(255),
            'delivery_method' => $this->text()->notNull(),
            'city' => $this->string(255),
            'post_index' => $this->string(255),
            'address' => $this->string(255),
            'delivery_price' => $this->double()->notNull(),
            'client_card' => $this->text(),
            'client_discount' => $this->double(),
            'primary_total' => $this->double()->notNull(),
            'total_discount' => $this->double(),
            'percent' => $this->double(),
            'coupon' => $this->text(),
            'coupon_discount' => $this->double(),
            'total' => $this->double()->notNull(),
            'payment_method' => $this->string(255)->notNull(),
            'customer_email' => $this->string(255),
            'customer_notes' => $this->text(),
            'status' => $this->string(255),
            'call_status' => $this->string(255),
            'test_status' => $this->integer(11),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%order}}');
    }
}
