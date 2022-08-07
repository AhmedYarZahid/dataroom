<?php

use yii\db\Migration;

/**
 * Class m180409_075135_update_MailingCampaign
 */
class m180409_075135_update_MailingCampaign extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE `MailingCampaign`
            ADD sentDate datetime NULL;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180409_075135_update_MailingCampaign cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180409_075135_update_MailingCampaign cannot be reverted.\n";

        return false;
    }
    */
}
