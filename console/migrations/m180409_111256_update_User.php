<?php

use yii\db\Migration;

/**
 * Class m180409_111256_update_User
 */
class m180409_111256_update_User extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE User
            ADD isMailingContact tinyint(1) NOT NULL DEFAULT 0 AFTER tempEmail;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180409_111256_update_User cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180409_111256_update_User cannot be reverted.\n";

        return false;
    }
    */
}
