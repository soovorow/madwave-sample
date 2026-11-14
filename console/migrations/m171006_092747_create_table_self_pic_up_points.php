<?php

use yii\db\Migration;

class m171006_092747_create_table_self_pic_up_points extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%self_pic_up_points}}', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'title' => $this->string(255)->notNull(),
            'city' => $this->text(),
            'metro_title' => $this->text(),
            'metro_color' => $this->text(),
            'address' => $this->text(),
            'working_hours' => $this->text(),
            'phone' => $this->text(),
            'is_pick_up_point' => $this->text(),
            'latitude' => $this->text(),
            'longitude' => $this->text(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%self_pic_up_points}}');
    }
}
