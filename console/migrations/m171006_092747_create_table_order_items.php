<?php

use yii\db\Migration;

class m171006_092747_create_table_order_items extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%order_items}}', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'order_id' => $this->integer(11)->notNull(),
            'name' => $this->text(),
            'quantity' => $this->integer(11)->notNull(),
            'vendore_code' => $this->text(),
            'size' => $this->text(),
            'color' => $this->text(),
            'clearPrice' => $this->double(),
            'clearPriceWDiscount' => $this->double()->notNull(),
            'sum' => $this->double(),
            'discount' => $this->double(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%order_items}}');
    }
}
