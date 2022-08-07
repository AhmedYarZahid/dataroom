<?php

use yii\db\Migration;

class m180306_083516_update_room_table extends Migration
{
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE Room
            ADD section enum('companies','real_estate','syndicates','cv') NOT NULL AFTER status,
            CHANGE publicationDate publicationDate date NOT NULL,
            CHANGE expirationDate expirationDate date NOT NULL,
            CHANGE archivationDate archivationDate date NOT NULL;

            UPDATE Room SET section = 'companies';
        ");
    }

    public function safeDown()
    {
        echo "m180306_083516_update_room_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180306_083516_update_room_table cannot be reverted.\n";

        return false;
    }
    */
}
