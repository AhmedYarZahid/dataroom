<?php

use yii\db\Migration;

/**
 * Class m180514_061213_alter_type_on_StaticPage
 */
class m180514_061213_alter_type_on_StaticPage extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE `StaticPage` CHANGE `type` `type` ENUM('other','legal_notice','terms')  CHARACTER SET utf8  COLLATE utf8_unicode_ci  NOT NULL  DEFAULT 'other';
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180514_061213_alter_type_on_StaticPage cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180514_061213_alter_type_on_StaticPage cannot be reverted.\n";

        return false;
    }
    */
}
