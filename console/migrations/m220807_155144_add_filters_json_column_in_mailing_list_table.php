<?php

use yii\db\Migration;

/**
 * Class m220807_155144_add_filters_json_column_in_mailing_list_table
 */
class m220807_155144_add_filters_json_column_in_mailing_list_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('MailingList', 'filters_json', $this->text());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('MailingList', 'filters_json');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220807_155144_add_filters_json_column_in_mailing_list_table cannot be reverted.\n";

        return false;
    }
    */
}
