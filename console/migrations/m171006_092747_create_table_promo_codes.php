<?php

use yii\db\Migration;

class m171006_092747_create_table_promo_codes extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%promo_codes}}', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'title' => $this->text()->notNull(),
            'code' => $this->text()->notNull(),
            'n_usage' => $this->integer(11),
            'was_used' => $this->integer(11),
            'type' => $this->integer(11),
            'value' => $this->text(),
            'group' => $this->text(),
            'excluding_type' => $this->integer(11),
            'allow_with_card' => $this->integer(11)->notNull(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%promo_codes}}');
    }
}
