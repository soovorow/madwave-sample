<?php

use yii\db\Migration;

class m171006_092747_create_table_diffs_title extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%diffs_title}}', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'CanonicalLinkElement1' => $this->text(),
            'Title1' => $this->text(),
            'MetaDescription1' => $this->text(),
            'MetaKeyword1' => $this->text(),
            'H11' => $this->text(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%diffs_title}}');
    }
}
