<?php

use yii\db\Migration;

class m171127_081730_update_newsletter_table extends Migration
{
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE Newsletter
            ADD firstName varchar(50) NOT NULL DEFAULT '' AFTER email,
            ADD lastName varchar(50) NOT NULL DEFAULT '' AFTER firstName,
            ADD profession int(11) NULL AFTER lastName,
            ADD userID int(11) NULL AFTER profession,
            ADD CONSTRAINT fk_Newsletter_User FOREIGN KEY (userID) REFERENCES User (id) ON DELETE CASCADE ON UPDATE CASCADE;
        ");
    }

    public function safeDown()
    {
        echo "m171127_081730_update_newsletter_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171127_081730_update_newsletter_table cannot be reverted.\n";

        return false;
    }
    */
}
