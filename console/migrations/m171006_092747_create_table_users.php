<?php

use yii\db\Migration;

class m171006_092747_create_table_users extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%users}}', [
            'uid' => $this->integer(10)->unsigned()->notNull()->comment('Primary Key: Unique user ID.')->append('PRIMARY KEY'),
            'name' => $this->string(60)->notNull()->defaultValue('')->comment('Unique user name.'),
            'pass' => $this->string(128)->notNull()->defaultValue('')->comment('Userâ€™s password (hashed).'),
            'mail' => $this->string(254)->defaultValue('')->comment('Userâ€™s e-mail address.'),
            'theme' => $this->string(255)->notNull()->defaultValue('')->comment('Userâ€™s default theme.'),
            'signature' => $this->string(255)->notNull()->defaultValue('')->comment('Userâ€™s signature.'),
            'signature_format' => $this->string(255)->comment('The filter_format.format of the signature.'),
            'created' => $this->integer(11)->notNull()->defaultValue('0')->comment('Timestamp for when user was created.'),
            'access' => $this->integer(11)->notNull()->defaultValue('0')->comment('Timestamp for previous time user accessed the site.'),
            'login' => $this->integer(11)->notNull()->defaultValue('0')->comment('Timestamp for userâ€™s last login.'),
            'status' => $this->smallInteger(4)->notNull()->defaultValue('0')->comment('Whether the user is active(1) or blocked(0).'),
            'timezone' => $this->string(32)->comment('Userâ€™s time zone.'),
            'language' => $this->string(12)->notNull()->defaultValue('')->comment('Userâ€™s default language.'),
            'picture' => $this->integer(11)->notNull()->defaultValue('0')->comment('Foreign key: file_managed.fid of userâ€™s picture.'),
            'init' => $this->string(254)->defaultValue('')->comment('E-mail address used for initial account creation.'),
            'data' => $this->binary()->comment('A serialized array of name value pairs that are related to the user. Any form values posted during user edit are stored and are loaded into the $user object during user_load(). Use of this field is discouraged and it will likely disappear in a future...'),
            'uuid' => $this->char(36)->notNull()->defaultValue('')->comment('The Universally Unique Identifier.'),
        ], $tableOptions);

        $this->createIndex('name', '{{%users}}', 'name', true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%users}}');
    }
}
