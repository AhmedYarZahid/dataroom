<?php

use yii\db\Migration;

/**
 * Class m220809_133052_add_buyer_profile_columns_in_users_table
 */
class m220809_133052_add_buyer_profile_columns_in_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('User', 'targetedSector', $this->string());
        $this->addColumn('User', 'targetedTurnover', $this->string());
        $this->addColumn('User', 'entranceTicket', $this->string());
        $this->addColumn('User', 'geographicalArea', $this->string());
        $this->addColumn('User', 'targetAmount', $this->string());
        $this->addColumn('User', 'effectiveMin', $this->string());
        $this->addColumn('User', 'effectiveMax', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('User', 'targetedSector');
        $this->dropColumn('User', 'targetedTurnover');
        $this->dropColumn('User', 'entranceTicket');
        $this->dropColumn('User', 'geographicalArea');
        $this->dropColumn('User', 'targetAmount');
        $this->dropColumn('User', 'effectiveMin');
        $this->dropColumn('User', 'effectiveMax');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220809_133052_add_buyer_profile_columns_in_users_table cannot be reverted.\n";

        return false;
    }
    */
}
