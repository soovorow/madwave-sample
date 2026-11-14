<?php

use yii\db\Migration;

class m171006_092747_create_table_feature extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%feature}}', [
            'feature_id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'product_id' => $this->integer(11)->notNull(),
            'img' => $this->text(),
            'title' => $this->text(),
            'description' => $this->text(),
            'content' => $this->text(),
            'title_ru' => $this->text(),
            'description_ru' => $this->text(),
            'content_ru' => $this->text(),
            'weight' => $this->integer(11),
            'status' => $this->integer(11),
        ], $tableOptions);

        $this->createIndex('feature_id', '{{%feature}}', 'feature_id', true);
        $this->createIndex('feature_id_2', '{{%feature}}', ['feature_id','product_id'], true);
        $this->createIndex('feature_id_3', '{{%feature}}', ['feature_id','product_id'], true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%feature}}');
    }
}
