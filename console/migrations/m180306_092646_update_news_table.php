<?php

use yii\db\Migration;

class m180306_092646_update_news_table extends Migration
{
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE News
            ADD category enum('communications','media') NOT NULL AFTER image;

            UPDATE News SET category = 'communications';
        ");
    }

    public function safeDown()
    {
        echo "m180306_092646_update_news_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180306_092646_update_news_table cannot be reverted.\n";

        return false;
    }
    */
}
