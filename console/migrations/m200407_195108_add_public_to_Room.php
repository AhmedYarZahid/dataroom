<?php

use yii\db\Migration;

/**
 * Class m200407_195108_add_public_to_Room
 */
class m200407_195108_add_public_to_Room extends Migration
{
    /**
     * {@inheritdoc}
     */
    // public function safeUp()
    // {
    //     $this->execute("

    //         ALTER TABLE `Room`

    //         ADD `public` TINYINT(1) NULL DEFAULT '0' AFTER `proposalsAllowed`;

    //     ");
    // }

    // /**
    //  * {@inheritdoc}
    //  */
    // public function safeDown()
    // {
    //     echo "m200407_195108_add_public_to_Room cannot be reverted.\n";

    //     return false;
    // }


    public function safeUp()
    {
        $this->addColumn('Room', 'public', $this->boolean()->defaultValue(0));
    }

    public function safeDown()
    {
        $this->dropColumn('Room', 'public');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200407_195108_add_public_to_Room cannot be reverted.\n";

        return false;
    }
    */
}
