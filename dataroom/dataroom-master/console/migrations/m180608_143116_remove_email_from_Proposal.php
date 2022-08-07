<?php

use yii\db\Migration;

/**
 * Class m180608_143116_remove_email_from_Proposal
 */
class m180608_143116_remove_email_from_Proposal extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE `ProposalRealEstate` DROP `email`;
            ALTER TABLE `ProposalCoownership` DROP `email`;
         ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180608_143116_remove_email_from_Proposal cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180608_143116_remove_email_from_Proposal cannot be reverted.\n";

        return false;
    }
    */
}
