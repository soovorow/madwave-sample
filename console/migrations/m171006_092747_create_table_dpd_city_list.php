<?php

use yii\db\Migration;

class m171006_092747_create_table_dpd_city_list extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%dpd_city_list}}', [
            'abbreviation' => $this->string(40)->notNull(),
            'cityCode' => $this->string(20)->notNull(),
            'cityId' => $this->string(20)->notNull()->append('PRIMARY KEY'),
            'cityName' => $this->string(65)->notNull(),
            'countryCode' => $this->string(10)->notNull(),
            'countryName' => $this->string(65)->notNull(),
            'indexMax' => $this->string(20)->notNull(),
            'indexMin' => $this->string(20)->notNull(),
            'regionCode' => $this->string(20)->notNull(),
            'regionName' => $this->text()->notNull(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%dpd_city_list}}');
    }
}
