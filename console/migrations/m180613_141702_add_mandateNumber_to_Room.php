<?php

use yii\db\Migration;

/**
 * Class m180613_141702_add_mandateNumber_to_Room
 */
class m180613_141702_add_mandateNumber_to_Room extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE `Room` ADD `mandateNumber` VARCHAR(30)  NULL  DEFAULT NULL  AFTER `id`;
            UPDATE Room SET mandateNumber = id;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180613_141702_add_mandateNumber_to_Room cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180613_141702_add_mandateNumber_to_Room cannot be reverted.\n";

        return false;
    }
    */
}
