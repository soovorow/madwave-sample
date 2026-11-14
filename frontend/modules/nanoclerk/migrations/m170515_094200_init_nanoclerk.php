<?php

use yii\db\Migration;

/**
 * Migration for NanoClerk module
 *
 * How to use:
 * $ yii migrate --migrationPath=@frontend/modules/nanoclerk/migrations/init_nanockerk
 *
 */
class m170515_094200_init_nanoclerk extends Migration
{
    public function up()
    {
        $this->createTable('nc_visitor', [
            'id' => $this->primaryKey(),
            'email' => $this->string(),
            'session' => $this->string(),
            'ga' => $this->string(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->createTable('nc_interest', [
            'id' => $this->primaryKey(),
            'title' => $this->string(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->createTable('nc_item', [
            'id' => $this->primaryKey(),
            'title' => $this->string(),
            'type' => $this->integer(),
            'html' => $this->text(),
            'page' => $this->text(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->createTable('nc_visitor_interest', [
            'id' => $this->primaryKey(),
            'visitor_id' => $this->integer(),
            'item_id' => $this->integer(),
            'action' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->createTable('nc_item_interest', [
            'id' => $this->primaryKey(),
            'item_id' => $this->integer(),
            'interest_id' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->createTable('nc_visitor_interest', [
            'id' => $this->primaryKey(),
            'visitor_id' => $this->integer(),
            'interest_id' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
    }

    public function down()
    {
        $this->dropTable('nc_visitor');

        $this->dropTable('nc_interest');

        $this->dropTable('nc_item');

        $this->dropTable('nc_visitor_interest');

        $this->dropTable('nc_item_interest');

        $this->dropTable('nc_visitor_interest');

        return false;
    }
}
